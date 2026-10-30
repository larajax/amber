<?php

namespace Tests\Feature\Showcase;

use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class GroupsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ajax posts an AJAX request for the given handler to a groups page.
     *
     * @param  array<string, mixed>  $data
     */
    protected function ajax(string $handler, array $data = [], string $url = 'groups'): TestResponse
    {
        return $this->withHeaders([
            'X-AJAX-HANDLER' => $handler,
            'X-Requested-With' => 'XMLHttpRequest',
        ])->post($url, $data);
    }

    public function test_the_list_shows_each_group_with_its_member_count_and_opens_it_in_a_popup(): void
    {
        $group = UserGroup::factory()->create(['name' => 'Editors', 'code' => 'editors']);
        $group->users()->attach(User::factory()->count(2)->create());

        $html = $this->get('groups')->assertOk()->getContent();

        $this->assertStringContainsString('Editors', $html);
        $this->assertMatchesRegularExpression('/list-cell-name-users_count list-cell-type-number\s+">\s+2\s+</', $html);
        $this->assertStringContainsString("onclick=\"jax.listOnLoadForm(&#039;{$group->id}&#039;)\"", $html);
        $this->assertStringContainsString('href="'.url('groups/create').'"', $html);
    }

    public function test_on_load_popup_form_renders_the_group_form(): void
    {
        $group = UserGroup::factory()->create(['name' => 'Editors', 'code' => 'editors']);
        $group->users()->attach(User::factory()->create(['name' => 'Ada Writer']));

        $content = $this->ajax('onLoadPopupForm', ['form_record_id' => $group->id])->assertOk()->json('result');

        $this->assertStringContainsString('Edit Group', $content);
        $this->assertStringContainsString('data-request="onSavePopupForm"', $content);
        $this->assertStringContainsString('name="form_record_id" value="'.$group->id.'"', $content);
        $this->assertMatchesRegularExpression('/name="name"[^>]*value="Editors"/', $content);
        $this->assertStringContainsString('Ada Writer', $content);
    }

    public function test_on_save_popup_form_saves_the_group_and_refreshes_the_list(): void
    {
        $group = UserGroup::factory()->create(['name' => 'Editors', 'code' => 'editors']);
        $member = User::factory()->create();

        $ops = $this->ajax('onSavePopupForm', [
            'form_record_id' => $group->id,
            'name' => 'Senior Editors',
            'code' => 'editors',
            'users' => [$member->id],
        ])->assertOk()->json('__ajax.ops');

        $this->assertSame('Group updated.', $ops[0]['text']);
        $this->assertSame('#Lists', $ops[1]['selector']);
        $this->assertStringContainsString('Senior Editors', $ops[1]['html']);
        $this->assertSame('Senior Editors', $group->refresh()->name);
        $this->assertSame([$member->id], $group->users()->pluck('users.id')->all());
    }

    public function test_on_save_popup_form_requires_a_name(): void
    {
        $group = UserGroup::factory()->create(['name' => 'Editors', 'code' => 'editors']);

        $this->ajax('onSavePopupForm', ['form_record_id' => $group->id, 'name' => ''])->assertStatus(422);

        $this->assertSame('Editors', $group->refresh()->name);
    }

    public function test_on_store_creates_a_group_with_its_members(): void
    {
        $member = User::factory()->create();

        $response = $this->ajax('onStore', [
            'name' => 'Reviewers',
            'code' => 'reviewers',
            'users' => [$member->id],
        ], 'groups/create')->assertOk()->assertSee('Group created.');

        $group = UserGroup::where('code', 'reviewers')->firstOrFail();
        $this->assertSame(route('groups.index'), $response->json('__ajax.redirect'));
        $this->assertSame([$member->id], $group->users()->pluck('users.id')->all());
    }

    public function test_on_update_saves_the_group_from_its_own_page(): void
    {
        $group = UserGroup::factory()->create(['name' => 'Editors', 'code' => 'editors']);

        $this->ajax('onUpdate', [
            'name' => 'Editors',
            'code' => 'editors',
            'description' => 'Can create and manage content.',
        ], "groups/{$group->id}/edit")->assertOk()->assertSee('Group updated.');

        $this->assertSame('Can create and manage content.', $group->refresh()->description);
    }

    public function test_on_destroy_deletes_the_group_and_keeps_its_members(): void
    {
        $group = UserGroup::factory()->create(['code' => 'editors']);
        $member = User::factory()->create();
        $group->users()->attach($member);

        $response = $this->ajax('onDestroy', [], "groups/{$group->id}/edit")->assertOk();

        $this->assertSame(route('groups.index'), $response->json('__ajax.redirect'));
        $this->assertNull(UserGroup::find($group->id));
        $this->assertNotNull(User::find($member->id));
    }
}
