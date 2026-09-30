<?php

namespace Tests\Unit;

use App\Models\Comment;
use App\Moderation\HeuristicCommentModerator;
use App\Moderation\ModerationResult;
use App\Moderation\ModerationStatus;
use App\Moderation\NullCommentModerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HeuristicCommentModeratorTest extends TestCase
{
    use RefreshDatabase;

    private HeuristicCommentModerator $moderator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->moderator = new HeuristicCommentModerator;
    }

    public function test_it_publishes_an_ordinary_comment(): void
    {
        $result = $this->moderate('Does anyone know a good iron farm design for 1.21?');

        $this->assertSame(ModerationStatus::Approved, $result->status);
        $this->assertSame('heuristic', $result->source);
    }

    public function test_it_rejects_a_comment_that_is_too_short(): void
    {
        $result = $this->moderate('ok');

        $this->assertSame(ModerationStatus::Rejected, $result->status);
        $this->assertContains('too_short', $result->categories);
    }

    public function test_it_rejects_blocked_language(): void
    {
        $result = $this->moderate('This server is a piece of shit and so are the admins.');

        $this->assertSame(ModerationStatus::Rejected, $result->status);
        $this->assertContains('abuse', $result->categories);
    }

    public function test_it_rejects_leetspeak_disguised_blocked_language(): void
    {
        $this->assertSame(ModerationStatus::Rejected, $this->moderate('you are a f4ggot here')->status);
    }

    public function test_it_rejects_blocked_language_split_by_separators(): void
    {
        $this->assertSame(ModerationStatus::Rejected, $this->moderate('what a f.u.c.k server')->status);
    }

    public function test_it_rejects_blocked_language_smuggled_past_zero_width_characters(): void
    {
        $this->assertSame(ModerationStatus::Rejected, $this->moderate("this sh\u{200B}it server is awful")->status);
    }

    public function test_it_allows_a_single_link(): void
    {
        $result = $this->moderate('Great guide, I used https://example.com/wiki/iron-farm for this.');

        $this->assertSame(ModerationStatus::Approved, $result->status);
    }

    public function test_it_rejects_link_spam(): void
    {
        $result = $this->moderate('buy now at cheap-craft.top and free-nether.de and also best-skin.net');

        $this->assertSame(ModerationStatus::Rejected, $result->status);
        $this->assertContains('link_spam', $result->categories);
    }

    public function test_it_rejects_shouting(): void
    {
        $result = $this->moderate('THIS SERVER IS THE ABSOLUTE WORST EVER BUILT');

        $this->assertSame(ModerationStatus::Rejected, $result->status);
        $this->assertContains('shouting', $result->categories);
    }

    public function test_it_allows_a_short_acronym_shout(): void
    {
        $result = $this->moderate('GG everyone, that PVP round was fun.');

        $this->assertSame(ModerationStatus::Approved, $result->status);
    }

    public function test_it_rejects_a_long_run_of_repeated_characters(): void
    {
        $result = $this->moderate('aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa');

        $this->assertSame(ModerationStatus::Rejected, $result->status);
        $this->assertContains('gibberish', $result->categories);
    }

    public function test_it_rejects_a_comment_the_same_user_just_posted(): void
    {
        $first = Comment::factory()->create(['body' => 'Same text, posted twice in a row.']);

        $second = Comment::factory()->create([
            'user_id' => $first->user_id,
            'body' => 'Same text, posted twice in a row.',
        ]);

        $result = $this->moderator->moderate($second);

        $this->assertSame(ModerationStatus::Rejected, $result->status);
        $this->assertContains('duplicate', $result->categories);
    }

    public function test_it_allows_the_same_text_from_a_different_user(): void
    {
        $first = Comment::factory()->create(['body' => 'Nice redstone contraption you have there.']);

        $second = Comment::factory()->create([
            'body' => 'Nice redstone contraption you have there.',
        ]);

        $this->assertSame(ModerationStatus::Approved, $this->moderator->moderate($second)->status);
    }

    public function test_disabled_moderator_publishes_everything(): void
    {
        $result = (new NullCommentModerator)->moderate(Comment::factory()->make(['body' => 'you are a bitch']));

        $this->assertSame(ModerationStatus::Approved, $result->status);
        $this->assertSame('disabled', $result->source);
    }

    private function moderate(string $body): ModerationResult
    {
        return $this->moderator->moderate(Comment::factory()->make(['body' => $body]));
    }
}
