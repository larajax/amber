<?php
namespace Tests\Feature;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaveTest extends TestCase
{
    use RefreshDatabase;

    public function test_on_update_saves_the_user(): void
    {
        $group = UserGroup::factory()->create(['code' => 'admin']);
        $user = User::factory()->create(['name' => 'Original']);

        $resp = $this->withHeaders([
            'X-AJAX-HANDLER' => 'onUpdate',
            'X-Requested-With' => 'XMLHttpRequest',
        ])->post("/users/{$user->id}/edit", [
            'name' => 'Renamed Person',
            'email' => $user->email,
            'notes' => 'changed via ajax',
            'primary_group' => $group->id,
            'groups' => [$group->id],
        ]);

        $resp->assertOk();
        $user->refresh();
        $this->assertSame('Renamed Person', $user->name);
        $this->assertSame('changed via ajax', $user->notes);
        $this->assertSame($group->id, $user->primary_group_id);
        $this->assertSame(1, $user->groups()->count());
    }

    public function test_on_update_validates_required_name(): void
    {
        $user = User::factory()->create();
        $resp = $this->withHeaders([
            'X-AJAX-HANDLER' => 'onUpdate',
            'X-Requested-With' => 'XMLHttpRequest',
        ])->post("/users/{$user->id}/edit", ['name' => '', 'email' => $user->email]);

        $resp->assertStatus(422);
    }

    public function test_on_store_creates_a_user(): void
    {
        $group = UserGroup::factory()->create();
        $resp = $this->withHeaders([
            'X-AJAX-HANDLER' => 'onStore',
            'X-Requested-With' => 'XMLHttpRequest',
        ])->post('/users/create', [
            'name' => 'Brand New',
            'email' => 'brandnew@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'primary_group' => $group->id,
        ]);

        $resp->assertOk();
        $this->assertDatabaseHas('users', ['email' => 'brandnew@example.com', 'name' => 'Brand New']);
    }
}
