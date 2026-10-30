<?php

namespace Tests\Feature\Showcase;

use App\Models\Post;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class AuthorsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ajax posts an AJAX request for the given handler to an authors page.
     *
     * @param  array<string, mixed>  $data
     */
    protected function ajax(string $handler, array $data = [], string $url = 'authors'): TestResponse
    {
        return $this->withHeaders([
            'X-AJAX-HANDLER' => $handler,
            'X-Requested-With' => 'XMLHttpRequest',
        ])->post($url, $data);
    }

    public function test_the_list_shows_each_author_with_the_number_of_posts_they_wrote(): void
    {
        $ada = User::factory()->create(['name' => 'Ada Writer']);
        Post::factory()->count(3)->for($ada, 'author')->create();

        $html = $this->get('authors')->assertOk()->getContent();

        $this->assertStringContainsString('Ada Writer', $html);
        $this->assertStringContainsString('href="'.url("authors/{$ada->id}/edit").'"', $html);
        $this->assertMatchesRegularExpression('/list-cell-name-posts_count list-cell-type-number\s+">\s+3\s+</', $html);
        $this->assertStringContainsString('href="'.url('authors/create').'"', $html);
        $this->assertMatchesRegularExpression('/data-request="onDelete"[^>]*data-request-confirm=/', $html);
    }

    public function test_the_has_posts_filter_leaves_out_authors_without_posts(): void
    {
        Post::factory()->for(User::factory()->create(['name' => 'Ada Writer']), 'author')->create();
        User::factory()->create(['name' => 'Silent Reader']);

        $this->ajax('filter::onFilterUpdate', ['scopeName' => 'has_posts', 'value' => '1'])
            ->assertOk()
            ->assertSee('Ada Writer')
            ->assertDontSee('Silent Reader');
    }

    public function test_the_group_filter_shows_the_members_of_the_picked_group(): void
    {
        $editors = UserGroup::factory()->create(['name' => 'Editors', 'code' => 'editors']);
        User::factory()->create(['name' => 'Ada Writer', 'primary_group_id' => $editors->id]);
        User::factory()->create(['name' => 'Bo Other']);

        $this->ajax('filter::onFilterUpdate', [
            'scopeName' => 'primary_group',
            'Filter' => ['value' => json_encode([$editors->id])],
        ])
            ->assertOk()
            ->assertSee('Ada Writer')
            ->assertDontSee('Bo Other');
    }

    public function test_deleting_checked_authors_keeps_their_posts_without_an_author(): void
    {
        $authors = User::factory()->count(3)->create();
        $post = Post::factory()->for($authors[0], 'author')->create();

        $response = $this->ajax('onDelete', ['checked' => [$authors[0]->id, $authors[2]->id]])
            ->assertOk()
            ->assertSee('2 authors deleted.');

        $this->assertSame(route('authors.index'), $response->json('__ajax.redirect'));
        $this->assertSame([$authors[1]->id], User::pluck('id')->all());
        $this->assertNull($post->refresh()->author_id);
    }

    public function test_the_create_form_asks_for_a_password_and_the_groups(): void
    {
        UserGroup::factory()->create(['name' => 'Editors', 'code' => 'editors']);
        UserGroup::factory()->create(['name' => 'Guest', 'code' => 'guest']);

        $html = $this->get('authors/create')->assertOk()->getContent();

        $this->assertStringContainsString('New Author', $html);
        $this->assertMatchesRegularExpression('/type="password"\s+name="password"/', $html);
        $this->assertMatchesRegularExpression('/type="password"\s+name="password_confirmation"/', $html);
        $this->assertStringContainsString('Editors', $html);
        $this->assertStringNotContainsString('>Guest<', $html);
    }

    public function test_on_store_creates_an_author_and_returns_to_the_list(): void
    {
        $editors = UserGroup::factory()->create(['code' => 'editors']);
        $support = UserGroup::factory()->create(['code' => 'support']);

        $response = $this->ajax('onStore', [
            'name' => 'Brand New',
            'email' => 'brandnew@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'primary_group' => $editors->id,
            'groups' => [$support->id],
        ], 'authors/create')->assertOk()->assertSee('Author created.');

        $author = User::where('email', 'brandnew@example.com')->firstOrFail();
        $this->assertSame(route('authors.index'), $response->json('__ajax.redirect'));
        $this->assertSame('Brand New', $author->name);
        $this->assertTrue(Hash::check('secret123', $author->password));
        $this->assertSame($editors->id, $author->primary_group_id);
        $this->assertSame([$support->id], $author->groups()->pluck('user_groups.id')->all());
    }

    public function test_on_store_requires_a_confirmed_password(): void
    {
        $this->ajax('onStore', [
            'name' => 'Brand New',
            'email' => 'brandnew@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'different',
        ], 'authors/create')->assertStatus(422);

        $this->assertSame(0, User::count());
    }

    public function test_on_store_refuses_an_email_already_in_use(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);

        $this->ajax('onStore', [
            'name' => 'Second Person',
            'email' => 'taken@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ], 'authors/create')->assertStatus(422);

        $this->assertSame(1, User::count());
    }

    public function test_the_edit_form_shows_the_author_with_their_groups(): void
    {
        $editors = UserGroup::factory()->create(['name' => 'Editors', 'code' => 'editors']);
        $support = UserGroup::factory()->create(['name' => 'Support', 'code' => 'support']);
        $author = User::factory()->create(['primary_group_id' => $editors->id]);
        $author->groups()->attach($support);

        $html = $this->get("authors/{$author->id}/edit")->assertOk()->getContent();

        $this->assertStringContainsString('Edit Author', $html);
        $this->assertStringContainsString('value="'.e($author->email).'"', $html);
        $this->assertStringContainsString('Reset Password', $html);
        $this->assertMatchesRegularExpression('/selected="selected"\s+value="'.$editors->id.'"\s*>Editors<\/option>/', $html);
        $this->assertMatchesRegularExpression('/value="'.$support->id.'"\s+checked="checked"/', $html);
    }

    public function test_on_update_saves_the_author_and_keeps_a_blank_password(): void
    {
        $group = UserGroup::factory()->create(['code' => 'admin']);
        $author = User::factory()->create(['name' => 'Original']);
        $password = $author->password;

        $this->ajax('onUpdate', [
            'name' => 'Renamed Person',
            'email' => $author->email,
            'password' => '',
            'password_confirmation' => '',
            'notes' => 'Writes the release notes.',
            'primary_group' => $group->id,
            'groups' => [$group->id],
        ], "authors/{$author->id}/edit")->assertOk()->assertSee('Author updated.');

        $author->refresh();
        $this->assertSame('Renamed Person', $author->name);
        $this->assertSame('Writes the release notes.', $author->notes);
        $this->assertSame($group->id, $author->primary_group_id);
        $this->assertSame(1, $author->groups()->count());
        $this->assertSame($password, $author->password);
    }

    public function test_on_update_clears_the_other_groups(): void
    {
        $author = User::factory()->create();
        $author->groups()->attach(UserGroup::factory()->create(['code' => 'editors']));

        // An empty checkbox list posts only its empty hidden input
        $this->ajax('onUpdate', [
            'name' => $author->name,
            'email' => $author->email,
            'groups' => '',
        ], "authors/{$author->id}/edit")->assertOk();

        $this->assertSame(0, $author->groups()->count());
    }

    public function test_on_update_requires_a_name(): void
    {
        $author = User::factory()->create(['name' => 'Kept Name']);

        $this->ajax('onUpdate', ['name' => '', 'email' => $author->email], "authors/{$author->id}/edit")
            ->assertStatus(422);

        $this->assertSame('Kept Name', $author->refresh()->name);
    }

    public function test_on_destroy_deletes_the_author_and_returns_to_the_list(): void
    {
        $author = User::factory()->create();
        $post = Post::factory()->for($author, 'author')->create();

        $response = $this->ajax('onDestroy', [], "authors/{$author->id}/edit")->assertOk();

        $this->assertSame(route('authors.index'), $response->json('__ajax.redirect'));
        $this->assertNull(User::find($author->id));
        $this->assertNull($post->refresh()->author_id);
    }
}
