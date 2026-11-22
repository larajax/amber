<?php

namespace Tests\Feature\Reference\Extending;

use App\Classes\ControllerBase;
use App\Controllers\Reference\ExtendingController;
use App\FormWidgets\TimeChecker;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Sample;
use App\Widgets\CommentFeed;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Testing\TestResponse;
use Larajax\Ui\Classes\ListWidgetBase;
use Larajax\Ui\Classes\WidgetBase;
use Larajax\Ui\Classes\WidgetManager;
use Larajax\Ui\Facades\Ui;
use Larajax\Ui\Widgets\Form;
use Larajax\Ui\Widgets\Lists;
use October\Rain\Exception\ApplicationException;
use Tests\TestCase;

class CustomWidgetsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware('web')->group(function () {
            Route::match(['get', 'post'], '/widget-assets', [WidgetAssetsTestController::class, 'index']);
            Route::match(['get', 'post'], '/widget-assets/blank', [WidgetAssetsTestController::class, 'blank']);
        });
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    /**
     * ajax posts an AJAX handler request to a URL.
     *
     * @param  array<string, mixed>  $data
     */
    protected function ajax(string $handler, string $url, array $data = []): TestResponse
    {
        return $this->withHeaders([
            'X-AJAX-HANDLER' => $handler,
            'X-Requested-With' => 'XMLHttpRequest',
        ])->post($url, $data);
    }

    /**
     * makeForm builds a form holding the time checker for the given sample.
     *
     * @param  array<string, mixed>  $config
     */
    protected function makeForm(Sample $sample, array $config = []): Form
    {
        $form = Form::createIn(new ExtendingController, [
            'model' => $sample,
            'fields' => [
                'checked_at' => $config + ['label' => 'Server Time Check', 'type' => 'timechecker'],
            ],
        ]);

        $form->bindToController();

        return $form;
    }

    /**
     * makePostWithComments creates a post with numbered comments, "Comment 1" being the oldest.
     */
    protected function makePostWithComments(int $count): Post
    {
        $post = Post::factory()->create();

        foreach (range(1, $count) as $number) {
            Comment::factory()->for($post)->create(['author' => "Reader {$number}", 'content' => "Comment {$number}"]);
        }

        return $post;
    }

    /**
     * makeFeed builds a comment feed for the given post, bound to the page's controller.
     *
     * @param  array<string, mixed>  $config
     */
    protected function makeFeed(Post $post, array $config = []): CommentFeed
    {
        $feed = CommentFeed::createIn(new ExtendingController, $config + ['post' => $post]);

        $feed->bindToController();

        return $feed;
    }

    /**
     * shownComments returns the numbers of the comments in the markup, in the order they are shown.
     *
     * @return array<int, int>
     */
    protected function shownComments(string $html): array
    {
        preg_match_all('/Comment (\d+)/', $html, $matches);

        return array_map('intval', $matches[1]);
    }

    /**
     * responseAssets returns the assets an AJAX response asks the browser to load, as type => [url => attributes].
     *
     * @return array<string, array<string, array<string, mixed>>>
     */
    protected function responseAssets(TestResponse $response): array
    {
        $assets = [];

        foreach ($response->json('__ajax.ops') as $op) {
            if ($op['op'] !== 'loadAssets') {
                continue;
            }

            foreach ($op['assets'] as $asset) {
                if (is_string($asset)) {
                    $assets[$op['type']][$asset] = [];
                }
                else {
                    $assets[$op['type']][$asset['url']] = $asset['attributes'];
                }
            }
        }

        return $assets;
    }

    public function test_the_page_renders_the_widget_and_every_source_file(): void
    {
        Sample::factory()->create(['checked_at' => '2026-10-30 09:00:00']);

        $html = $this->get(route('reference.extending.custom-widgets'))->assertOk()->getContent();

        $sources = [
            'resources/ui/reference/extending/custom-widgets/time-checker.yaml',
            'app/FormWidgets/TimeChecker.php',
            'app/FormWidgets/TimeChecker/partials/_timechecker.php',
            'app/FormWidgets/TimeChecker/partials/_clock.php',
            'app/Providers/AppServiceProvider.php',
            'public/js/widgets/timechecker.js',
            'public/css/widgets/timechecker.css',
            'resources/views/layouts/app.blade.php',
        ];

        foreach ($sources as $path) {
            $this->assertStringContainsString($path, $html);
            $this->assertStringContainsString('<pre><code>'.e(trim(file_get_contents(base_path($path)))).'</code></pre>', $html, "{$path} is not shown.");
        }

        $this->assertSame(1, preg_match_all('/class="time-checker"\s+data-control="timechecker"/', $html));
        $this->assertStringContainsString('Fri 30 Oct 2026, 09:00:00 UTC', $html);
        $this->assertStringContainsString('data-request="exampleTimeCheckerCheckedAt::onCheckTime"', $html);
        $this->assertStringContainsString('data-request="onSaveExamples"', $html);
    }

    public function test_the_page_loads_the_widget_assets_once_in_the_head(): void
    {
        $html = $this->get(route('reference.extending.custom-widgets'))->assertOk()->getContent();
        $head = substr($html, 0, strpos($html, '</head>'));

        $css = '<link rel="stylesheet" href="'.asset('css/widgets/timechecker.css').'" />';
        $js = '<script src="'.asset('js/widgets/timechecker.js').'" defer></script>';

        $this->assertSame(1, substr_count($html, $css));
        $this->assertSame(1, substr_count($html, $js));
        $this->assertStringContainsString($css, $head);
        $this->assertStringContainsString($js, $head);
        $this->assertLessThan(strpos($head, $js), strpos($head, $css));
    }

    public function test_checking_the_time_stamps_the_server_time_into_the_field(): void
    {
        Carbon::setTestNow('2026-10-30 21:15:42');
        $sample = Sample::factory()->create();

        $response = $this->ajax('exampleTimeCheckerCheckedAt::onCheckTime', route('reference.extending.custom-widgets'))->assertOk();

        $patch = collect($response->json('__ajax.ops'))->first(fn (array $op) => isset($op['selector']));
        $this->assertStringEndsWith('-checked_at', $patch['selector']);
        $this->assertMatchesRegularExpression('/name="checked_at"\s+value="2026-10-30 21:15:42"/', $patch['html']);
        $this->assertStringContainsString('data-server-time="'.Carbon::now()->getTimestampMs().'"', $patch['html']);
        $this->assertStringContainsString('Server clock', $patch['html']);

        // The widget was created during the request, so its assets travel with the response
        $this->assertSame(['defer' => true], $this->responseAssets($response)['js'][asset('js/widgets/timechecker.js')]);

        // Checking only stamps the field, and saving the page stores it
        $this->assertNull($sample->fresh()->checked_at);
    }

    public function test_saving_the_page_stores_the_stamp_on_the_reference_sample(): void
    {
        $sample = Sample::factory()->create();

        $this->ajax('onSaveExamples', route('reference.extending.custom-widgets'), [
            'checked_at' => '2026-10-30 21:15:42',
        ])->assertOk();

        $this->assertSame('2026-10-30 21:15:42', (string) $sample->fresh()->getRawOriginal('checked_at'));
    }

    public function test_the_app_registers_the_widget_under_its_code(): void
    {
        $this->assertSame(TimeChecker::class, WidgetManager::instance()->resolveFormWidget('timechecker'));
    }

    public function test_the_widget_renders_the_saved_stamp_and_its_check_button(): void
    {
        $sample = Sample::factory()->create(['checked_at' => '2026-10-30 09:00:00']);

        $html = (string) $this->makeForm($sample, ['format' => 'j M Y H:i'])->render();

        $this->assertStringContainsString('data-control="timechecker"', $html);
        $this->assertMatchesRegularExpression('/name="checked_at"\s+value="2026-10-30 09:00:00"/', $html);
        $this->assertStringContainsString('Saved stamp', $html);
        $this->assertStringContainsString('30 Oct 2026 09:00', $html);
        $this->assertStringContainsString('data-request="formCheckedAt::onCheckTime"', $html);
        $this->assertStringNotContainsString('data-server-time', $html);
    }

    public function test_an_empty_field_says_it_has_not_been_checked(): void
    {
        $html = (string) $this->makeForm(Sample::factory()->create())->render();

        $this->assertStringContainsString('Not checked yet', $html);
        $this->assertMatchesRegularExpression('/name="checked_at"\s+value=""/', $html);
    }

    public function test_the_widget_adds_its_own_stylesheet_and_script(): void
    {
        $widget = $this->makeForm(Sample::factory()->create())->getFormWidget('checked_at');

        $this->assertSame([
            'js' => [asset('js/widgets/timechecker.js')],
            'css' => [asset('css/widgets/timechecker.css')],
        ], $widget->getAssetPaths());
    }

    public function test_the_save_value_is_the_stamp_or_null(): void
    {
        $widget = $this->makeForm(Sample::factory()->create())->getFormWidget('checked_at');

        $this->assertSame('2026-10-30 21:15:42', $widget->getSaveValue('2026-10-30 21:15:42'));
        $this->assertNull($widget->getSaveValue(''));
    }

    public function test_the_page_renders_the_comment_feed_and_every_source_file(): void
    {
        $this->makePostWithComments(2);
        $this->makePostWithComments(5);

        $html = $this->get(route('reference.extending.custom-widgets'))->assertOk()->getContent();
        $head = substr($html, 0, strpos($html, '</head>'));

        $sources = [
            'app/Widgets/CommentFeed.php',
            'app/Widgets/CommentFeed/partials/_commentfeed.php',
            'app/Widgets/CommentFeed/partials/_comments.php',
            'public/css/widgets/commentfeed.css',
        ];

        foreach ($sources as $path) {
            $this->assertStringContainsString($path, $html);
            $this->assertStringContainsString('<pre><code>'.e(trim(file_get_contents(base_path($path)))).'</code></pre>', $html, "{$path} is not shown.");
        }

        // The method that builds the widget is printed from the trait that holds it
        $this->assertStringContainsString('app/Controllers/Reference/ExtendingController/StandaloneWidgets.php', $html);
        $this->assertStringContainsString(e('protected function makeCommentFeedWidget(): CommentFeed'), $html);

        // The feed shows the first page of the post with the most comments, newest first
        $feed = substr($html, strpos($html, '<div id="CommentFeed" class="comment-feed">'));
        $feed = substr($feed, 0, strpos($feed, '<details'));

        $this->assertSame([5, 4, 3], $this->shownComments($feed));
        $this->assertStringContainsString('data-request="commentfeed::onLoadMore"', $feed);
        $this->assertStringContainsString('data-request-data="'.e('{"shown":3}').'"', $feed);
        $this->assertStringContainsString('Show more (2 left)', $feed);

        $this->assertStringContainsString('<link rel="stylesheet" href="'.asset('css/widgets/commentfeed.css').'" />', $head);
    }

    public function test_the_page_renders_an_empty_feed_when_there_are_no_posts(): void
    {
        $html = $this->get(route('reference.extending.custom-widgets'))->assertOk()->getContent();

        $this->assertStringContainsString('No comments yet.', $html);
        $this->assertStringNotContainsString('data-request="commentfeed::onLoadMore"', $html);
    }

    public function test_showing_more_adds_a_page_of_comments_to_the_feed(): void
    {
        $this->makePostWithComments(8);

        $response = $this->ajax('commentfeed::onLoadMore', route('reference.extending.custom-widgets'), ['shown' => 3])->assertOk();

        $patch = collect($response->json('__ajax.ops'))->first(fn (array $op) => isset($op['selector']));

        $this->assertSame('#CommentFeed', $patch['selector']);
        $this->assertSame([8, 7, 6, 5, 4, 3], $this->shownComments($patch['html']));
        $this->assertStringContainsString('data-request-data="'.e('{"shown":6}').'"', $patch['html']);
        $this->assertStringContainsString('Show more (2 left)', $patch['html']);

        // The handler replaces what is inside the widget's element, which stays on the page
        $this->assertStringNotContainsString('id="CommentFeed"', $patch['html']);
    }

    public function test_the_last_page_of_comments_has_no_button(): void
    {
        $this->makePostWithComments(5);

        $response = $this->ajax('commentfeed::onLoadMore', route('reference.extending.custom-widgets'), ['shown' => 3])->assertOk();

        $patch = collect($response->json('__ajax.ops'))->first(fn (array $op) => isset($op['selector']));

        $this->assertSame([5, 4, 3, 2, 1], $this->shownComments($patch['html']));
        $this->assertStringNotContainsString('onLoadMore', $patch['html']);
    }

    public function test_the_feed_takes_its_page_size_from_the_options(): void
    {
        $html = (string) $this->makeFeed($this->makePostWithComments(5), ['pageSize' => 2])->render();

        $this->assertSame([5, 4], $this->shownComments($html));
        $this->assertStringContainsString('Show more (3 left)', $html);
        $this->assertStringContainsString('Reader 5', $html);
    }

    public function test_the_feed_leaves_out_deleted_comments_and_other_posts(): void
    {
        $post = $this->makePostWithComments(3);
        $post->comments()->where('content', 'Comment 2')->first()->delete();
        Comment::factory()->create(['content' => 'Comment 99']);

        $html = (string) $this->makeFeed($post, ['pageSize' => 10])->render();

        $this->assertSame([3, 1], $this->shownComments($html));
        $this->assertStringNotContainsString('onLoadMore', $html);
    }

    public function test_an_alias_names_the_feed_element_and_its_handler(): void
    {
        $html = (string) $this->makeFeed($this->makePostWithComments(5), ['alias' => 'latestComments'])->render();

        $this->assertStringContainsString('id="CommentFeed-latestComments"', $html);
        $this->assertStringContainsString('data-request="latestComments::onLoadMore"', $html);
    }

    public function test_the_feed_escapes_what_readers_wrote(): void
    {
        $post = Post::factory()->create();
        Comment::factory()->for($post)->create(['author' => '<b>Mallory</b>', 'content' => '<script>alert(1)</script>']);

        $html = (string) $this->makeFeed($post)->render();

        $this->assertStringContainsString(e('<b>Mallory</b>'), $html);
        $this->assertStringContainsString(e('<script>alert(1)</script>'), $html);
        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
    }

    public function test_the_feed_adds_its_own_stylesheet(): void
    {
        $this->assertSame([
            'js' => [],
            'css' => [asset('css/widgets/commentfeed.css')],
        ], $this->makeFeed(Post::factory()->create())->getAssetPaths());
    }

    public function test_a_layout_prints_widget_assets_once_in_the_head(): void
    {
        $html = $this->get('/widget-assets')->assertOk()->getContent();
        $head = substr($html, 0, strpos($html, '</head>'));

        $css = '<link rel="stylesheet" href="'.asset('css/colorwheel.css').'" />';
        $js = '<script src="'.asset('js/colorwheel.js').'" type="module"></script>';

        $this->assertSame(1, substr_count($html, $css));
        $this->assertSame(1, substr_count($html, $js));
        $this->assertStringContainsString($css, $head);
        $this->assertStringContainsString($js, $head);
        $this->assertLessThan(strpos($head, $js), strpos($head, $css));

        // A widget created while the page content renders still reaches the head
        $this->assertStringContainsString('<link rel="stylesheet" href="'.asset('css/swatch.css').'" />', $head);
        $this->assertStringContainsString('data-control="colorwheel"', $html);
    }

    public function test_relative_names_resolve_against_the_asset_path(): void
    {
        $plain = ColorWheelTestWidget::createIn(new WidgetAssetsTestController);
        $packaged = PackagedColorWheelTestWidget::createIn(new WidgetAssetsTestController);

        $this->assertNull($plain->assetPath);

        $this->assertSame([
            'js' => [asset('js/colorwheel.js'), asset('js/shared.js'), 'https://cdn.example.com/wheel.js'],
            'css' => [asset('css/colorwheel.css')],
        ], $plain->getAssetPaths());

        $this->assertSame([
            'js' => [asset('vendor/acme/colorwheel/js/colorwheel.js'), asset('js/shared.js'), 'https://cdn.example.com/wheel.js'],
            'css' => [asset('vendor/acme/colorwheel/css/colorwheel.css')],
        ], $packaged->getAssetPaths());
    }

    public function test_a_guessed_public_asset_path_sits_next_to_the_widget_class(): void
    {
        $widget = ColorWheelTestWidget::createIn(new WidgetAssetsTestController);
        $widget->assetPath = $widget->guessViewPath('/assets', true);

        $this->assertSame('/tests/Feature/Reference/Extending/ColorWheelTestWidget/assets', $widget->assetPath);
        $this->assertSame('/tests/Feature/Reference/Extending/ColorWheelTestWidget/assets/js/colorwheel.js', $widget->getAssetPath('js/colorwheel.js'));
    }

    public function test_protocol_relative_urls_pass_through_unchanged(): void
    {
        $widget = ColorWheelTestWidget::createIn(new WidgetAssetsTestController);
        $widget->addJs('//cdn.example.com/relative.js');

        $this->assertContains('//cdn.example.com/relative.js', $widget->getAssetPaths()['js']);
        $this->assertStringContainsString('<script src="//cdn.example.com/relative.js"></script>', (string) Ui::renderAssets('js'));
    }

    public function test_asset_attributes_are_printed_without_the_build_reference(): void
    {
        ColorWheelTestWidget::createIn(new WidgetAssetsTestController);

        $html = (string) Ui::renderAssets();

        $this->assertStringContainsString('<script src="'.asset('js/colorwheel.js').'" type="module"></script>', $html);
        $this->assertStringContainsString('<script src="'.asset('js/shared.js').'" defer></script>', $html);
        $this->assertStringContainsString('<script src="https://cdn.example.com/wheel.js"></script>', $html);
        $this->assertStringNotContainsString('build=', $html);
        $this->assertStringNotContainsString('<script', (string) Ui::renderAssets('css'));
    }

    public function test_duplicate_assets_print_once_in_the_order_added(): void
    {
        $controller = new WidgetAssetsTestController;
        ColorWheelTestWidget::createIn($controller);
        ColorWheelTestWidget::createIn($controller, ['alias' => 'second']);
        PackagedColorWheelTestWidget::createIn($controller);

        $this->assertSame(implode(PHP_EOL, [
            '<link rel="stylesheet" href="'.asset('css/colorwheel.css').'" />',
            '<link rel="stylesheet" href="'.asset('vendor/acme/colorwheel/css/colorwheel.css').'" />',
            '<script src="'.asset('js/colorwheel.js').'" type="module"></script>',
            '<script src="'.asset('js/shared.js').'" defer></script>',
            '<script src="https://cdn.example.com/wheel.js"></script>',
            '<script src="'.asset('vendor/acme/colorwheel/js/colorwheel.js').'" type="module"></script>',
        ]).PHP_EOL, (string) Ui::renderAssets());
    }

    public function test_an_ajax_handler_returning_an_array_sends_the_widget_assets(): void
    {
        $response = $this->ajax('onLoadArray', '/widget-assets/blank')->assertOk();

        $this->assertStringContainsString('data-control="colorwheel"', $response->json('result'));

        // The global build reference is already on every page, so it stays out of AJAX responses
        $this->assertSame([
            'js' => [
                asset('js/colorwheel.js') => ['type' => 'module'],
                asset('js/shared.js') => ['defer' => true],
            ],
            'css' => [
                asset('css/colorwheel.css') => [],
                asset('css/swatch.css') => [],
            ],
        ], $this->responseAssets($response));
    }

    public function test_an_ajax_handler_returning_a_string_sends_the_widget_assets(): void
    {
        $response = $this->ajax('onLoadString', '/widget-assets/blank')->assertOk();

        $this->assertStringContainsString('data-control="colorwheel"', $response->json('result'));
        $this->assertArrayHasKey(asset('js/colorwheel.js'), $this->responseAssets($response)['js']);
        $this->assertArrayHasKey(asset('css/swatch.css'), $this->responseAssets($response)['css']);
    }

    public function test_an_ajax_handler_returning_an_ajax_response_sends_the_widget_assets(): void
    {
        $response = $this->ajax('onLoadResponse', '/widget-assets/blank')->assertOk();

        $this->assertArrayHasKey(asset('js/colorwheel.js'), $this->responseAssets($response)['js']);
    }

    public function test_an_ajax_response_without_widget_assets_has_no_load_operations(): void
    {
        $response = $this->ajax('onNothing', '/widget-assets/blank')->assertOk();

        $this->assertSame([], $this->responseAssets($response));
    }

    public function test_widget_errors_hide_internal_details_outside_debug_mode(): void
    {
        config(['app.debug' => false]);

        $widget = ColorWheelTestWidget::createIn(new WidgetAssetsTestController);
        $widget->handleError(new Exception('SQLSTATE connection details'));

        $this->assertSame("We're sorry, but something went wrong and the page cannot be displayed.", $widget->getFatalError());
    }

    public function test_widget_errors_show_safe_exception_messages(): void
    {
        config(['app.debug' => false]);

        $widget = ColorWheelTestWidget::createIn(new WidgetAssetsTestController);
        $widget->handleError(new ApplicationException('Record is locked'));

        $this->assertSame('Record is locked', $widget->getFatalError());
    }

    public function test_widget_errors_include_details_in_debug_mode(): void
    {
        config(['app.debug' => true]);

        $widget = ColorWheelTestWidget::createIn(new WidgetAssetsTestController);
        $widget->handleError(new Exception('Something broke'));

        $this->assertStringContainsString('"Something broke" on line', $widget->getFatalError());
    }

    public function test_the_meter_list_widget_draws_each_value_as_a_share_of_max(): void
    {
        Post::factory()->create(['title' => 'Popular', 'views' => 6000]);
        Post::factory()->create(['title' => 'Overflowing', 'views' => 9000]);

        $html = $this->get(route('reference.extending.custom-widgets'))->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/<td class="[^"]*list-cell-type-meter[^"]*nolink">\s*<button\s+type="button"\s+class="list-meter"\s+title="6,000"\s+data-request="meterListViews::onShowValue"[^>]*>\s*<span class="list-meter-bar" style="width: 75%"><\/span>/', $html);
        $this->assertStringContainsString('<span class="list-meter-bar" style="width: 100%"></span>', $html);
        $this->assertStringContainsString('<link rel="stylesheet" href="'.asset('css/widgets/meter.css').'"', $html);

        foreach (['list-widget', 'list-widget-class'] as $section) {
            $this->assertStringContainsString('id="'.$section.'"', $html);
        }

        $this->assertStringContainsString('app/ListWidgets/Meter.php', $html);
    }

    public function test_the_meter_handler_names_the_value_of_a_row_in_the_list(): void
    {
        $post = Post::factory()->create(['views' => 2000]);

        $response = $this->ajax('meterListViews::onShowValue', route('reference.extending.custom-widgets'), ['key' => $post->id])->assertOk();

        $flash = collect($response->json('__ajax.ops'))->firstWhere('op', 'flash');
        $this->assertSame('info', $flash['level']);
        $this->assertSame('Views: 2,000, 25% of 8,000.', $flash['text']);
    }

    public function test_list_widget_handlers_reach_bound_and_late_columns_through_the_list_query(): void
    {
        $this->registerPingListWidget();

        $published = Post::factory()->create(['title' => 'Shown', 'published' => true]);
        $draft = Post::factory()->create(['title' => 'Filtered out', 'published' => false]);

        $this->assertFlash($this->pingAjax('listViews::onPing', ['key' => $published->id]), 'views: Shown');
        $this->assertFlash($this->pingAjax('listLate::onPing', ['key' => $published->id]), 'late: Shown');
        $this->assertFlash($this->pingAjax('listViews::onPing', ['key' => $draft->id]), 'views: none');
    }

    public function test_a_list_widget_column_hidden_by_permissions_has_no_handlers(): void
    {
        $this->registerPingListWidget();

        $post = Post::factory()->create(['published' => true]);

        $response = $this->pingAjax('listSecret::onPing', ['key' => $post->id]);

        $this->assertNull(collect($response->json('__ajax.ops'))->firstWhere('op', 'flash'));
        $this->assertStringContainsString('listSecret', (string) $response->getContent());
    }

    public function test_a_late_extend_columns_listener_still_shapes_the_rendered_list(): void
    {
        $this->registerPingListWidget();

        Post::factory()->create(['title' => 'Shown', 'published' => true]);

        $html = $this->get('/list-widgets')->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/list-cell-name-late[^>]*>\s*PING late Shown/', $html);
        $this->assertStringNotContainsString('list-cell-name-secret', $html);
    }

    /**
     * registerPingListWidget registers the ping test widget and the page that lists posts with it.
     */
    protected function registerPingListWidget(): void
    {
        app('system.widgets')->registerListWidgets(function ($manager) {
            $manager->registerListWidget(PingTestListWidget::class, 'ping');
        });

        Route::middleware('web')->group(function () {
            Route::match(['get', 'post'], '/list-widgets', [ListWidgetTestController::class, 'index'])->name('list-widgets-test');
        });
    }

    /**
     * pingAjax posts an AJAX handler request to the ping list page, with a fresh controller so a second request in the test runs its own handler.
     *
     * @param  array<string, mixed>  $data
     */
    protected function pingAjax(string $handler, array $data = []): TestResponse
    {
        app('router')->getRoutes()->refreshNameLookups();
        app('router')->getRoutes()->getByName('list-widgets-test')->flushController();

        return $this->ajax($handler, '/list-widgets', $data);
    }

    /**
     * assertFlash checks an AJAX response carries an info flash message with the given text.
     */
    protected function assertFlash(TestResponse $response, string $text): void
    {
        $response->assertOk();

        $this->assertSame($text, collect($response->json('__ajax.ops'))->firstWhere('op', 'flash')['text'] ?? null);
    }

    public function test_a_list_widget_code_keeps_the_first_widget_registered_under_it(): void
    {
        app('system.widgets')->registerListWidgets(function ($manager) {
            $manager->registerListWidget(ShoutTestListWidget::class, 'switch');
            $manager->registerListWidget(ShoutTestListWidget::class, 'shout');
        });

        $this->assertSame(\Larajax\Ui\ListWidgets\SwitchColumn::class, WidgetManager::instance()->resolveListWidget('switch'));
        $this->assertSame(\App\ListWidgets\Meter::class, WidgetManager::instance()->resolveListWidget('meter'));
        $this->assertSame(ShoutTestListWidget::class, WidgetManager::instance()->resolveListWidget('shout'));
    }

    public function test_a_built_in_column_type_wins_over_a_list_widget_of_the_same_code(): void
    {
        app('system.widgets')->registerListWidgets(function ($manager) {
            $manager->registerListWidget(ShoutTestListWidget::class, 'text');
            $manager->registerListWidget(ShoutTestListWidget::class, 'shout');
        });

        $post = Post::factory()->create(['title' => 'Quiet']);

        $list = Lists::createIn(new ExtendingController, [
            'model' => new Post,
            'columns' => [
                'title' => ['label' => 'Title', 'type' => 'text'],
                'excerpt' => ['label' => 'Excerpt', 'type' => 'shout', 'sortable' => true],
            ],
        ]);

        $columns = $list->getColumns();

        $this->assertSame('Quiet', $list->getColumnValue($post, $columns['title']));
        $this->assertSame('SHOUT QUIET', $list->getColumnValue($post, $columns['excerpt']));
        $this->assertNull($list->getListWidget('title'));
        $this->assertInstanceOf(ShoutTestListWidget::class, $list->getListWidget('excerpt'));

        // The widget's column defaults apply only where the column does not set them
        $this->assertFalse($columns['excerpt']->clickable);
        $this->assertTrue($columns['excerpt']->sortable);
    }
}

/**
 * ListWidgetTestController lists published posts with ping columns: one defined at make(), one added by a listener bound after make(), and one hidden by permissions.
 */
class ListWidgetTestController extends ControllerBase
{
    /**
     * index builds the list and prints it.
     */
    public function index()
    {
        $list = Lists::make(
            model: new Post,
            columns: [
                'title' => ['label' => 'Title'],
                'views' => ['label' => 'Views', 'type' => 'ping'],
                'secret' => ['label' => 'Secret', 'type' => 'ping', 'permissions' => 'see-secrets'],
            ],
        );

        $list->addFilter(fn ($query) => $query->where('published', true));

        $list->bindEvent('list.extendColumns', function () use ($list) {
            $list->addColumns(['late' => ['label' => 'Late', 'type' => 'ping']]);
        });

        return response((string) $list->render());
    }
}

/**
 * PingTestListWidget is a list widget whose handler names its column and the record it finds through the list.
 */
class PingTestListWidget extends ListWidgetBase
{
    /**
     * renderValue names the column and the record's title.
     */
    public function renderValue($record, $value)
    {
        return 'PING '.$this->columnName.' '.e($record->title);
    }

    /**
     * onPing flashes the column and the title of the record found through the list query.
     */
    public function onPing()
    {
        return ajax()->flash('info', $this->columnName.': '.($this->findRecord(request()->input('key'))?->title ?? 'none'));
    }
}

/**
 * ShoutTestListWidget is a list widget that writes the record's title in capitals.
 */
class ShoutTestListWidget extends ListWidgetBase
{
    /**
     * columnDefaults keeps the column out of the row link and of sorting.
     */
    public static function columnDefaults(): array
    {
        return ['clickable' => false, 'sortable' => false];
    }

    /**
     * renderValue shouts the record's title.
     */
    public function renderValue($record, $value)
    {
        return 'SHOUT '.strtoupper($record->title);
    }
}

/**
 * WidgetAssetsTestController serves the test widgets on a page and through AJAX handlers.
 */
class WidgetAssetsTestController extends ControllerBase
{
    /**
     * index renders two color wheels in the content section of the Amber layout.
     */
    public function index()
    {
        $first = ColorWheelTestWidget::make();
        $second = ColorWheelTestWidget::make(alias: 'second');

        return response(Blade::render(<<<'BLADE'
            @extends('layouts.app')
            @section('content')
                {{ $first }}
                {{ $second }}
            @endsection
            BLADE, ['first' => $first, 'second' => $second]));
    }

    /**
     * blank is a page without widgets.
     */
    public function blank(): string
    {
        return 'blank';
    }

    /**
     * onLoadArray creates a color wheel and returns it in an array.
     */
    public function onLoadArray(): array
    {
        return ['result' => ColorWheelTestWidget::make()->render()];
    }

    /**
     * onLoadString creates a color wheel and returns its markup.
     */
    public function onLoadString(): string
    {
        return ColorWheelTestWidget::make()->render();
    }

    /**
     * onLoadResponse creates a color wheel and returns an AJAX response.
     */
    public function onLoadResponse()
    {
        return ajax()->data(['result' => ColorWheelTestWidget::make()->render()]);
    }

    /**
     * onNothing returns without creating a widget.
     */
    public function onNothing(): array
    {
        return ['result' => 'ok'];
    }
}

/**
 * ColorWheelTestWidget is a custom widget that loads its own assets and renders a nested widget.
 */
class ColorWheelTestWidget extends WidgetBase
{
    /**
     * loadAssets adds a stylesheet, a module script, a root-relative script and a global CDN script.
     */
    protected function loadAssets()
    {
        $this->addCss('css/colorwheel.css');
        $this->addJs('js/colorwheel.js', ['type' => 'module']);
        $this->addJs('/js/shared.js', ['defer' => true]);
        $this->addJs('https://cdn.example.com/wheel.js', 'global');
    }

    /**
     * render the color wheel with a swatch widget created at render time.
     */
    public function render()
    {
        return '<div data-control="colorwheel"></div>'.$this->makeWidget(SwatchTestWidget::class)->render();
    }
}

/**
 * PackagedColorWheelTestWidget resolves its relative asset names inside a published package folder.
 */
class PackagedColorWheelTestWidget extends ColorWheelTestWidget
{
    /**
     * @var string|null assetPath for the published package assets.
     */
    public $assetPath = 'vendor/acme/colorwheel';
}

/**
 * SwatchTestWidget is a widget that is only created while its parent renders.
 */
class SwatchTestWidget extends WidgetBase
{
    /**
     * loadAssets adds the swatch stylesheet.
     */
    protected function loadAssets()
    {
        $this->addCss('css/swatch.css');
    }

    /**
     * render the swatch.
     */
    public function render()
    {
        return '<span class="swatch"></span>';
    }
}
