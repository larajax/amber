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
            'callout-types', 'callout-body', 'callout-block', 'callout-icons', 'scoreboard',
            'loader-container', 'load-indicator-markup', 'load-indicator-api', 'change-monitor',
        ];

        foreach ($examples as $example) {
            $source = e(trim(file_get_contents(resource_path("views/reference/ui/examples/{$example}.blade.php"))));
            $this->assertStringContainsString('<pre><code>'.$source.'</code></pre>', $html, "{$example} is not shown.");
        }

        foreach (['callouts', 'callout-body', 'callout-block', 'callout-icons', 'scoreboard', 'loader-container', 'load-indicator', 'load-indicator-api', 'change-monitor'] as $section) {
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
        $this->assertMatchesRegularExpression('/<div class="content">\s*<a\s+href="https:\/\/larajax.org"[^>]*class="btn-link p-0\s*"\s*>Read the upgrade guide<\/a>/', $html);
        $this->assertMatchesRegularExpression('/<div class="action">\s*<button class="btn-solid theme-primary" type="button" data-request="onCalloutAction"[^>]*>\s*Add a card\s*<\/button>\s*<\/div>/', $html);
        $this->assertMatchesRegularExpression('/<div class="content">\s*<p>\s*Add a payment method to keep your projects\./', $html);
        $this->assertStringContainsString('data-request="onCalloutAction"', $html);
        $this->assertStringContainsString('class="scoreboard"', $html);
        $this->assertStringContainsString('<p class="positive">', $html);
        $this->assertStringContainsString('data-control="loader-container"', $html);
        $this->assertStringContainsString('class="loading-indicator size-small"', $html);
        $this->assertStringContainsString('class="loading-indicator indicator-center"', $html);
        $this->assertStringContainsString("import { LoadIndicator } from '".asset('vendor/larajax/ui/index.js')."';", $html);
        $this->assertStringContainsString('<form data-change-monitor', $html);
        $this->assertStringContainsString('data-change-monitor-commit', $html);
    }

    public function test_a_string_body_renders_in_the_content(): void
    {
        $html = Ui::callout('<p>Plain callout body</p>')->toHtml();

        $this->assertMatchesRegularExpression('/<div class="content">\s*<p>Plain callout body<\/p>\s*<\/div>/', $html);
    }

    public function test_a_closure_body_that_echoes_renders_in_the_content(): void
    {
        $html = Ui::callout(function () {
            echo '<p>Echoed body</p>';
        })->toHtml();

        $this->assertMatchesRegularExpression('/<div class="content">\s*<p>Echoed body<\/p>\s*<\/div>/', $html);
    }

    public function test_a_closure_body_can_return_a_view(): void
    {
        $html = Ui::callout(fn () => view('reference.ui._callout_checklist'))->label('Setup complete')->toHtml();

        $this->assertStringContainsString('Two teammates accepted their invites.', $html);
    }

    public function test_an_element_body_renders_in_the_content(): void
    {
        $html = Ui::callout(Ui::button('Read more')->linkUrl('https://example.com'))->toHtml();

        $this->assertMatchesRegularExpression('/<div class="content">\s*<a\s+href="https:\/\/example.com"/', $html);
    }

    public function test_a_callout_without_a_body_renders_no_content(): void
    {
        $html = Ui::callout()->label('Heads up')->toHtml();

        $this->assertStringNotContainsString('class="content"', $html);
    }

    public function test_a_block_captures_the_body_and_a_named_slot_until_the_end(): void
    {
        ob_start();
        Ui::callout()->danger()->label('Card expired')->slot();
        echo '<p>Update your card.</p>';
        Ui::slot('action');
        echo Ui::ajaxButton(label: 'Update', handler: 'onUpdate', danger: true);
        Ui::end();
        $html = ob_get_clean();

        $this->assertStringContainsString('callout-danger', $html);
        $this->assertMatchesRegularExpression('/<div class="content">\s*<p>Update your card.<\/p>\s*<\/div>/', $html);
        $this->assertMatchesRegularExpression('/<div class="action">\s*<button class="btn-solid theme-danger" type="button" data-request="onUpdate">/', $html);
        $this->assertSame(1, substr_count($html, 'Update your card.'));
    }

    public function test_an_empty_block_renders_no_content(): void
    {
        ob_start();
        Ui::callout()->label('Heads up')->slot();
        echo "\n    \n";
        Ui::end();
        $html = ob_get_clean();

        $this->assertStringContainsString('<h3>Heads up</h3>', $html);
        $this->assertStringNotContainsString('class="content"', $html);
    }

    public function test_each_type_shows_its_icon_from_the_pack(): void
    {
        $callouts = [
            'info' => Ui::callout()->label('Info'),
            'success' => Ui::callout()->success()->label('Success'),
            'warning' => Ui::callout()->warning()->label('Warning'),
            'danger' => Ui::callout()->danger()->label('Danger'),
        ];

        foreach ($callouts as $type => $callout) {
            $html = $callout->toHtml();

            $this->assertStringContainsString('callout-'.$type, $html);
            $this->assertStringContainsString('<div class="custom-icon">', $html);
            $this->assertStringContainsString(Ui::icon('callout.'.$type), $html);
            $this->assertStringNotContainsString('no-icon', $html);
        }
    }

    public function test_tip_is_the_info_type(): void
    {
        $this->assertStringContainsString('callout-info', Ui::callout()->tip()->label('Tip')->toHtml());
    }

    public function test_a_custom_icon_replaces_the_type_icon(): void
    {
        $html = Ui::callout()->warning()->icon('ph ph-rocket-launch')->label('Launch')->toHtml();

        $this->assertStringContainsString('<i class="ph ph-rocket-launch" aria-hidden="true"></i>', $html);
        $this->assertStringContainsString('has-custom-icon', $html);
        $this->assertStringNotContainsString(Ui::icon('callout.warning'), $html);
    }

    public function test_the_icon_can_be_turned_off(): void
    {
        $html = Ui::callout()->icon(false)->label('Quiet')->toHtml();

        $this->assertStringNotContainsString('custom-icon', $html);
        $this->assertStringContainsString('no-icon', $html);
    }

    public function test_a_callout_with_only_a_body_has_no_header_or_icon(): void
    {
        $html = Ui::callout('<p>Body only</p>')->toHtml();

        $this->assertStringNotContainsString('class="header"', $html);
        $this->assertStringContainsString('no-icon', $html);
        $this->assertStringContainsString('no-title', $html);
    }

    public function test_an_action_renders_beside_the_header(): void
    {
        $html = Ui::callout()
            ->danger()
            ->label('Card expired')
            ->action(Ui::ajaxButton(label: 'Update', handler: 'onUpdate', danger: true))
            ->toHtml();

        $this->assertMatchesRegularExpression('/<div class="action">\s*<button class="btn-solid theme-danger" type="button" data-request="onUpdate">/', $html);
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
}
