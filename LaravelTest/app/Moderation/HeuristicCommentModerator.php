<?php

namespace App\Moderation;

use App\Models\Comment;
use Illuminate\Support\Str;

/**
 * Rule based comment moderator. It needs no API key and no network, so it is
 * the default driver and the safety net the AI moderator degrades to.
 */
class HeuristicCommentModerator implements CommentModerator
{
    /**
     * How many adjacent words are joined before matching, which is what makes
     * "f.u.c.k" match the blocklist entry "fuck".
     */
    private const MAX_JOINED_WORDS = 4;

    public function name(): string
    {
        return 'heuristic';
    }

    public function moderate(Comment $comment): ModerationResult
    {
        $config = config('moderation.heuristic');

        $raw = (string) $comment->body;

        // Zero width and bidi control characters are used to smuggle blocklist
        // words past a naive filter, so they are stripped before matching.
        $visible = $this->strip($raw);
        $normalized = $this->normalize($visible);

        if (Str::length($normalized) < $config['min_length']) {
            return ModerationResult::rejected(
                'The comment is too short to be meaningful.',
                categories: ['too_short'],
                source: $this->name(),
            );
        }

        if (Str::length($raw) > $config['max_length']) {
            return ModerationResult::rejected(
                'The comment exceeds the maximum length.',
                categories: ['too_long'],
                source: $this->name(),
            );
        }

        $linkCount = $this->countLinks($raw);

        if ($linkCount > $config['max_links']) {
            return ModerationResult::rejected(
                'The comment contains too many links.',
                confidence: 0.9,
                categories: ['link_spam'],
                source: $this->name(),
            );
        }

        $matched = $this->matchBlocklist($normalized, $config['blocklist']);

        if ($matched !== []) {
            return ModerationResult::rejected(
                'The comment contains blocked language: '.implode(', ', $matched).'.',
                categories: ['abuse'],
                source: $this->name(),
            );
        }

        $ratio = $this->capsRatio($visible);

        if ($ratio > $config['max_caps_ratio'] && Str::length(preg_replace('/\s+/u', '', $visible) ?? '') >= 12) {
            return ModerationResult::rejected(
                'The comment is mostly capital letters.',
                confidence: 0.8,
                categories: ['shouting'],
                source: $this->name(),
            );
        }

        if (preg_match('/(.)\1{'.max(0, $config['max_repeated_chars'] - 1).',}/u', $normalized) === 1) {
            return ModerationResult::rejected(
                'The comment contains an implausible run of repeated characters.',
                confidence: 0.8,
                categories: ['gibberish'],
                source: $this->name(),
            );
        }

        if ($this->isDuplicate($comment, $config['duplicate_window_minutes'])) {
            return ModerationResult::rejected(
                'The same comment was posted moments ago.',
                confidence: 0.85,
                categories: ['duplicate'],
                source: $this->name(),
            );
        }

        return ModerationResult::approved(source: $this->name());
    }

    /**
     * Remove zero width, soft hyphen and bidi control characters, which are
     * used to smuggle blocked words past a naive filter, while keeping the
     * original casing intact.
     */
    private function strip(string $body): string
    {
        $body = (string) preg_replace('/[\x{200B}-\x{200D}\x{FEFF}\x{00AD}]/u', '', $body);

        return (string) preg_replace('/[\x{202A}-\x{202E}\x{2066}-\x{2069}]/u', '', $body);
    }

    /**
     * Lowercase the text and undo the most common leetspeak substitutions so
     * one blocklist entry covers its obvious disguises.
     */
    private function normalize(string $body): string
    {
        return strtr(Str::lower($body), [
            '0' => 'o',
            '1' => 'i',
            '3' => 'e',
            '4' => 'a',
            '5' => 's',
            '7' => 't',
            '@' => 'a',
            '$' => 's',
        ]);
    }

    /**
     * Match whole words and short runs of adjacent words against the
     * blocklist, so "shit", "f u c k" and "f.u.c.k" all match the single
     * entry "fuck" while ordinary words that merely contain a blocked term
     * ("spice") are left alone.
     *
     * @param  array<int, string>  $blocklist
     * @return array<int, string>
     */
    private function matchBlocklist(string $normalized, array $blocklist): array
    {
        $candidates = $this->wordCandidates($normalized);

        $matched = [];

        foreach ($blocklist as $term) {
            $term = $this->squeeze(Str::lower(trim((string) $term)));

            if ($term !== '' && isset($candidates[$term])) {
                $matched[] = $term;
            }
        }

        return array_values(array_unique($matched));
    }

    /**
     * Every single word plus every run of up to four adjacent words joined
     * without their separator, as a lookup set.
     *
     * @return array<string, true>
     */
    private function wordCandidates(string $normalized): array
    {
        $words = preg_split('/[^\p{L}\p{N}]+/u', $normalized, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        $candidates = [];
        $count = count($words);

        for ($start = 0; $start < $count; $start++) {
            $buffer = '';

            for ($offset = 0; $offset < self::MAX_JOINED_WORDS && $start + $offset < $count; $offset++) {
                $buffer .= $words[$start + $offset];
                $candidates[$buffer] = true;
            }
        }

        return $candidates;
    }

    private function squeeze(string $value): string
    {
        return (string) preg_replace('/[^\p{L}\p{N}]+/u', '', $value);
    }

    private function countLinks(string $body): int
    {
        return (int) preg_match_all('~(?:https?://|www\.)\S+|\b[\w-]+\.(?:com|net|org|io|gg|xyz|top|ru|de|tk|link)\b~iu', $body);
    }

    private function capsRatio(string $cased): float
    {
        $letters = preg_replace('/[^\p{L}]/u', '', $cased) ?? '';

        if ($letters === '') {
            return 0.0;
        }

        $upper = preg_replace('/[^\p{Lu}]/u', '', $letters) ?? '';

        return mb_strlen($upper) / mb_strlen($letters);
    }

    private function isDuplicate(Comment $comment, int $windowMinutes): bool
    {
        if ($windowMinutes <= 0 || $comment->user_id === null) {
            return false;
        }

        $since = now()->subMinutes($windowMinutes);

        return $comment->newQuery()
            ->where('user_id', $comment->user_id)
            ->whereKeyNot($comment->getKey())
            ->where('created_at', '>=', $since)
            ->where('body', $comment->body)
            ->exists();
    }
}
