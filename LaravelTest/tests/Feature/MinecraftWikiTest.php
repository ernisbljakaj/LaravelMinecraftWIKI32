<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Favorite;
use App\Models\Server;
use App\Models\Tag;
use App\Models\User;
use App\Models\WikiPage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MinecraftWikiTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_lists_only_approved_servers(): void
    {
        $approved = Server::factory()->approved()->create(['name' => 'Craftland']);
        $unapproved = Server::factory()->unapproved()->create(['name' => 'Secret Server']);

        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('Craftland')
            ->assertDontSee('Secret Server');

        $this->assertDatabaseHas('servers', ['id' => $unapproved->id, 'approved' => false]);
    }

    public function test_server_list_can_be_filtered_by_tag(): void
    {
        $survival = Tag::create(['name' => 'Survival', 'slug' => 'survival']);
        $pvp = Tag::create(['name' => 'PvP', 'slug' => 'pvp']);

        $survivalServer = Server::factory()->approved()->notFeatured()->create();
        $survivalServer->tags()->attach($survival);

        $pvpServer = Server::factory()->approved()->notFeatured()->create();
        $pvpServer->tags()->attach($pvp);

        $response = $this->get(route('servers.index', ['tag' => 'survival']));

        $response->assertOk()
            ->assertSee($survivalServer->name)
            ->assertDontSee($pvpServer->name);
    }

    public function test_server_detail_is_not_accessible_for_unapproved_servers(): void
    {
        $unapproved = Server::factory()->unapproved()->create();

        $this->get(route('servers.show', $unapproved))->assertNotFound();
    }

    public function test_wiki_index_is_filterable_by_category(): void
    {
        $redstone = WikiPage::factory()->approved()->create(['title' => 'Redstone Guide', 'category' => 'Redstone']);
        $farm = WikiPage::factory()->approved()->create(['title' => 'Farming Guide', 'category' => 'Farming']);

        $response = $this->get(route('wiki.index', ['category' => 'Redstone']));

        $response->assertOk()
            ->assertSee('Redstone Guide')
            ->assertDontSee('Farming Guide');
    }

    public function test_wiki_detail_shows_content_and_comments(): void
    {
        $page = WikiPage::factory()->approved()->create(['title' => 'Build an iron farm']);
        $user = User::factory()->create();
        Comment::create([
            'user_id' => $user->id,
            'commentable_type' => WikiPage::class,
            'commentable_id' => $page->id,
            'body' => 'Great guide!',
        ]);

        $this->get(route('wiki.show', $page))
            ->assertOk()
            ->assertSee('Build an iron farm')
            ->assertSee('Great guide!');
    }

    public function test_unapproved_wiki_page_is_not_accessible(): void
    {
        $page = WikiPage::factory()->create(['approved' => false]);

        $this->get(route('wiki.show', $page))->assertNotFound();
    }

    public function test_registration_creates_user_as_member_role(): void
    {
        $response = $this->post('/register', [
            'name' => 'Alex',
            'email' => 'alex@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('home'));

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'alex@example.com',
            'role' => 'user',
        ]);
    }

    public function test_user_can_toggle_favorite_state(): void
    {
        $user = User::factory()->create();
        $server = Server::factory()->approved()->notFeatured()->create();

        $this->actingAs($user)
            ->post(route('servers.favorite', $server))
            ->assertRedirect();

        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'server_id' => $server->id,
        ]);

        $this->actingAs($user)
            ->post(route('servers.favorite', $server))
            ->assertRedirect();

        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'server_id' => $server->id,
        ]);
    }

    public function test_guests_are_redirected_from_favorites_page(): void
    {
        $this->get(route('favorites.index'))->assertRedirect(route('login'));
    }

    public function test_user_favorites_page_shows_favorited_servers(): void
    {
        $user = User::factory()->create();
        $server = Server::factory()->approved()->notFeatured()->create();
        Favorite::create(['user_id' => $user->id, 'server_id' => $server->id]);

        $this->actingAs($user)
            ->get(route('favorites.index'))
            ->assertOk()
            ->assertSee($server->name);
    }

    public function test_authenticated_user_can_comment_on_a_server(): void
    {
        $user = User::factory()->create();
        $server = Server::factory()->approved()->notFeatured()->create();

        $this->actingAs($user)->post(route('comments.store'), [
            'commentable_type' => Server::class,
            'commentable_id' => $server->id,
            'body' => 'This server is amazing!',
        ])->assertRedirect();

        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'commentable_type' => Server::class,
            'commentable_id' => $server->id,
            'body' => 'This server is amazing!',
        ]);
    }

    public function test_guest_cannot_submit_a_comment(): void
    {
        $server = Server::factory()->approved()->notFeatured()->create();

        $this->post(route('comments.store'), [
            'commentable_type' => Server::class,
            'commentable_id' => $server->id,
            'body' => 'Hello',
        ])->assertRedirect(route('login'));
    }

    public function test_only_owner_or_admin_can_delete_a_comment(): void
    {
        $server = Server::factory()->approved()->notFeatured()->create();
        $owner = User::factory()->create();
        $other = User::factory()->create();

        $comment = Comment::create([
            'user_id' => $owner->id,
            'commentable_type' => Server::class,
            'commentable_id' => $server->id,
            'body' => 'My comment',
        ]);

        $this->actingAs($other)->delete(route('comments.destroy', $comment))->assertForbidden();
        $this->assertDatabaseHas('comments', ['id' => $comment->id]);

        $this->actingAs($owner)->delete(route('comments.destroy', $comment))->assertRedirect();
        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }

    public function test_authenticated_user_can_submit_a_server_for_review(): void
    {
        $user = User::factory()->create();
        $tag = Tag::create(['name' => 'Survival', 'slug' => 'survival']);

        $response = $this->actingAs($user)->post(route('servers.store'), [
            'name' => 'My Server',
            'ip' => 'play.myserver.com',
            'version' => '1.21',
            'mode' => 'Survival',
            'description' => 'A great server.',
            'tags' => [$tag->id],
        ]);

        $this->assertDatabaseHas('servers', [
            'name' => 'My Server',
            'approved' => false,
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('server_tag', ['tag_id' => $tag->id]);
    }
}
