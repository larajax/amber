<?php

namespace Tests\Feature\Reference\Ui;

use Carbon\CarbonInterval;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Sleep;
use Illuminate\Testing\TestResponse;
use Larajax\Ui\Facades\Ui;
use Tests\TestCase;

class FeedbackTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Sleep::fake();
    }

    /**
     * ajax posts an AJAX handler request to the Feedback page.
     *
     * @param  array<string, mixed>  $data
     */
    protected function ajax(string $handler, array $data = []): TestResponse
    {
        return $this->withHeaders([
            'X-AJAX-HANDLER' => $handler,
            'X-Requested-With' => 'XMLHttpRequest',
        ])->post(route('reference.ui.feedback'), $data);
    }

    /**
     * assertFlash checks the response carries one flash message with the given level and text.
     */
    protected function assertFlash(TestResponse $response, string $level, string $text): void
    {
        $response->assertOk();

        $flash = collect($response->json('__ajax.ops'))->firstWhere('op', 'flash');

        $this->assertNotNull($flash, 'The response has no flash message.');
        $this->assertSame($level, $flash['level']);
        $this->assertSame($text, $flash['text']);
    }

    public function test_the_page_renders_every_example_beside_its_source(): void
    {
        $html = $this->get(route('reference.ui.feedback'))->assertOk()->getContent();

        $examples = [
            'callout-types', 'callout-body', 'callout-action', 'callout-icons', 'callout-dismiss', 'status-list',
            'loader-container', 'load-indicator-markup', 'load-indicator-api', 'change-monitor',
        ];

        foreach ($examples as $example) {
            $source = e(trim(file_get_contents(resource_path("views/reference/ui/examples/{$example}.blade.php"))));
            $this->assertStringContainsString('<pre><code>'.$source.'</code></pre>', $html, "{$example} is not shown.");
        }

        foreach (['callouts', 'callout-body', 'callout-action', 'callout-icons', 'callout-dismiss', 'status-list', 'loader-container', 'load-indicator', 'load-indicator-api', 'change-monitor'] as $section) {
            $this->assertStringContainsString('id="'.$section.'"', $html);
        }

        $this->assertStringContainsString('resources/views/reference/ui/_callout_checklist.blade.php', $html);
    }

    public function test_the_page_renders_callouts_and_states(): void
    {
        $html = $this->get(route('reference.ui.feedback'))->assertOk()->getContent();

        foreach (['info', 'success', 'warning', 'danger'] as $type) {
            $this->assertStringContainsString('callout-'.$type, $html);
        }

        $this->assertStringContainsString('<i class="ph ph-warning-octagon" aria-hidden="true"></i>', $html);
        $this->assertStringContainsString('<i class="ph ph-rocket-launch" aria-hidden="true"></i>', $html);
        $this->assertStringContainsString('Two teammates accepted their invites.', $html);
        $this->assertMatchesRegularExpression('/<div class="action">\s*<button class="btn-solid theme-primary" type="button" data-request="onCalloutAction"[^>]*>\s*Add a card\s*<\/button>\s*<\/div>/', $html);
        $this->assertMatchesRegularExpression('/<div class="content">\s*<p>\s*Add a payment method to keep your projects\./', $html);
        $this->assertStringContainsString('data-request="onCalloutAction"', $html);
        $this->assertStringContainsString('<div class="control-status-list">', $html);
        $this->assertStringContainsString('data-control="loader-container"', $html);
        $this->assertStringContainsString('class="loading-indicator size-small"', $html);
        $this->assertStringContainsString('class="loading-indicator indicator-center"', $html);
        $this->assertStringContainsString("import { LoadIndicator } from '".asset('vendor/larajax/ui/index.js')."';", $html);
        $this->assertStringContainsString('<form data-change-monitor', $html);
        $this->assertStringContainsString('data-change-monitor-commit', $html);
    }

    /**
     * capture runs a callback and returns what it printed, for callouts written as blocks.
     */
    protected function capture(callable $callback): string
    {
        ob_start();
        $callback();

        return ob_get_clean();
    }

    public function test_a_callout_renders_its_label_and_comment_in_the_header(): void
    {
        $html = Ui::callout(label: 'Heads up', comment: 'Info is the default type.')->toHtml();

        $this->assertStringContainsString('<div class="callout fade show callout-info">', $html);
        $this->assertMatchesRegularExpression('/<div class="header">.*<h3>\s*Heads up\s*<\/h3>\s*<p>\s*Info is the default type.\s*<\/p>\s*<\/div>/s', $html);
        $this->assertStringNotContainsString('class="content"', $html);
    }

    public function test_the_label_and_comment_are_escaped_unless_the_comment_is_html(): void
    {
        $html = Ui::callout(label: '<b>Label</b>', comment: '<i>Comment</i>')->toHtml();

        $this->assertStringContainsString('&lt;b&gt;Label&lt;/b&gt;', $html);
        $this->assertStringContainsString('&lt;i&gt;Comment&lt;/i&gt;', $html);

        $html = Ui::callout(label: 'Release', comment: '<p>Read the <strong>notes</strong>.</p>', commentHtml: true)->toHtml();

        $this->assertMatchesRegularExpression('/<\/h3>\s*<p>Read the <strong>notes<\/strong>.<\/p>\s*<\/div>/', $html);
    }

    public function test_a_slot_captures_the_body_until_the_end(): void
    {
        $html = $this->capture(function () {
            Ui::callout(label: 'Release notes')->slot();
            echo '<p>Plain callout body</p>';
            Ui::end();
        });

        $this->assertMatchesRegularExpression('/<div class="content">\s*<p>Plain callout body<\/p>\s*<\/div>/', $html);
        $this->assertSame(1, substr_count($html, 'Plain callout body'));
    }

    public function test_a_body_can_include_a_view(): void
    {
        $html = $this->capture(function () {
            Ui::callout(label: 'Setup complete', success: true)->slot();
            echo view('reference.ui._callout_checklist')->render();
            Ui::end();
        });

        $this->assertStringContainsString('callout-success', $html);
        $this->assertStringContainsString('Two teammates accepted their invites.', $html);
    }

    public function test_a_block_captures_the_body_and_the_action_slot(): void
    {
        $html = $this->capture(function () {
            Ui::callout(label: 'Card expired', danger: true)->slot();
            echo '<p>Update your card.</p>';
            Ui::slot('action');
            echo Ui::ajaxButton(label: 'Update', handler: 'onUpdate', danger: true);
            Ui::end();
        });

        $this->assertStringContainsString('callout-danger', $html);
        $this->assertMatchesRegularExpression('/<div class="content">\s*<p>Update your card.<\/p>\s*<\/div>/', $html);
        $this->assertMatchesRegularExpression('/<div class="action">\s*<button class="btn-solid theme-danger" type="button" data-request="onUpdate">/', $html);
        $this->assertSame(1, substr_count($html, 'Update your card.'));
    }

    public function test_a_callout_can_start_with_the_action_slot(): void
    {
        $html = $this->capture(function () {
            Ui::callout(label: 'Card expired', danger: true)->slot('action');
            echo Ui::ajaxButton(label: 'Update', handler: 'onUpdate', danger: true);
            Ui::end();
        });

        $this->assertMatchesRegularExpression('/<div class="action">\s*<button class="btn-solid theme-danger" type="button" data-request="onUpdate">/', $html);
        $this->assertStringNotContainsString('class="content"', $html);
    }

    public function test_an_empty_block_renders_no_content(): void
    {
        $html = $this->capture(function () {
            Ui::callout(label: 'Heads up')->slot();
            echo "\n    \n";
            Ui::end();
        });

        $this->assertMatchesRegularExpression('/<h3>\s*Heads up\s*<\/h3>/', $html);
        $this->assertStringNotContainsString('class="content"', $html);
    }

    public function test_the_dismiss_example_closes_its_own_callout_and_another_by_target(): void
    {
        $html = $this->get(route('reference.ui.feedback'))->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/<div class="callout fade show callout-success no-icon no-subheader">\s*<button type="button" class="btn-close" data-dismiss="callout" aria-label="Close"><\/button>/', $html);
        $this->assertStringContainsString('<div id="calloutTip" class="callout fade show callout-info no-icon no-subheader">', $html);
        $this->assertMatchesRegularExpression('/class="btn-solid theme-secondary"[^>]*data-dismiss="callout"[^>]*data-target="#calloutTip"/', $html);
    }

    public function test_each_type_shows_its_icon_from_the_pack(): void
    {
        $callouts = [
            'info' => Ui::callout(label: 'Info'),
            'success' => Ui::callout(label: 'Success', success: true),
            'warning' => Ui::callout(label: 'Warning', warning: true),
            'danger' => Ui::callout(label: 'Danger', danger: true),
        ];

        foreach ($callouts as $type => $callout) {
            $html = $callout->toHtml();

            $this->assertStringContainsString('callout-'.$type, $html);
            $this->assertStringContainsString('<div class="custom-icon">', $html);
            $this->assertStringContainsString(Ui::icon('callout.'.$type), $html);
            $this->assertStringNotContainsString('no-icon', $html);
        }
    }

    public function test_a_custom_icon_replaces_the_type_icon(): void
    {
        $html = Ui::callout(label: 'Launch', warning: true, icon: 'ph ph-rocket-launch')->toHtml();

        $this->assertStringContainsString('<i class="ph ph-rocket-launch" aria-hidden="true"></i>', $html);
        $this->assertStringContainsString('has-custom-icon', $html);
        $this->assertStringNotContainsString(Ui::icon('callout.warning'), $html);
    }

    public function test_the_icon_can_be_turned_off(): void
    {
        $html = Ui::callout(label: 'Quiet', icon: false)->toHtml();

        $this->assertStringNotContainsString('custom-icon', $html);
        $this->assertStringContainsString('no-icon', $html);
    }

    public function test_a_callout_with_only_a_body_has_no_header_or_icon(): void
    {
        $html = $this->capture(function () {
            Ui::callout(warning: true)->slot();
            echo '<p>Body only</p>';
            Ui::end();
        });

        $this->assertStringNotContainsString('class="header"', $html);
        $this->assertStringContainsString('no-icon', $html);
        $this->assertStringContainsString('no-title', $html);
        $this->assertStringContainsString('no-subheader', $html);
    }

    public function test_other_arguments_render_as_attributes_with_the_class_merged(): void
    {
        $html = Ui::callout(label: 'Flush', class: 'is-flush', id: 'billing-callout')->toHtml();

        $this->assertMatchesRegularExpression('/<div class="callout fade show callout-info no-subheader is-flush" id="billing-callout">/', $html);
    }

    public function test_on_callout_action_flashes_a_message(): void
    {
        $this->assertFlash($this->ajax('onCalloutAction'), 'info', 'Payment details are not part of this demo.');
    }

    public function test_on_slow_request_sleeps_before_flashing(): void
    {
        $this->assertFlash($this->ajax('onSlowRequest'), 'success', 'The slow request finished.');

        Sleep::assertSlept(fn (CarbonInterval $duration) => (int) $duration->totalMilliseconds === 1500);
    }

    public function test_on_save_profile_flashes_a_message(): void
    {
        $response = $this->ajax('onSaveProfile', ['display_name' => 'Ada Lovelace', 'is_public' => '1']);

        $this->assertFlash($response, 'success', 'Profile saved.');
    }

    public function test_the_page_renders_the_status_list(): void
    {
        $html = $this->get(route('reference.ui.feedback'))->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/<span class="status-icon success">\s*<i class="ph ph-check" aria-hidden="true"><\/i>\s*<\/span>\s*<span class="status-text success">\s*Software is up to date\s*<\/span>\s*<a href="#status-list" class="status-label link">\s*Check again\s*<\/a>/', $html);
        $this->assertMatchesRegularExpression('/<span class="status-text muted">\s*Online since\s*<\/span>\s*<span class="status-label">\s*4 April 2026\s*<\/span>/', $html);
    }

    public function test_a_status_list_holds_its_items(): void
    {
        $html = $this->capture(function () {
            Ui::statusList(id: 'health')->slot();
            echo Ui::statusItem(label: 'System build', value: '313');
            Ui::end();
        });

        $this->assertMatchesRegularExpression('/^<div class="control-status-list" id="health">\s*<ul>\s*<li>\s*<span class="status-text">\s*System build\s*<\/span>\s*<span class="status-label">\s*313\s*<\/span>\s*<\/li>\s*<\/ul>\s*<\/div>/', $html);
    }

    public function test_a_status_item_escapes_its_text_and_takes_one_type(): void
    {
        $html = Ui::statusItem(label: '<b>Disk</b>', value: '<i>90%</i>', icon: 'ph ph-hard-drives', danger: true, warning: true)->toHtml();

        $this->assertStringContainsString('<span class="status-icon warning">', $html);
        $this->assertMatchesRegularExpression('/<span class="status-text warning">\s*&lt;b&gt;Disk&lt;\/b&gt;\s*<\/span>/', $html);
        $this->assertMatchesRegularExpression('/<span class="status-label">\s*&lt;i&gt;90%&lt;\/i&gt;\s*<\/span>/', $html);

        $html = Ui::statusItem(label: 'Plain', muted: true)->toHtml();

        $this->assertStringNotContainsString('status-icon', $html);
        $this->assertStringNotContainsString('status-label', $html);
        $this->assertStringContainsString('<span class="status-text muted">', $html);
    }

    public function test_a_status_item_slot_takes_markup_on_the_right(): void
    {
        $html = $this->capture(function () {
            Ui::statusItem(label: 'Updates', value: 'unused', href: '/unused')->slot();
            echo '<button class="btn-solid theme-primary" type="button">Update</button>';
            Ui::end();
        });

        $this->assertMatchesRegularExpression('/<span class="status-label">\s*<button class="btn-solid theme-primary" type="button">Update<\/button>\s*<\/span>/', $html);
        $this->assertStringNotContainsString('unused', $html);
    }
}
