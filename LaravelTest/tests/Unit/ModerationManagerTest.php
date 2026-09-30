<?php

namespace Tests\Unit;

use App\Models\Comment;
use App\Moderation\ChainedCommentModerator;
use App\Moderation\CommentModerator;
use App\Moderation\HeuristicCommentModerator;
use App\Moderation\ModerationManager;
use App\Moderation\ModerationResult;
use App\Moderation\ModerationStatus;
use App\Moderation\NullCommentModerator;
use Tests\TestCase;

class ModerationManagerTest extends TestCase
{
    public function test_it_uses_the_heuristic_moderator_when_no_ai_key_is_configured(): void
    {
        config()->set('moderation.driver', 'auto');
        config()->set('moderation.ai.key', null);

        $this->assertInstanceOf(HeuristicCommentModerator::class, (new ModerationManager)->driver());
    }

    public function test_it_uses_the_heuristic_moderator_when_asked_explicitly(): void
    {
        config()->set('moderation.driver', 'heuristic');
        config()->set('moderation.ai.key', 'sk-test');

        $this->assertInstanceOf(HeuristicCommentModerator::class, (new ModerationManager)->driver());
    }

    public function test_auto_driver_chains_the_ai_moderator_when_a_key_is_configured(): void
    {
        config()->set('moderation.driver', 'auto');
        config()->set('moderation.ai.key', 'sk-test');

        $driver = (new ModerationManager)->driver();

        $this->assertInstanceOf(ChainedCommentModerator::class, $driver);
        $this->assertSame('heuristic+ai', $driver->name());
    }

    public function test_ai_driver_falls_back_to_heuristic_without_a_key(): void
    {
        config()->set('moderation.driver', 'ai');
        config()->set('moderation.ai.key', null);

        $this->assertInstanceOf(HeuristicCommentModerator::class, (new ModerationManager)->driver());
    }

    public function test_off_driver_disables_moderation(): void
    {
        config()->set('moderation.driver', 'off');

        $this->assertInstanceOf(NullCommentModerator::class, (new ModerationManager)->driver());
    }

    public function test_the_chain_stops_at_the_first_moderator_that_does_not_approve(): void
    {
        $first = $this->fakeModerator('first', ModerationStatus::Approved);
        $second = $this->fakeModerator('second', ModerationStatus::Rejected, 'abuse');
        $third = $this->fakeModerator('third', ModerationStatus::Approved);

        $result = (new ChainedCommentModerator([$first, $second, $third]))->moderate($this->comment());

        $this->assertSame(ModerationStatus::Rejected, $result->status);
        $this->assertSame('second', $result->source);
        $this->assertSame(0, $third->calls);
    }

    public function test_the_chain_uses_the_last_verdict_when_every_moderator_approves(): void
    {
        $chain = new ChainedCommentModerator([
            $this->fakeModerator('first', ModerationStatus::Approved),
            $this->fakeModerator('second', ModerationStatus::Approved),
        ]);

        $this->assertSame('second', $chain->moderate($this->comment())->source);
    }

    public function test_the_container_resolves_a_single_moderator_instance(): void
    {
        config()->set('moderation.driver', 'heuristic');

        $this->assertSame(
            $this->app->make(CommentModerator::class),
            $this->app->make(CommentModerator::class),
        );
    }

    private function fakeModerator(string $name, ModerationStatus $status, string $reason = ''): CommentModerator
    {
        return new class($name, $status, $reason) implements CommentModerator
        {
            public int $calls = 0;

            public function __construct(
                private string $name,
                private ModerationStatus $status,
                private string $reason = '',
            ) {}

            public function name(): string
            {
                return $this->name;
            }

            public function moderate(Comment $comment): ModerationResult
            {
                $this->calls++;

                return new ModerationResult($this->status, $this->reason, 1.0, source: $this->name);
            }
        };
    }

    private function comment(): Comment
    {
        return new Comment(['body' => 'A perfectly ordinary comment.']);
    }
}
