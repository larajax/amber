<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\UserGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_home_page_redirects_to_the_reference(): void
    {
        $this->get('/')->assertRedirect('reference');
    }

    public function test_the_shell_renders_the_sidebar_top_bar_and_assets(): void
    {
        $this->get('posts')
            ->assertOk()
            ->assertSee('<body class="amber-layout">', false)
            ->assertSee('id="amber-sidebar"', false)
            ->assertSee('class="topbar"', false)
            ->assertSee('data-amber-theme-toggle', false)
            ->assertSee('css/amber.css', false)
            ->assertSee('js/amber.js', false);
    }

    public function test_the_colour_mode_is_set_before_the_stylesheets_load(): void
    {
        $html = $this->get('posts')->assertOk()->getContent();

        $themeScriptAt = strpos($html, "document.documentElement.setAttribute('data-bs-theme'");
        $stylesheetAt = strpos($html, 'rel="stylesheet"');

        $this->assertNotFalse($themeScriptAt);
        $this->assertNotFalse($stylesheetAt);
        $this->assertLessThan($stylesheetAt, $themeScriptAt);
        $this->assertStringContainsString("localStorage.getItem('amber-theme')", $html);
        $this->assertStringContainsString("matchMedia('(prefers-color-scheme: dark)')", $html);
    }

    public function test_the_sidebar_lists_the_showcase_pages(): void
    {
        $html = $this->get(route('reference.index'))->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/<p class="sidebar-nav-heading">\s*Showcase\s*<\/p>/', $html);
        $this->assertSeeLinksInOrder($html, [
            route('posts.index') => 'Posts',
            route('categories.index') => 'Categories',
            route('posts.create') => 'New Post',
            route('authors.index') => 'Authors',
            route('authors.create') => 'New Author',
            route('groups.index') => 'Groups',
        ]);
        $this->assertStringNotContainsString('kitchen-sink', $html);
        $this->assertStringNotContainsString(url('users'), $html);
    }

    public function test_the_current_page_is_marked_in_the_sidebar(): void
    {
        $html = $this->get('posts')->assertOk()->getContent();
        $this->assertMatchesRegularExpression('/href="[^"]*\/posts"\s+aria-current="page"/', $html);
        $this->assertDoesNotMatchRegularExpression('/href="[^"]*\/posts\/create"\s+aria-current="page"/', $html);

        $html = $this->get('authors/create')->assertOk()->getContent();
        $this->assertMatchesRegularExpression('/href="[^"]*\/authors\/create"\s+aria-current="page"/', $html);
        $this->assertDoesNotMatchRegularExpression('/href="[^"]*\/authors"\s+aria-current="page"/', $html);

        $group = UserGroup::factory()->create(['code' => 'editors']);
        $html = $this->get("groups/{$group->id}/edit")->assertOk()->getContent();
        $this->assertMatchesRegularExpression('/href="[^"]*\/groups"\s+aria-current="page"/', $html);

        $html = $this->get(route('reference.lists.columns'))->assertOk()->getContent();
        $this->assertMatchesRegularExpression('/href="'.preg_quote(route('reference.lists.columns'), '/').'"\s+aria-current="page"/', $html);
        $this->assertDoesNotMatchRegularExpression('/href="[^"]*\/(posts|authors|groups|categories)"\s+aria-current="page"/', $html);
    }

    public function test_the_top_bar_search_filters_the_posts_list(): void
    {
        Post::factory()->create(['title' => 'Zebra Crossings Explained']);
        Post::factory()->create(['title' => 'Ordinary Article']);

        $this->get('posts?q=Zebra')
            ->assertOk()
            ->assertSee('Zebra Crossings Explained')
            ->assertDontSee('Ordinary Article')
            ->assertSee('value="Zebra"', false);
    }

    public function test_the_top_bar_search_is_an_adorned_field_named_by_its_hidden_label(): void
    {
        $html = $this->get('posts')->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/<form\s+class="search"\s+action="'.preg_quote(route('posts.index'), '/').'"\s+method="GET"/', $html);
        $this->assertMatchesRegularExpression('/<label class="visually-hidden" for="amber-search-input">\s*Search posts\s*<\/label>\s*<label class="form-control form-adorn search-input">\s*<span class="form-adorn-icon" aria-hidden="true">\s*<i class="ph ph-magnifying-glass"><\/i>\s*<\/span>\s*<input\s+id="amber-search-input"\s+class="form-ghost"\s+type="search"\s+name="q"/', $html);
        $this->assertStringNotContainsString('search-button', $html);
    }

    /**
     * assertSeeLinksInOrder checks that each address is linked with its label, in the given order.
     *
     * @param  array<string, string>  $links
     */
    protected function assertSeeLinksInOrder(string $html, array $links): void
    {
        $pattern = collect($links)
            ->map(fn (string $label, string $href) => 'href="'.preg_quote($href, '/').'".*?'.preg_quote($label, '/'))
            ->implode('.*?');

        $this->assertMatchesRegularExpression('/'.$pattern.'/s', $html);
    }
}
