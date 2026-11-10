<?php

namespace Tests\Feature\Reference\Ui;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Larajax\Ui\Facades\Ui;
use Tests\TestCase;

class ScoreboardsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * capture runs a callback and returns what it printed, for components written as blocks.
     */
    protected function capture(callable $callback): string
    {
        ob_start();
        $callback();

        return ob_get_clean();
    }

    public function test_the_page_renders_every_example_beside_its_source(): void
    {
        $html = $this->get(route('reference.ui.scoreboards'))->assertOk()->getContent();

        $examples = [
            'scoreboard-figures', 'scoreboard-trends', 'scoreboard-markers', 'scoreboard-meters', 'scoreboard-links',
            'scoreboard-slots', 'scoreboard-thumbnail', 'scoreboard-charts', 'scoreboard-markup',
        ];

        foreach ($examples as $example) {
            $source = e(trim(file_get_contents(resource_path("views/reference/ui/examples/{$example}.blade.php"))));
            $this->assertStringContainsString('<pre><code>'.$source.'</code></pre>', $html, "{$example} is not shown.");
            $this->assertStringContainsString('id="'.$example.'"', $html);
        }
    }

    public function test_the_page_renders_figures_markers_meters_and_charts(): void
    {
        $html = $this->get(route('reference.ui.scoreboards'))->assertOk()->getContent();

        foreach (['positive', 'negative', 'success', 'danger'] as $trend) {
            $this->assertStringContainsString('<p class="'.$trend.'">', $html);
        }

        $this->assertStringContainsString('<i class="ph ph-star" aria-hidden="true"></i>', $html);
        $this->assertStringContainsString('<span class="scoreboard-colorpicker" style="--background-color: var(--bs-success-base); --foreground-color: var(--bs-success-base)"></span>', $html);
        $this->assertStringContainsString('<img class="scoreboard-image" src="'.asset('images/acme-supply.svg').'" alt="" />', $html);
        $this->assertStringContainsString('class="scoreboard-item title-value goal-meter-inverse" data-control="goal-meter" data-value="3"', $html);
        $this->assertStringContainsString('<img src="'.asset('images/desk-lamp.svg').'" alt="Desk lamp" />', $html);
        $this->assertStringContainsString('class="scoreboard-item control-chart" data-control="chart-pie"', $html);
        $this->assertStringContainsString('class="scoreboard-item control-chart" data-control="chart-bar"', $html);
    }

    public function test_the_scoreboard_wraps_its_items_in_a_drag_scrolling_row(): void
    {
        $html = $this->capture(function () {
            Ui::scoreboard(id: 'stats')->slot();
            echo Ui::scoreboardItem(label: 'Orders', value: 12);
            Ui::end();
        });

        $this->assertMatchesRegularExpression('/^<div class="scoreboard" id="stats">\s*<div data-control="toolbar">\s*<div class="scoreboard-item title-value">/', $html);
    }

    public function test_an_item_escapes_its_text_and_leaves_out_what_is_empty(): void
    {
        $html = Ui::scoreboardItem(label: '<b>Orders</b>', value: '<i>12</i>', comment: '<u>this month</u>')->toHtml();

        $this->assertMatchesRegularExpression('/<div class="scoreboard-label">\s*&lt;b&gt;Orders&lt;\/b&gt;\s*<\/div>/', $html);
        $this->assertMatchesRegularExpression('/<p>\s*&lt;i&gt;12&lt;\/i&gt;\s*<\/p>/', $html);
        $this->assertMatchesRegularExpression('/<p class="description">\s*&lt;u&gt;this month&lt;\/u&gt;\s*<\/p>/', $html);

        $html = Ui::scoreboardItem(value: 12)->toHtml();

        $this->assertStringNotContainsString('scoreboard-label', $html);
        $this->assertStringNotContainsString('description', $html);
        $this->assertStringNotContainsString('scoreboard-trend', $html);
        $this->assertStringNotContainsString('<svg', $html);
        $this->assertStringNotContainsString('<a', $html);
    }

    public function test_success_and_danger_color_the_figure(): void
    {
        $this->assertMatchesRegularExpression('/<p class="success">\s*1/', Ui::scoreboardItem(value: 1, success: true, danger: true)->toHtml());
        $this->assertMatchesRegularExpression('/<p class="danger">\s*1/', Ui::scoreboardItem(value: 1, danger: true)->toHtml());
    }

    public function test_a_trend_shows_beside_the_figure_in_a_pill(): void
    {
        $html = Ui::scoreboardItem(value: '$48,210', change: '14.9%', positive: true, negative: true)->toHtml();

        $this->assertMatchesRegularExpression('/<p>\s*\$48,210\s*<span class="scoreboard-trend is-positive">\s*<i class="ph ph-arrow-up-right" aria-hidden="true"><\/i>\s*<span class="visually-hidden">Increased<\/span>\s*14.9%\s*<\/span>\s*<\/p>/', $html);

        $html = Ui::scoreboardItem(value: 31, change: '<b>26%</b>', negative: true)->toHtml();

        $this->assertStringContainsString('<span class="scoreboard-trend is-negative">', $html);
        $this->assertStringContainsString('<i class="ph ph-arrow-down-right" aria-hidden="true"></i>', $html);
        $this->assertStringContainsString('<span class="visually-hidden">Decreased</span>', $html);
        $this->assertStringContainsString('&lt;b&gt;26%&lt;/b&gt;', $html);

        // A change on its own is a grey pill, and a direction on its own an arrow
        $this->assertMatchesRegularExpression('/<span class="scoreboard-trend">\s*0.4%\s*<\/span>/', Ui::scoreboardItem(value: 1, change: '0.4%')->toHtml());
        $this->assertMatchesRegularExpression('/<span class="scoreboard-trend is-positive">\s*<i[^>]*><\/i>\s*<span class="visually-hidden">Increased<\/span>\s*<\/span>/', Ui::scoreboardItem(value: 1, positive: true)->toHtml());
    }

    public function test_a_unit_shows_after_the_figure(): void
    {
        $this->assertMatchesRegularExpression('/<p>\s*312\s*<span class="scoreboard-unit">&lt;ms&gt;<\/span>\s*<\/p>/', Ui::scoreboardItem(value: 312, unit: '<ms>')->toHtml());
    }

    public function test_a_null_value_shows_there_is_no_data(): void
    {
        $html = Ui::scoreboardItem(label: 'Churn', value: null, unit: '%', change: '3%', positive: true)->toHtml();

        $this->assertMatchesRegularExpression('/<p>\s*<span class="scoreboard-empty">No data<\/span>\s*<\/p>/', $html);
        $this->assertStringNotContainsString('scoreboard-unit', $html);
        $this->assertStringNotContainsString('scoreboard-trend', $html);

        // A meter stands in for the figure, and an empty value is left blank
        $this->assertMatchesRegularExpression('/<p>\s*40%\s*<\/p>/', Ui::scoreboardItem(value: null, meter: 40)->toHtml());
        $this->assertStringNotContainsString('No data', Ui::scoreboardItem(value: '')->toHtml());
    }

    public function test_compare_works_out_the_change_and_its_direction(): void
    {
        $html = Ui::scoreboardItem(value: '$48,210', compare: [48210, 41950])->toHtml();
        $this->assertMatchesRegularExpression('/<span class="scoreboard-trend is-positive">\s*<i class="ph ph-arrow-up-right"[^>]*><\/i>\s*<span class="visually-hidden">Increased<\/span>\s*14.9%/', $html);

        $html = Ui::scoreboardItem(value: 31, compare: [31, 42])->toHtml();
        $this->assertMatchesRegularExpression('/<span class="scoreboard-trend is-negative">\s*<i class="ph ph-arrow-down-right"[^>]*><\/i>\s*<span class="visually-hidden">Decreased<\/span>\s*26.2%/', $html);

        $this->assertMatchesRegularExpression('/<span class="scoreboard-trend">\s*0%\s*<\/span>/', Ui::scoreboardItem(value: 4, compare: [4, 4])->toHtml());
        $this->assertMatchesRegularExpression('/>\s*30%\s*<\/span>/', Ui::scoreboardItem(value: 13, compare: [13, 10])->toHtml());

        // From nothing there is only a direction, and values that are not numbers give nothing
        $this->assertMatchesRegularExpression('/<span class="visually-hidden">Increased<\/span>\s*<\/span>/', Ui::scoreboardItem(value: 5, compare: [5, 0])->toHtml());
        $this->assertStringNotContainsString('scoreboard-trend', Ui::scoreboardItem(value: 5, compare: ['five', 4])->toHtml());

        // A change or direction that is set wins
        $this->assertMatchesRegularExpression('/>\s*about 10%\s*<\/span>/', Ui::scoreboardItem(value: 5, change: 'about 10%', compare: [5, 4])->toHtml());
        $this->assertStringContainsString('is-negative', Ui::scoreboardItem(value: 5, negative: true, compare: [5, 4])->toHtml());
    }

    public function test_inverse_tints_a_fall_green_and_a_rise_red(): void
    {
        $html = Ui::scoreboardItem(value: 312, compare: [312, 340], inverse: true, sparkline: [340, 312])->toHtml();

        $this->assertStringContainsString('<span class="scoreboard-trend is-positive">', $html);
        $this->assertStringContainsString('<i class="ph ph-arrow-down-right" aria-hidden="true"></i>', $html);
        $this->assertStringContainsString('<span class="visually-hidden">Decreased</span>', $html);
        $this->assertStringContainsString('<svg class="scoreboard-sparkline is-positive"', $html);

        $html = Ui::scoreboardItem(value: 9, positive: true, inverse: true)->toHtml();

        $this->assertStringContainsString('<span class="scoreboard-trend is-negative">', $html);
        $this->assertStringContainsString('<i class="ph ph-arrow-up-right" aria-hidden="true"></i>', $html);
    }

    public function test_a_sparkline_draws_recent_values_scaled_between_the_lowest_and_highest(): void
    {
        $html = Ui::scoreboardItem(value: 1, sparkline: [10, 20, 15, 'n/a', 30], positive: true)->toHtml();

        $this->assertStringContainsString('<svg class="scoreboard-sparkline is-positive" viewBox="0 0 100 24" preserveAspectRatio="none" aria-hidden="true">', $html);
        $this->assertStringContainsString('<polyline points="0,23 33.33,12 66.67,17.5 100,1" />', $html);
        $this->assertStringContainsString('<polygon points="0,24 0,23 33.33,12 66.67,17.5 100,1 100,24" />', $html);
        $this->assertStringContainsString('<path class="scoreboard-sparkline-dot" d="M100,1h0" />', $html);

        $this->assertStringContainsString('<polyline points="0,12 100,12" />', Ui::scoreboardItem(value: 1, sparkline: [5, 5.0])->toHtml());
        $this->assertStringNotContainsString('<svg', Ui::scoreboardItem(value: 1, sparkline: [5])->toHtml());
    }

    public function test_an_icon_shows_in_a_tile_tinted_by_the_color(): void
    {
        $html = Ui::scoreboardItem(label: 'Customers', value: 12, icon: 'ph ph-users')->toHtml();

        $this->assertMatchesRegularExpression('/<div class="scoreboard-item title-value has-icon">\s*<span class="scoreboard-icon">\s*<i class="ph ph-users" aria-hidden="true"><\/i>\s*<\/span>\s*<div class="scoreboard-body">\s*<div class="scoreboard-label">/', $html);

        $html = Ui::scoreboardItem(value: 12, icon: 'ph ph-star', color: 'var(--bs-warning-base)')->toHtml();

        $this->assertStringContainsString('<span class="scoreboard-icon is-tinted" style="--scoreboard-icon-tint: var(--bs-warning-base)">', $html);
        $this->assertStringNotContainsString('scoreboard-colorpicker', $html);
    }

    public function test_an_href_makes_the_whole_item_a_link(): void
    {
        $html = Ui::scoreboardItem(label: 'Open "orders"', value: 18, href: 'samples?status=open')->toHtml();

        $this->assertStringContainsString('<div class="scoreboard-item title-value has-link">', $html);
        $this->assertStringContainsString('<a class="scoreboard-link" href="'.url('samples?status=open').'" aria-label="Open &quot;orders&quot;"></a>', $html);

        // A scheme link that points nowhere leaves the item without a link
        app('larajax.ui')->resolveLinkUsing('page', fn (string $link) => $link === 'page://about' ? url('about-us') : null);
        $this->assertStringContainsString('href="'.url('about-us').'"', Ui::scoreboardItem(value: 1, href: 'page://about')->toHtml());
        $this->assertStringNotContainsString('scoreboard-link', Ui::scoreboardItem(value: 1, href: 'page://missing')->toHtml());
    }

    public function test_a_tooltip_adds_a_help_icon_beside_the_title(): void
    {
        $html = Ui::scoreboardItem(label: 'Conversion', value: '3.4%', tooltip: 'Orders "placed" over visits.')->toHtml();

        $this->assertMatchesRegularExpression('/<div class="scoreboard-label">\s*Conversion\s*<span class="scoreboard-help" data-tooltip-text="Orders &quot;placed&quot; over visits." data-tooltip-position="top" tabindex="0" aria-label="Orders &quot;placed&quot; over visits.">\s*<i class="ph ph-info" aria-hidden="true"><\/i>\s*<\/span>\s*<\/div>/', $html);
    }

    public function test_one_marker_shows_with_an_icon_first_then_a_color_then_an_image(): void
    {
        $html = Ui::scoreboardItem(value: 'Shipped', icon: 'ph ph-truck', image: '/logo.png')->toHtml();
        $this->assertStringContainsString('<i class="ph ph-truck" aria-hidden="true"></i>', $html);
        $this->assertStringNotContainsString('<img', $html);

        $html = Ui::scoreboardItem(value: 'Shipped', color: 'rgb(149, 183, 83)', image: '/logo.png')->toHtml();
        $this->assertStringContainsString('--background-color: rgb(149, 183, 83)', $html);
        $this->assertStringNotContainsString('<img', $html);

        $html = Ui::scoreboardItem(value: 'Acme', image: '/logo.png?size="s"')->toHtml();
        $this->assertStringContainsString('<img class="scoreboard-image" src="/logo.png?size=&quot;s&quot;" alt="" />', $html);
    }

    public function test_a_color_that_is_not_a_plain_color_is_left_out(): void
    {
        foreach (['red', '#fff; background: url(/track)', 'var(--x);}'] as $color) {
            $this->assertStringNotContainsString('scoreboard-colorpicker', Ui::scoreboardItem(value: 1, color: $color)->toHtml(), $color);
        }
    }

    public function test_a_meter_adds_the_goal_meter_and_shows_as_the_figure_without_a_value(): void
    {
        $html = Ui::scoreboardItem(label: 'Open rate', meter: 64)->toHtml();

        $this->assertStringContainsString('<div class="scoreboard-item title-value" data-control="goal-meter" data-value="64">', $html);
        $this->assertMatchesRegularExpression('/<p>\s*64%\s*<\/p>/', $html);

        $html = Ui::scoreboardItem(value: '37 of 50', meter: 74, inverse: true)->toHtml();

        $this->assertStringContainsString('class="scoreboard-item title-value goal-meter-inverse"', $html);
        $this->assertMatchesRegularExpression('/<p>\s*37 of 50\s*<\/p>/', $html);

        $this->assertStringNotContainsString('goal-meter', Ui::scoreboardItem(value: 1, inverse: true)->toHtml());
    }

    public function test_the_value_and_comment_slots_take_markup(): void
    {
        $html = $this->capture(function () {
            Ui::scoreboardItem(label: 'Customer', value: 'unused')->slot();
            echo '<a href="/customers/7">Jane</a>';
            Ui::slot('comment');
            echo 'Email: <a href="mailto:jane@example.com">jane@example.com</a>';
            Ui::end();
        });

        $this->assertMatchesRegularExpression('/<p>\s*<a href="\/customers\/7">Jane<\/a>\s*<\/p>/', $html);
        $this->assertStringNotContainsString('unused', $html);
        $this->assertMatchesRegularExpression('/<p class="description">\s*Email: <a href="mailto:jane@example.com">/', $html);

        $html = $this->capture(function () {
            Ui::scoreboardItem(label: 'Total', value: '$10', comment: 'unused')->slot('comment');
            echo 'Tax: <b>$1</b>';
            Ui::end();
        });

        $this->assertMatchesRegularExpression('/<p>\s*\$10\s*<\/p>/', $html);
        $this->assertMatchesRegularExpression('/<p class="description">\s*Tax: <b>\$1<\/b>\s*<\/p>/', $html);
    }

    public function test_a_thumbnail_is_an_image_item(): void
    {
        $html = Ui::scoreboardThumbnail(image: '/lamp.jpg', label: 'Desk "lamp"', class: 'me-3')->toHtml();

        $this->assertMatchesRegularExpression('/<div class="scoreboard-item thumbnail-value me-3">\s*<img src="\/lamp.jpg" alt="Desk &quot;lamp&quot;" \/>\s*<\/div>/', $html);
    }

    public function test_a_chart_is_an_item_only_directly_inside_a_scoreboard(): void
    {
        $html = $this->capture(function () {
            Ui::scoreboard()->slot();
            echo Ui::pieChart(values: ['A' => 1]);
            echo Ui::barChart(values: ['A' => 1]);
            Ui::scoreboardItem(label: 'Nested')->slot();
            echo Ui::pieChart(values: ['B' => 1]);
            Ui::end();
            Ui::end();
        });

        $this->assertStringContainsString('<div class="scoreboard-item control-chart" data-control="chart-pie">', $html);
        $this->assertStringContainsString('<div class="scoreboard-item control-chart" data-control="chart-bar">', $html);
        $this->assertStringContainsString('<div class="control-chart" data-control="chart-pie" data-size="200">', $html);

        $this->assertStringContainsString('<div class="control-chart" data-control="chart-pie" data-size="200">', Ui::pieChart(values: ['A' => 1])->toHtml());
    }
}
