<?php

namespace App\Moderation;

/**
 * Turns the `moderation.driver` config value into the moderator that will
 * actually review comments.
 *
 * The AI driver needs an API key. Rather than failing a comment submission
 * when one is missing, the manager quietly falls back to the heuristic
 * moderator so the site always has a working safety net.
 */
class ModerationManager
{
    public function driver(): CommentModerator
    {
        $heuristic = new HeuristicCommentModerator;

        return match (config('moderation.driver')) {
            'off' => new NullCommentModerator,
            'heuristic' => $heuristic,
            'ai' => $this->withAi($heuristic),
            default => $this->ai()->isConfigured()
                ? $this->withAi($heuristic)
                : $heuristic,
        };
    }

    /**
     * The heuristic runs first as a free pre-filter, so only comments that
     * already look clean are paid for.
     */
    private function withAi(HeuristicCommentModerator $heuristic): CommentModerator
    {
        $ai = $this->ai();

        return $ai->isConfigured()
            ? new ChainedCommentModerator([$heuristic, $ai])
            : $heuristic;
    }

    private function ai(): AiCommentModerator
    {
        return app(AiCommentModerator::class);
    }
}
