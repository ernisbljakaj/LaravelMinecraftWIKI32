<?php

namespace Tests\Feature;

use App\Jobs\ModerateComment;
use App\Models\Comment;
use App\Models\Server;
use App\Models\User;
use App\Models\WikiPage;
use App\Moderation\CommentModerator;
use App\Moderation\ModerationStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class CommentModerationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('moderation.driver', 'heuristic');
        config()->set('moderation.queue', false);
    }

    public function test_an_ordinary_comment_is_published_immediately(): void
    {
        $user = User::factory()->create();
        $server = Server::factory()->approved()->create();

        $this->actingAs($user)->post(route('comments.store'), [
            'commentable_type' => Server::class,
            'commentable_id' => $server->id,
            'body' => 'What a great server, the community is lovely!',
        ])->assertRedirect();

        $comment = Comment::sole();

        $this->assertTrue($comment->isPublished());
        $this->assertSame(ModerationStatus::Approved, $comment->moderation_status);
        $this->assertSame('heuristic', $comment->moderation_source);
        $this->assertNotNull($comment->moderated_at);
    }

    public function test_abusive_comment_is_stored_but_never_published(): void
    {
        $user = User::factory()->create();
        $server = Server::factory()->approved()->create();

        $this->actingAs($user)->post(route('comments.store'), [
            'commentable_type' => Server::class,
            'commentable_id' => $server->id,
            'body' => 'This server is complete shit and the owner is a dick.',
        ])->assertRedirect();

        $comment = Comment::sole();

        $this->assertFalse($comment->isPublished());
        $this->assertSame(ModerationStatus::Rejected, $comment->moderation_status);
        $this->assertStringContainsString('shit', $comment->moderation_reason);
    }

    public function test_a_rejected_comment_is_not_visible_on_the_page(): void
    {
        $server = Server::factory()->approved()->create();
        Comment::factory()->on($server)->rejected('Blocked language.')->create(['body' => 'A hidden insult']);
        Comment::factory()->on($server)->create(['body' => 'A perfectly fine comment']);

        $this->get(route('servers.show', $server))
            ->assertOk()
            ->assertSee('A perfectly fine comment')
            ->assertDontSee('A hidden insult');
    }

    public function test_admins_can_see_comments_hidden_by_moderation(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $server = Server::factory()->approved()->create();
        Comment::factory()->on($server)->rejected('Blocked language.')->create(['body' => 'A hidden insult']);

        $this->actingAs($admin)->get(route('servers.show', $server))
            ->assertOk()
            ->assertSee('A hidden insult')
            ->assertSee('Rejected')
            ->assertSee('Blocked language.');
    }

    public function test_guests_never_see_hidden_comments(): void
    {
        $server = Server::factory()->approved()->create();
        Comment::factory()->on($server)->pending()->create(['body' => 'An unpublished thought']);

        $this->get(route('servers.show', $server))
            ->assertOk()
            ->assertDontSee('An unpublished thought');
    }

    public function test_the_redirect_message_reflects_the_verdict(): void
    {
        $user = User::factory()->create();
        $server = Server::factory()->approved()->create();

        $this->actingAs($user)->post(route('comments.store'), [
            'commentable_type' => Server::class,
            'commentable_id' => $server->id,
            'body' => 'Thanks for the detailed redstone guide, very useful.',
        ])->assertSessionHas('status', 'Comment added.');

        $this->actingAs($user)->post(route('comments.store'), [
            'commentable_type' => Server::class,
            'commentable_id' => $server->id,
            'body' => 'The admin of this server is a massive dick.',
        ])->assertSessionHas('status', 'Your comment was not published. Please review the community guidelines.');
    }

    public function test_comments_cannot_be_posted_on_an_unapproved_server(): void
    {
        $user = User::factory()->create();
        $server = Server::factory()->unapproved()->create();

        $this->actingAs($user)->post(route('comments.store'), [
            'commentable_type' => Server::class,
            'commentable_id' => $server->id,
            'body' => 'Trying to comment on a hidden page.',
        ])->assertNotFound();

        $this->assertDatabaseCount('comments', 0);
    }

    public function test_comments_cannot_be_posted_on_an_unapproved_wiki_page(): void
    {
        $user = User::factory()->create();
        $page = WikiPage::factory()->unapproved()->create();

        $this->actingAs($user)->post(route('comments.store'), [
            'commentable_type' => WikiPage::class,
            'commentable_id' => $page->id,
            'body' => 'Trying to comment on a hidden page.',
        ])->assertNotFound();

        $this->assertDatabaseCount('comments', 0);
    }

    public function test_a_comment_body_shorter_than_the_minimum_is_rejected_by_validation(): void
    {
        $user = User::factory()->create();
        $server = Server::factory()->approved()->create();

        $this->actingAs($user)->post(route('comments.store'), [
            'commentable_type' => Server::class,
            'commentable_id' => $server->id,
            'body' => 'ok',
        ])->assertSessionHasErrors('body');

        $this->assertDatabaseCount('comments', 0);
    }

    public function test_an_unsupported_commentable_type_is_rejected(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('comments.store'), [
            'commentable_type' => User::class,
            'commentable_id' => $user->id,
            'body' => 'Trying to attach a comment to something else.',
        ])->assertSessionHasErrors('commentable_type');

        $this->assertDatabaseCount('comments', 0);
    }

    public function test_submitting_many_comments_at_once_is_rate_limited(): void
    {
        config()->set('moderation.quota.per_minute', 3);

        $user = User::factory()->create();
        $server = Server::factory()->approved()->create();

        for ($i = 0; $i < 3; $i++) {
            $this->actingAs($user)->post(route('comments.store'), [
                'commentable_type' => Server::class,
                'commentable_id' => $server->id,
                'body' => "Flooding the page number {$i}.",
            ])->assertRedirect();
        }

        $this->actingAs($user)->post(route('comments.store'), [
            'commentable_type' => Server::class,
            'commentable_id' => $server->id,
            'body' => 'One comment too many.',
        ])->assertStatus(429);

        $this->assertDatabaseCount('comments', 3);
    }

    public function test_moderation_can_run_on_the_queue(): void
    {
        config()->set('moderation.queue', true);

        Queue::fake();

        $user = User::factory()->create();
        $server = Server::factory()->approved()->create();

        $this->actingAs($user)->post(route('comments.store'), [
            'commentable_type' => Server::class,
            'commentable_id' => $server->id,
            'body' => 'A perfectly reasonable comment.',
        ])->assertRedirect();

        Queue::assertPushed(ModerateComment::class);

        $comment = Comment::sole();

        $this->assertSame(ModerationStatus::Pending, $comment->moderation_status);
        $this->assertFalse($comment->isPublished());
    }

    public function test_moderation_runs_inline_when_queuing_is_disabled(): void
    {
        config()->set('moderation.queue', false);

        $user = User::factory()->create();
        $server = Server::factory()->approved()->create();

        $this->actingAs($user)->post(route('comments.store'), [
            'commentable_type' => Server::class,
            'commentable_id' => $server->id,
            'body' => 'A perfectly reasonable comment.',
        ])->assertRedirect();

        $this->assertTrue(Comment::sole()->isPublished());
    }

    public function test_the_ai_moderator_approves_a_clean_comment(): void
    {
        config()->set('moderation.driver', 'ai');
        config()->set('moderation.ai.key', 'sk-test-key');

        Http::fake([
            '*/chat/completions' => Http::response([
                'choices' => [['message' => ['content' => json_encode([
                    'verdict' => 'approved',
                    'confidence' => 0.96,
                    'reason' => 'Ordinary question about redstone.',
                    'categories' => [],
                ])]]],
            ]),
        ]);

        $user = User::factory()->create();
        $server = Server::factory()->approved()->create();

        $this->actingAs($user)->post(route('comments.store'), [
            'commentable_type' => Server::class,
            'commentable_id' => $server->id,
            'body' => 'How do I make a hopper array that sorts both?',
        ])->assertRedirect();

        $comment = Comment::sole();

        $this->assertTrue($comment->isPublished());
        $this->assertSame('ai', $comment->moderation_source);
        $this->assertSame(0.96, $comment->moderation_confidence);
    }

    public function test_the_ai_moderator_rejects_spam(): void
    {
        $this->fakeAiVerdict('rejected', 0.98, 'Pure advertising spam.', ['spam']);

        $user = User::factory()->create();
        $server = Server::factory()->approved()->create();

        $this->actingAs($user)->post(route('comments.store'), [
            'commentable_type' => Server::class,
            'commentable_id' => $server->id,
            'body' => 'Check out my store for the best deals you will ever see!',
        ])->assertRedirect();

        $comment = Comment::sole();

        $this->assertFalse($comment->isPublished());
        $this->assertSame(ModerationStatus::Rejected, $comment->moderation_status);
        $this->assertSame('Pure advertising spam.', $comment->moderation_reason);
    }

    public function test_an_unsure_ai_verdict_is_held_for_a_human_instead_of_rejecting(): void
    {
        $this->fakeAiVerdict('rejected', 0.4, 'Might be rude.', ['abuse']);

        $user = User::factory()->create();
        $server = Server::factory()->approved()->create();

        $this->actingAs($user)->post(route('comments.store'), [
            'commentable_type' => Server::class,
            'commentable_id' => $server->id,
            'body' => 'Honestly this server has some balance problems.',
        ])->assertRedirect();

        $comment = Comment::sole();

        $this->assertFalse($comment->isPublished());
        $this->assertSame(ModerationStatus::Pending, $comment->moderation_status);
    }

    public function test_an_unreachable_ai_never_rejects_a_comment(): void
    {
        config()->set('moderation.driver', 'ai');
        config()->set('moderation.ai.key', 'sk-test-key');

        Http::fake([
            '*' => Http::response('upstream exploded', 500),
        ]);

        $user = User::factory()->create();
        $server = Server::factory()->approved()->create();

        $this->actingAs($user)->post(route('comments.store'), [
            'commentable_type' => Server::class,
            'commentable_id' => $server->id,
            'body' => 'A completely innocent comment about builds.',
        ])->assertRedirect();

        $comment = Comment::sole();

        $this->assertFalse($comment->isPublished());
        $this->assertSame(ModerationStatus::Pending, $comment->moderation_status);
    }

    public function test_obvious_spam_never_reaches_the_ai(): void
    {
        $this->fakeAiVerdict('approved', 1.0, 'Looks fine.');

        $user = User::factory()->create();
        $server = Server::factory()->approved()->create();

        $this->actingAs($user)->post(route('comments.store'), [
            'commentable_type' => Server::class,
            'commentable_id' => $server->id,
            'body' => 'this server is a piece of shit and the owner is a dick.',
        ])->assertRedirect();

        $comment = Comment::sole();

        $this->assertSame('heuristic', $comment->moderation_source);
        $this->assertSame(ModerationStatus::Rejected, $comment->moderation_status);

        Http::assertNothingSent();
    }

    public function test_an_already_moderated_comment_is_not_moderated_again(): void
    {
        $comment = Comment::factory()->create([
            'moderation_source' => 'manual',
            'moderation_reason' => 'Published by hand.',
        ]);

        (new ModerateComment($comment->id))
            ->handle($this->app->make(CommentModerator::class));

        $comment->refresh();

        $this->assertSame('manual', $comment->moderation_source);
        $this->assertSame('Published by hand.', $comment->moderation_reason);
    }

    public function test_a_pending_comment_is_moderated_by_the_job(): void
    {
        $comment = Comment::factory()->pending()->create();

        (new ModerateComment($comment->id))
            ->handle($this->app->make(CommentModerator::class));

        $this->assertTrue($comment->fresh()->isPublished());
    }

    private function fakeAiVerdict(string $verdict, float $confidence, string $reason, array $categories = []): void
    {
        config()->set('moderation.driver', 'ai');
        config()->set('moderation.ai.key', 'sk-test-key');

        Http::fake([
            '*/chat/completions' => Http::response([
                'choices' => [['message' => ['content' => json_encode([
                    'verdict' => $verdict,
                    'confidence' => $confidence,
                    'reason' => $reason,
                    'categories' => $categories,
                ])]]],
            ]),
        ]);
    }
}
