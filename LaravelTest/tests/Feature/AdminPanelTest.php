<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Server;
use App\Models\Tag;
use App\Models\User;
use App\Models\WikiPage;
use Filament\Auth\Pages\Login;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin, 'web')
            ->get('/admin')
            ->assertOk();
    }

    public function test_normal_user_cannot_access_admin_panel(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user, 'web')
            ->get('/admin')
            ->assertForbidden();
    }

    public function test_admin_can_list_servers_in_panel(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Server::factory()->count(2)->create(['approved' => false]);

        $this->actingAs($admin, 'web')
            ->get('/admin/servers')
            ->assertOk();
    }

    public function test_admin_can_list_wiki_pages_in_panel(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        WikiPage::factory()->count(2)->create();

        $this->actingAs($admin, 'web')
            ->get('/admin/wiki-pages')
            ->assertOk();
    }

    public function test_admin_can_list_tags_in_panel(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Tag::factory()->count(2)->create();

        $this->actingAs($admin, 'web')
            ->get('/admin/tags')
            ->assertOk();
    }

    public function test_admin_can_list_comments_in_panel(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $server = Server::factory()->approved()->notFeatured()->create();
        Comment::factory()->create([
            'commentable_type' => Server::class,
            'commentable_id' => $server->id,
        ]);

        $this->actingAs($admin, 'web')
            ->get('/admin/comments')
            ->assertOk();
    }

    public function test_admin_can_open_edit_page_of_a_server(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $server = Server::factory()->unapproved()->notFeatured()->create();

        $this->actingAs($admin, 'web')
            ->get("/admin/servers/{$server->id}/edit")
            ->assertOk();
    }

    public function test_admin_user_can_sign_in_to_panel(): void
    {
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => 'password',
            'role' => 'admin',
        ]);

        Livewire::test(Login::class)
            ->fillForm([
                'email' => 'admin@example.com',
                'password' => 'password',
            ])
            ->call('authenticate')
            ->assertHasNoFormErrors();

        $this->assertAuthenticated();
    }

    public function test_normal_user_cannot_sign_in_to_panel(): void
    {
        User::factory()->create([
            'name' => 'Steve',
            'email' => 'steve@example.com',
            'password' => 'password',
            'role' => 'user',
        ]);

        Livewire::test(Login::class)
            ->fillForm([
                'email' => 'steve@example.com',
                'password' => 'password',
            ])
            ->call('authenticate')
            ->assertHasFormErrors(['email']);

        $this->assertGuest();
    }
}
