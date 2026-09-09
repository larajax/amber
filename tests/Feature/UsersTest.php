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

    public function test_the_edit_form_renders_for_a_user(): void
    {
        $group = UserGroup::factory()->create();
        $user = User::factory()->create(['primary_group_id' => $group->id]);

        $this->get("users/{$user->id}/edit")
            ->assertOk()
            ->assertSee($user->email);
    }
}
