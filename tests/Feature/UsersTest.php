<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsersTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_home_page_redirects_to_users(): void
    {
        $this->get('/')->assertRedirect('users');
    }

    public function test_the_users_list_renders(): void
    {
        User::factory()->count(3)->create();

        $this->get('users')
            ->assertOk()
            ->assertSee('Users');
    }

    public function test_the_structure_page_renders_the_flat_reorder_variant(): void
    {
        User::factory()->count(2)->create();

        $this->get('users/structure')
            ->assertOk()
            ->assertSee('list-cell-tree', false);
    }

    public function test_the_toolbar_renders_declarative_yaml_buttons(): void
    {
        User::factory()->create();

        $this->get('users')
            ->assertOk()
            ->assertSee('New User')
            ->assertSee('data-request="onDelete"', false)
            ->assertSee('data-request-confirm', false)
            ->assertSee('data-list-checked-trigger', false)
            ->assertSee('data-list-checked-request', false);
    }

    public function test_on_delete_removes_the_checked_users(): void
    {
        $users = User::factory()->count(3)->create();

        $resp = $this->withHeaders([
            'X-AJAX-HANDLER' => 'onDelete',
            'X-Requested-With' => 'XMLHttpRequest',
        ])->post('users', [
            'checked' => [$users[0]->id, $users[2]->id],
        ]);

        $resp->assertOk();
        $this->assertSame([$users[1]->id], User::pluck('id')->all());
    }

    public function test_the_edit_form_renders_for_a_user(): void
    {
        $group = UserGroup::factory()->create();
        $user = User::factory()->create(['primary_group_id' => $group->id]);

        $this->get("users/{$user->id}/edit")
            ->assertOk()
            ->assertSee($user->email);
    }

    public function test_deleting_a_user_from_its_edit_page_redirects_to_the_absolute_list_url(): void
    {
        $user = User::factory()->create();

        $response = $this->withHeaders([
            'X-AJAX-HANDLER' => 'onDestroy',
            'X-Requested-With' => 'XMLHttpRequest',
        ])->post("/users/{$user->id}/edit");

        $this->assertSame(route('users.index'), $response->json('__ajax.redirect'));
        $this->assertNull(User::find($user->id));
    }

    public function test_the_group_edit_page_posts_the_form_session_key(): void
    {
        $group = UserGroup::factory()->create(['code' => 'editors']);

        $html = $this->get("/user-groups/{$group->id}/edit")->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/<input type="hidden" name="_session_key" value="[^"]+"/', $html);
    }
}
