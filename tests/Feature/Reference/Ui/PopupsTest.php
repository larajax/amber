<?php

namespace Tests\Feature\Reference\Ui;

use Carbon\CarbonInterval;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Sleep;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class PopupsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Sleep::fake();
    }

    /**
     * ajax posts an AJAX handler request to the Popups page.
     *
     * @param  array<string, mixed>  $data
     */
    protected function ajax(string $handler, array $data = []): TestResponse
    {
        return $this->withHeaders([
            'X-AJAX-HANDLER' => $handler,
            'X-Requested-With' => 'XMLHttpRequest',
        ])->post(route('reference.ui.popups'), $data);
    }

    public function test_the_page_renders_every_example_beside_its_source(): void
    {
        $html = $this->get(route('reference.ui.popups'))->assertOk()->getContent();

        $examples = ['popup-buttons', 'popup-window-sizes', 'popup-closing', 'popup-markup', 'popup-javascript', 'popover', 'tooltip'];

        foreach ($examples as $example) {
            $source = e(trim(file_get_contents(resource_path("views/reference/ui/examples/{$example}.blade.php"))));
            $this->assertStringContainsString('<pre><code>'.$source.'</code></pre>', $html, "{$example} is not shown.");
        }

        foreach (['popup-button', 'window-sizes', 'closing', 'popup-markup', 'popup-javascript', 'popover', 'tooltip'] as $section) {
            $this->assertStringContainsString('id="'.$section.'"', $html);
        }

        $this->assertStringContainsString('resources/views/reference/ui/_dialog.blade.php', $html);
    }

    public function test_the_page_renders_each_popup_trigger(): void
    {
        $html = $this->get(route('reference.ui.popups'))->assertOk()->getContent();

        foreach (['tiny', 'small', 'large', 'huge', 'giant', 'adaptive', 'auto', '600', 'fullscreen'] as $size) {
            $this->assertStringContainsString('data-size="'.$size.'"', $html);
        }

        $this->assertStringContainsString('data-control="popup" data-handler="onLoadDialog"', $html);
        $this->assertStringContainsString('data-adaptive-height="true"', $html);
        $this->assertStringContainsString('data-keyboard="false"', $html);
        $this->assertStringContainsString('data-backdrop="true"', $html);
        $this->assertStringContainsString("data-request-data=\"size: 'small'\"", $html);
        $this->assertStringContainsString('<template id="uiTemplatePopup">', $html);
        $this->assertStringContainsString('jax.popup.open({', $html);
        $this->assertStringContainsString('data-control="popover" data-content-from="#uiShippingPopover" data-placement="left"', $html);
        $this->assertStringContainsString('<template id="uiShippingPopover">', $html);
        $this->assertStringContainsString('data-modal="true" data-width="320"', $html);
        $this->assertStringContainsString('data-tooltip-text="Shown above"', $html);
        $this->assertStringContainsString('data-tooltip-hotkey="⌃C, ⌘C"', $html);
    }

    public function test_on_load_dialog_renders_the_dialog_at_the_requested_size(): void
    {
        $html = $this->ajax('onLoadDialog', ['size' => 'large'])->assertOk()->json('result');

        $this->assertStringContainsString('Large dialog', $html);
        $this->assertStringContainsString('<div class="dialog-header">', $html);
        $this->assertStringContainsString('<div class="dialog-body">', $html);
        $this->assertStringContainsString('<div class="dialog-footer">', $html);
        $this->assertStringContainsString('data-dismiss="popup"', $html);
        $this->assertStringContainsString('data-request="onSubmitDialog"', $html);
        $this->assertStringContainsString('data-popup-load-indicator', $html);
        $this->assertMatchesRegularExpression('/data-control="popup"\s+data-handler="onLoadDialog"[^>]*data-size="small"/', $html);
    }

    public function test_on_load_dialog_titles_a_pixel_width(): void
    {
        $html = $this->ajax('onLoadDialog', ['size' => '600'])->assertOk()->json('result');

        $this->assertStringContainsString('600px dialog', $html);
    }

    public function test_on_load_dialog_falls_back_to_the_default_size(): void
    {
        $html = $this->ajax('onLoadDialog', ['size' => '<script>'])->assertOk()->json('result');

        $this->assertStringContainsString('Default dialog', $html);
        $this->assertStringNotContainsString('<script>', $html);
    }

    public function test_on_submit_dialog_waits_then_flashes_a_message(): void
    {
        $response = $this->ajax('onSubmitDialog', ['note' => 'Hello'])->assertOk();

        $flash = collect($response->json('__ajax.ops'))->firstWhere('op', 'flash');
        $this->assertSame('success', $flash['level']);
        $this->assertSame('The dialog was submitted.', $flash['text']);

        Sleep::assertSlept(fn (CarbonInterval $duration) => (int) $duration->totalMilliseconds === 500);
    }
}
