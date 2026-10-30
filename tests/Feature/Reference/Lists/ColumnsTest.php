<?php

namespace Tests\Feature\Reference\Lists;

use App\Controllers\Reference\ListsController;
use App\Models\Category;
use App\Models\Comment;
use App\Models\File;
use App\Models\Post;
use App\Models\Sample;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Larajax\Ui\Widgets\Lists;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ColumnsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * tearDown removes the stored attachment files.
     */
    protected function tearDown(): void
    {
        File::all()->each->delete();

        parent::tearDown();
    }

    /**
     * listHtml returns the markup of one list on the Columns page.
     */
    protected function listHtml(string $example): string
    {
        $html = $this->get(route('reference.lists.columns'))->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/id="Lists-'.Str::camel($example).'List".*?<\/table>/s', $html);
        preg_match('/id="Lists-'.Str::camel($example).'List".*?<\/table>/s', $html, $match);

        return $match[0];
    }

    public function test_the_page_renders_every_example_beside_its_yaml(): void
    {
        $html = $this->get(route('reference.lists.columns'))->assertOk()->getContent();

        foreach (['text', 'number', 'switch', 'colorpicker', 'selectable', 'dates', 'relative', 'summary', 'image', 'files', 'linkage', 'partial', 'relations', 'headers'] as $example) {
            $this->assertStringContainsString("resources/ui/reference/lists/columns/{$example}.yaml", $html);
            $this->assertStringContainsString('id="Lists-'.Str::camel($example).'List"', $html);
        }

        $this->assertStringContainsString('app/Controllers/Reference/ListsController/ColumnsExamples.php', $html);
        $this->assertStringContainsString('protected function makeColumnLists(): array', $html);
        $this->assertStringContainsString('resources/views/reference/lists/columns/_column_tags.php', $html);
    }

    public function test_each_list_pages_on_its_own_without_the_address(): void
    {
        Post::factory()->count(6)->create();

        $html = $this->get(route('reference.lists.columns'))->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/data-request="textList::onPaginate"\s+data-request-data="\{ _page: 2 \}"/', $html);
        $this->assertStringNotContainsString('data-request-query', $html);
    }

    public function test_paging_a_list_refreshes_only_that_list(): void
    {
        Post::factory()->count(6)->sequence(fn ($sequence) => ['title' => 'Post Number '.($sequence->index + 1)])->create();

        $response = $this->withHeaders([
            'X-AJAX-HANDLER' => 'numberList::onPaginate',
            'X-Requested-With' => 'XMLHttpRequest',
        ])->post(route('reference.lists.columns'), ['_page' => 2]);

        $response->assertOk();
        $ops = collect($response->json('__ajax.ops'));
        $this->assertSame(['#Lists-numberList'], $ops->pluck('selector')->all());
        $this->assertStringContainsString('Post Number 5', $ops->first()['html']);
        $this->assertStringNotContainsString('Post Number 1<', $ops->first()['html']);
    }

    public function test_text_columns_format_join_arrays_and_fall_back_to_the_default(): void
    {
        Post::factory()->create(['title' => 'Formatted', 'slug' => 'formatted-post', 'keywords' => ['php', 'ajax']]);
        Post::factory()->create(['title' => 'Bare', 'keywords' => []]);

        $html = $this->listHtml('text');

        $this->assertStringContainsString('/blog/formatted-post', $html);
        $this->assertStringContainsString('php, ajax', $html);
        $this->assertMatchesRegularExpression('/list-cell-name-keywords list-cell-type-text\s+">\s+None\s+</', $html);
    }

    public function test_number_columns_format_the_value_and_count_relations(): void
    {
        $post = Post::factory()->create(['views' => 1234]);
        Comment::factory()->count(4)->for($post)->create();

        $html = $this->listHtml('number');

        $this->assertStringContainsString('1234 views', $html);
        $this->assertMatchesRegularExpression('/list-cell-name-comments_count list-cell-type-number\s+">\s+4\s+</', $html);
    }

    public function test_switch_columns_label_the_true_and_false_values(): void
    {
        Post::factory()->published()->create();
        Post::factory()->create();

        $html = $this->listHtml('switch');

        $this->assertStringContainsString('list-switch is-true', $html);
        $this->assertStringContainsString('title="Published"', $html);
        $this->assertStringContainsString('list-switch is-false', $html);
        $this->assertStringContainsString('title="Not published"', $html);
    }

    public function test_color_picker_columns_show_a_closed_swatch(): void
    {
        Post::factory()->create(['featured_color' => '#ff0000']);

        $html = $this->listHtml('colorpicker');

        $this->assertMatchesRegularExpression('/<span\s+class="list-colorpicker"\s+style="--background-color:#ff0000"\s+><\/span>/', $html);
    }

    public function test_selectable_columns_show_option_labels_with_colors_icons_methods_and_presets(): void
    {
        Sample::factory()->create([
            'status' => 'active',
            'priority' => 'high',
            'country' => 'au',
            'state' => 'nsw',
            'timezone' => 'Australia/Sydney',
            'features' => ['wifi', 'gym'],
        ]);

        $html = $this->listHtml('selectable');

        $this->assertStringContainsString('<span class="status-indicator" style="background:#22c55e"></span>', $html);
        $this->assertStringContainsString('Active', $html);
        $this->assertStringContainsString('ph ph-arrow-up', $html);
        $this->assertStringContainsString('High', $html);
        $this->assertStringContainsString('Australia', $html);
        $this->assertStringContainsString('New South Wales', $html);
        $this->assertMatchesRegularExpression('/\(\+\d{2}:\d{2}\) Australia\/Sydney/', $html);
        $this->assertStringContainsString('Wi-Fi, Gym', $html);
    }

    public function test_date_columns_use_their_default_or_given_format(): void
    {
        Post::factory()->create([
            'published' => true,
            'published_at' => '2026-03-14 09:30:00',
            'review_on' => '2026-04-02',
            'share_time' => '07:45:00',
        ]);

        $html = $this->listHtml('dates');

        $this->assertStringContainsString('Mar 14, 2026', $html);
        $this->assertStringContainsString('Thu 2 Apr 2026', $html);
        $this->assertMatchesRegularExpression('/list-cell-name-share_time list-cell-type-time\s+">\s+07:45\s+</', $html);
    }

    public function test_relative_columns_name_the_day_and_count_the_time_passed(): void
    {
        Post::factory()->create(['published' => true, 'published_at' => now()->subDays(3)]);

        $html = $this->listHtml('relative');

        $this->assertStringContainsString('list-cell-type-timetense', $html);
        $this->assertStringContainsString(' at ', $html);
        $this->assertStringContainsString('3 days ago', $html);
    }

    public function test_summary_columns_strip_html_and_shorten_to_words(): void
    {
        Post::factory()->create([
            'excerpt' => 'A short line about building forms that goes on for quite a while longer.',
            'content' => '<h2>Heading</h2><p>One two three four five six seven eight nine ten.</p>',
        ]);

        $html = $this->listHtml('summary');

        $this->assertStringContainsString('A short line about building forms that g...', $html);
        $this->assertStringContainsString('Heading One two three four five six seven (more)', $html);
        $this->assertStringNotContainsString('&lt;h2&gt;', $html);
    }

    public function test_image_columns_render_a_sized_thumbnail(): void
    {
        $post = Post::factory()->create();
        $post->featured_image()->create(['data' => UploadedFile::fake()->image('cover.png', 200, 120)]);

        $html = $this->listHtml('image');

        $this->assertStringContainsString('list-image-thumb', $html);
        $this->assertStringContainsString('thumb_', $html);
        $this->assertStringContainsString('width="96" height="54"', $html);
        $this->assertStringNotContainsString('is-default-size', $html);
    }

    public function test_file_columns_show_icons_and_link_them_when_not_clickable(): void
    {
        $sample = Sample::factory()->create();
        $sample->gallery()->create(['data' => UploadedFile::fake()->image('one.png', 80, 80)]);
        $sample->brochure()->create(['data' => UploadedFile::fake()->create('brochure.pdf', 4, 'application/pdf')]);
        $sample->attachments()->create(['data' => UploadedFile::fake()->create('notes.txt', 1, 'text/plain')]);
        $sample->attachments()->create(['data' => UploadedFile::fake()->create('specs.csv', 1, 'text/csv')]);
        $sample->attachments()->create(['data' => UploadedFile::fake()->create('extra.json', 1, 'application/json')]);

        $html = $this->listHtml('files');

        $this->assertStringContainsString('list-image-thumb is-default-size', $html);
        $this->assertMatchesRegularExpression('/<span class="list-file-item" title="brochure.pdf">/', $html);
        $this->assertMatchesRegularExpression('/<a\s+href="[^"]+"\s+target="_blank"\s+class="list-file-item"\s+title="notes.txt"/', $html);
        $this->assertStringContainsString('title="specs.csv"', $html);
        $this->assertStringNotContainsString('title="extra.json"', $html);
    }

    public function test_linkage_columns_link_to_the_url_with_text_and_attributes(): void
    {
        $category = Category::factory()->create(['name' => 'Guides', 'slug' => 'guides']);
        $post = Post::factory()->for($category)->create();

        $html = $this->listHtml('linkage');

        $this->assertStringContainsString('href="'.url("categories/{$category->id}/edit").'"', $html);
        $this->assertStringContainsString('>Guides</a>', $html);
        $this->assertStringContainsString('href="'.url("posts/{$post->id}/edit").'"  target="_blank">Edit post</a>', $html);
        $this->assertMatchesRegularExpression('/list-cell-name-category list-cell-type-linkage\s+nolink/', $html);
    }

    /**
     * @return array<string, array{string, bool}>
     */
    public static function linkageUrlProvider(): array
    {
        return [
            'https' => ['https://example.com', true],
            'mailto' => ['mailto:someone@example.com', true],
            'relative path' => ['/posts/1/edit', true],
            'javascript' => ['javascript:alert(1)', false],
            'uppercase javascript' => ['JAVASCRIPT:alert(1)', false],
            'javascript with control characters' => ["java\tscript:alert(1)", false],
            'data uri' => ['data:text/html,<script>alert(1)</script>', false],
        ];
    }

    #[DataProvider('linkageUrlProvider')]
    public function test_linkage_columns_only_link_safe_urls(string $url, bool $isLinked): void
    {
        $list = Lists::createIn(new ListsController, [
            'model' => new Post,
            'columns' => [
                'website' => ['label' => 'Website', 'type' => 'linkage'],
            ],
        ]);

        $post = new Post;
        $post->website = $url;

        $html = (string) $list->getColumnValue($post, $list->getColumns()['website']);

        if ($isLinked) {
            $this->assertStringContainsString('href="'.e($url).'"', $html);
        }
        else {
            $this->assertStringNotContainsString('<a', $html);
            $this->assertSame(e($url), $html);
        }
    }

    public function test_values_named_like_a_language_file_render_as_given(): void
    {
        Post::factory()->for(Category::factory()->create(['name' => 'validation', 'slug' => 'validation']))->create();

        $this->assertStringContainsString('>validation</a>', $this->listHtml('linkage'));
    }

    public function test_partial_columns_render_the_partial_with_the_eager_loaded_relation(): void
    {
        $tagged = Post::factory()->create(['title' => 'Tagged']);
        $tagged->tags()->attach([
            Tag::factory()->create(['name' => 'alpha'])->id,
            Tag::factory()->create(['name' => 'beta'])->id,
        ]);
        Post::factory()->create(['title' => 'Plain']);

        $html = $this->listHtml('partial');

        $this->assertStringContainsString('alpha', $html);
        $this->assertStringContainsString('beta', $html);
        $this->assertStringContainsString('Untagged', $html);
    }

    public function test_relation_and_sql_columns_read_their_values_in_the_query(): void
    {
        $category = Category::factory()->create(['name' => 'Guides', 'slug' => 'guides']);
        $post = Post::factory()->for($category)->create(['title' => 'Twelve chars']);
        $post->tags()->attach([
            Tag::factory()->create(['name' => 'alpha'])->id,
            Tag::factory()->create(['name' => 'beta'])->id,
        ]);

        $html = $this->listHtml('relations');

        $this->assertMatchesRegularExpression('/list-cell-name-category_name list-cell-type-text\s+">\s+Guides\s+</', $html);
        $this->assertMatchesRegularExpression('/list-cell-name-tag_names list-cell-type-text\s+">\s+(alpha, beta|beta, alpha)\s+</', $html);
        $this->assertMatchesRegularExpression('/list-cell-name-category-slug list-cell-type-text\s+">\s+guides\s+</', $html);
        $this->assertMatchesRegularExpression('/list-cell-name-title_length list-cell-type-number\s+">\s+12\s+</', $html);
    }

    public function test_header_options_set_labels_tooltips_widths_and_classes(): void
    {
        Post::factory()->create(['status' => 'approved', 'views' => 10]);

        $html = $this->listHtml('headers');

        $this->assertStringContainsString('style="width: 40%"', $html);
        $this->assertStringContainsString('data-bs-title="Shown as the heading of the post."', $html);
        $this->assertMatchesRegularExpression('/<a[^>]*data-request-data="sortColumn: \'views\'[^>]*>Views</', $html);
        $this->assertStringNotContainsString('Views this year', $html);
        $this->assertStringContainsString('list-cell-name-views list-cell-type-number list-cell-align-center', $html);
        $this->assertStringContainsString('list-cell-type-text  text-capitalize', $html);
        $this->assertStringContainsString('fst-italic has-tooltip', $html);
        $this->assertStringContainsString('class="ph ph-question"', $html);
        $this->assertStringContainsString('data-bs-placement="right"', $html);
        $this->assertStringContainsString('data-bs-html="true"', $html);
        $this->assertStringContainsString('data-bs-title="Only <strong>approved</strong> posts can go live."', $html);
    }
}
