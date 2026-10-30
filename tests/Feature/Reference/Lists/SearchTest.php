<?php

namespace Tests\Feature\Reference\Lists;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ajax posts an AJAX request for the given handler to the Search page.
     *
     * @param  array<string, mixed>  $data
     */
    protected function ajax(string $handler, array $data = []): TestResponse
    {
        return $this->withHeaders([
            'X-AJAX-HANDLER' => $handler,
            'X-Requested-With' => 'XMLHttpRequest',
        ])->post(route('reference.lists.search'), $data);
    }

    public function test_the_page_renders_every_example_with_its_source(): void
    {
        $html = $this->get(route('reference.lists.search'))->assertOk()->getContent();

        foreach (['tagSearch', 'boundToolbarSearch', 'scopeToolbarSearch', 'modeToolbarSearch', 'enterToolbarSearch'] as $alias) {
            $this->assertStringContainsString('id="Search-'.$alias.'"', $html);
        }

        foreach (['makeTagSearch', 'renderTagResults', 'makeBoundToolbar', 'makeScopeToolbar', 'makeModeToolbar', 'makeEnterToolbar', 'onSearchCities'] as $method) {
            $this->assertStringContainsString("function {$method}(", $html);
        }

        $this->assertStringContainsString('resources/ui/reference/lists/search/posts.yaml', $html);
        $this->assertStringContainsString('{{ Ui::searchInput(name: &#039;city_search&#039;', $html);
        $this->assertStringContainsString('resources/views/reference/lists/search/_city_results.blade.php', $html);
    }

    public function test_the_toolbar_search_is_an_adorned_field_with_its_icon_input_and_clear_button(): void
    {
        $html = $this->get(route('reference.lists.search'))->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/<div\s+id="Search-boundToolbarSearch"\s+data-control="search-input"\s*>\s*<label class="form-control form-adorn is-searchable is-growable">\s*<span class="form-adorn-icon" aria-hidden="true">\s*<i class="ph ph-magnifying-glass" aria-hidden="true"><\/i>\s*<\/span>\s*<input[^>]*name="boundToolbarSearch\[term\]"[^>]*aria-label="Search posts..."[^>]*class="form-ghost"[^>]*data-search-input \/>\s*<button\s+class="clear-input-text"[^>]*style="display:none"\s+aria-label="Clear"\s+data-search-clear\s*>\s*<i class="ph ph-x" aria-hidden="true"><\/i>\s*<\/button>\s*<\/label>/', $html);
        $this->assertStringNotContainsString('search-input-container', $html);
        $this->assertStringNotContainsString('search-icon', $html);
    }

    public function test_the_search_options_set_the_width_and_the_trigger(): void
    {
        $html = $this->get(route('reference.lists.search'))->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/id="Search-tagSearch"\s+data-control="search-input"\s*>\s*<label class="form-control form-adorn is-searchable ">/', $html);
        $this->assertMatchesRegularExpression('/name="tagSearch\[term\]"[^>]*data-request="tagSearch::onSubmit"\s+data-track-input/', $html);
        $this->assertMatchesRegularExpression('/name="enterToolbarSearch\[term\]"[^>]*data-request="enterToolbarSearch::onSubmit"\s+data-load-indicator/', $html);
        $this->assertMatchesRegularExpression('/id="Search-enterToolbarSearch"\s+data-control="search-input"\s*>\s*<label class="form-control form-adorn is-searchable ">/', $html);
    }

    public function test_the_prompt_is_escaped(): void
    {
        $html = $this->get(route('reference.lists.search'))->assertOk()->getContent();

        $this->assertStringContainsString('placeholder="Search posts, categories and tags..."', $html);
    }

    public function test_the_standalone_search_returns_the_matching_tags(): void
    {
        Tag::factory()->create(['name' => 'javascript']);
        Tag::factory()->create(['name' => 'php']);

        $html = $this->ajax('tagSearch::onSubmit', ['tagSearch' => ['term' => 'java']])
            ->assertOk()
            ->json('__ajax.ops.0');

        $this->assertSame('#tagResults', $html['selector']);
        $this->assertStringContainsString('javascript', $html['html']);
        $this->assertStringNotContainsString('php', $html['html']);
        $this->assertSame('java', json_decode(session('widget.app_controllers-ListsController-Search-tagSearch'), true)['term']);
    }

    public function test_the_standalone_search_keeps_its_term_after_a_reload(): void
    {
        Tag::factory()->create(['name' => 'javascript']);
        Tag::factory()->create(['name' => 'php']);

        $html = $this->withSession([
            'widget.app_controllers-ListsController-Search-tagSearch' => json_encode(['term' => 'php']),
        ])->get(route('reference.lists.search'))->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/name="tagSearch\[term\]"\s+value="php"/', $html);
        $this->assertMatchesRegularExpression('/<div id="tagResults">\s*<ul[^>]*>\s*<li class="badge badge-subtle theme-secondary">\s*php/', $html);
    }

    public function test_the_bound_search_needs_every_word(): void
    {
        Post::factory()->create(['title' => 'Sorting Long Lists']);
        Post::factory()->create(['title' => 'Sorting Forms']);

        $this->ajax('boundToolbarSearch::onSubmit', ['boundToolbarSearch' => ['term' => 'sorting lists']])
            ->assertOk()
            ->assertSee('Sorting Long Lists')
            ->assertDontSee('Sorting Forms');
    }

    public function test_the_search_scope_matches_tag_and_category_names(): void
    {
        $tagged = Post::factory()->create(['title' => 'First Article']);
        $tagged->tags()->attach(Tag::factory()->create(['name' => 'zebra']));
        Post::factory()->for(Category::factory()->create(['name' => 'Zebra Crossing', 'slug' => 'zebra-crossing']))
            ->create(['title' => 'Second Article']);
        Post::factory()->create(['title' => 'Third Article']);

        $this->ajax('scopeToolbarSearch::onSubmit', ['scopeToolbarSearch' => ['term' => 'zebra']])
            ->assertOk()
            ->assertSee('First Article')
            ->assertSee('Second Article')
            ->assertDontSee('Third Article');
    }

    public function test_the_any_mode_matches_one_of_the_words(): void
    {
        Post::factory()->create(['title' => 'Dark Mode']);
        Post::factory()->create(['title' => 'Long Lists']);
        Post::factory()->create(['title' => 'Field Spans']);

        $this->ajax('modeToolbarSearch::onSubmit', ['modeToolbarSearch' => ['term' => 'dark lists']])
            ->assertOk()
            ->assertSee('Dark Mode')
            ->assertSee('Long Lists')
            ->assertDontSee('Field Spans');
    }

    public function test_a_search_without_matches_shows_the_no_records_message(): void
    {
        Post::factory()->create();

        $this->ajax('enterToolbarSearch::onSubmit', ['enterToolbarSearch' => ['term' => 'nothing-like-this']])
            ->assertOk()
            ->assertSee('No posts match the search.');
    }

    public function test_the_search_inputs_render_with_and_without_a_handler(): void
    {
        $html = $this->get(route('reference.lists.search'))->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/<input[^>]*name="plain_search"[^>]*placeholder="Type to show the clear button..."[^>]*data-search-input/', $html);
        $this->assertMatchesRegularExpression('/<input[^>]*name="city_search"[^>]*data-request="onSearchCities"[^>]*data-request-trigger="input changed delay:500"/', $html);
        $this->assertStringContainsString('Type a city name, such as Paris or Sydney.', $html);
    }

    public function test_the_city_handler_updates_the_results(): void
    {
        $response = $this->ajax('onSearchCities', ['city_search' => 'syd'])->assertOk();

        $patch = collect($response->json('__ajax.ops'))->firstWhere('selector', '#cityResults');
        $this->assertNotNull($patch);
        $this->assertStringContainsString('Sydney', $patch['html']);
        $this->assertStringNotContainsString('Paris', $patch['html']);
    }

    public function test_the_city_handler_reports_no_matches(): void
    {
        $response = $this->ajax('onSearchCities', ['city_search' => 'zzz'])->assertOk();

        $this->assertStringContainsString('No cities match "zzz".', collect($response->json('__ajax.ops'))->firstWhere('selector', '#cityResults')['html']);
    }
}
