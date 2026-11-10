<?php

namespace Tests\Feature\Reference\Ui;

use DateTimeImmutable;
use DateTimeZone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Larajax\Ui\Facades\Ui;
use Tests\TestCase;

class ChartsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * NOV_1 is midnight on the 1st of November 2026 in UTC, in milliseconds.
     */
    protected const NOV_1 = 1793491200000;

    protected const DAY = 86400000;

    public function test_the_page_renders_every_example_beside_its_source(): void
    {
        $html = $this->get(route('reference.ui.charts'))->assertOk()->getContent();

        $examples = ['chart-pie', 'chart-values', 'chart-bar', 'chart-line', 'chart-zoom', 'chart-config', 'chart-events', 'chart-markup'];

        foreach ($examples as $example) {
            $source = e(trim(file_get_contents(resource_path("views/reference/ui/examples/{$example}.blade.php"))));
            $this->assertStringContainsString('<pre><code>'.$source.'</code></pre>', $html, "{$example} is not shown.");
            $this->assertStringContainsString('id="'.$example.'"', $html);
        }
    }

    public function test_the_page_renders_each_kind_of_chart(): void
    {
        $html = $this->get(route('reference.ui.charts'))->assertOk()->getContent();

        $this->assertStringContainsString('<div class="control-chart" data-control="chart-pie" data-size="160" data-center-text="114">', $html);
        $this->assertStringContainsString('<div class="control-chart centered wrap-legend" data-control="chart-pie" data-size="160">', $html);
        $this->assertStringContainsString('<div class="control-chart wrap-legend" data-control="chart-bar" data-height="100" data-full-width="1">', $html);
        $this->assertStringContainsString('data-control="chart-line" data-time-mode="weeks" style="height: 260px"', $html);
        $this->assertStringContainsString('data-zoomable="1" data-reset-zoom-link="#chartResetZoom"', $html);
        $this->assertStringContainsString('data-chart-options="xaxis: {mode: \'time\', minTickSize: [1, \'day\']}, legend: {show: false}"', $html);
        $this->assertStringContainsString("addEventListener('chart-bar:ready'", $html);
    }

    public function test_values_take_the_forms_of_an_options_list(): void
    {
        $html = Ui::pieChart(values: [
            'Published' => 84,
            'Drafts' => [12, '#e5a91a'],
            ['label' => 'Deleted', 'value' => 18, 'color' => '#cc3300'],
            '<b>Bold</b>' => '7.5',
            'Unknown' => 'n/a',
        ])->toHtml();

        $this->assertMatchesRegularExpression('/<li>\s*Published\s*<span>84<\/span>\s*<\/li>/', $html);
        $this->assertMatchesRegularExpression('/<li data-color="#e5a91a">\s*Drafts\s*<span>12<\/span>/', $html);
        $this->assertMatchesRegularExpression('/<li data-color="#cc3300">\s*Deleted\s*<span>18<\/span>/', $html);
        $this->assertMatchesRegularExpression('/<li>\s*&lt;b&gt;Bold&lt;\/b&gt;\s*<span>7.5<\/span>/', $html);
        $this->assertMatchesRegularExpression('/<li>\s*Unknown\s*<span>0<\/span>/', $html);
    }

    public function test_a_value_color_that_is_not_a_color_is_left_out(): void
    {
        $html = Ui::barChart(values: ['A' => [1, 'red'], 'B' => [2, '"><script>']])->toHtml();

        $this->assertStringNotContainsString('data-color', $html);
        $this->assertStringNotContainsString('<script>', $html);
    }

    public function test_pie_options_become_data_attributes(): void
    {
        $html = Ui::pieChart(values: ['A' => 1], size: 120, centerText: '<b>9</b>', centered: true, wrapLegend: true, config: ['cutout' => '50%'], id: 'visits')->toHtml();

        $this->assertStringContainsString('<div class="control-chart centered wrap-legend" data-control="chart-pie" data-size="120" data-center-text="&lt;b&gt;9&lt;/b&gt;" data-chart-config="{&quot;cutout&quot;:&quot;50%&quot;}" id="visits">', $html);
    }

    public function test_a_bar_chart_sizes_itself_outside_a_scoreboard(): void
    {
        $html = Ui::barChart(values: ['A' => 1])->toHtml();
        $this->assertStringContainsString('<div class="control-chart" data-control="chart-bar" data-height="100" style="height: 100px">', $html);

        $html = Ui::barChart(values: ['A' => 1], height: 60, fullWidth: true)->toHtml();
        $this->assertStringContainsString('<div class="control-chart" data-control="chart-bar" data-height="60" data-full-width="1">', $html);
    }

    public function test_line_points_are_utc_timestamps_from_dates_pairs_and_numbers(): void
    {
        $html = Ui::lineChart(datasets: [
            'Visits' => ['2026-11-01' => 400, '2026-11-02' => '380'],
            'Orders' => ['color' => '#95b753', 'data' => [
                [new DateTimeImmutable('2026-11-01 06:00', new DateTimeZone('Australia/Sydney')), 3],
                [self::NOV_1 + self::DAY, 4],
                ['not a date', 'n/a'],
            ]],
        ])->toHtml();

        $this->assertStringContainsString('data-set-name="Visits" data-set-data="['.self::NOV_1.',400],['.(self::NOV_1 + self::DAY).',380]"', $html);

        // A date keeps its own clock time, so 6am in Sydney shows as 6am
        $this->assertStringContainsString('data-set-name="Orders" data-set-color="#95b753" data-set-data="['.(self::NOV_1 + 6 * 3600000).',3],['.(self::NOV_1 + self::DAY).',4],[null,null]"', $html);
    }

    public function test_a_line_data_set_takes_a_label_or_name_and_flot_options(): void
    {
        $html = Ui::lineChart(datasets: [
            ['label' => 'Amount "net"', 'data' => [[1, 2]], 'lines' => ['fill' => false], 'points' => ['show' => false]],
            ['name' => 'Units', 'color' => 'not a color', 'data' => []],
        ])->toHtml();

        $this->assertStringContainsString('data-set-name="Amount &quot;net&quot;" data-set-lines="{&quot;fill&quot;:false}" data-set-points="{&quot;show&quot;:false}" data-set-data="[1,2]"', $html);
        $this->assertStringContainsString('<span data-chart="dataset" data-set-name="Units" data-set-data=""></span>', $html);
    }

    public function test_line_options_become_data_attributes(): void
    {
        $html = Ui::lineChart(height: '20rem', timeMode: 'weeks', zoomable: true, resetZoomLink: '#reset', config: ['scales' => ['y' => ['beginAtZero' => true]]])->toHtml();

        $this->assertStringContainsString('<div data-control="chart-line" data-zoomable="1" data-reset-zoom-link="#reset" data-time-mode="weeks" data-chart-config="{&quot;scales&quot;:{&quot;y&quot;:{&quot;beginAtZero&quot;:true}}}" style="height: 20rem">', $html);
        $this->assertStringContainsString('<div data-control="chart-line" style="height: 250px">', Ui::lineChart()->toHtml());
    }
}
