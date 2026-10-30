<?php

namespace Tests\Feature;

use Carbon\CarbonInterval;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Sleep;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class UiElementsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Sleep::fake();
    }

    /**
     * ajax posts an AJAX handler request to a UI elements page.
     *
     * @param  array<string, mixed>  $data
     */
    protected function ajax(string $url, string $handler, array $data = []): TestResponse
    {
        return $this->withHeaders([
            'X-AJAX-HANDLER' => $handler,
            'X-Requested-With' => 'XMLHttpRequest',
        ])->post($url, $data);
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

    public function test_the_buttons_page_renders_every_section(): void
    {
        $html = $this->get('/ui')->assertOk()->getContent();

        foreach (['variants', 'outline', 'sizes', 'icons', 'links', 'fluent', 'ajax-button', 'popup-button', 'icon-button', 'dropdown-button', 'hotkeys'] as $section) {
            $this->assertStringContainsString('id="'.$section.'"', $html);
        }

        $this->assertStringContainsString('class="btn-solid theme-primary"', $html);
        $this->assertStringContainsString('class="btn btn-outline theme-primary"', $html);
        $this->assertStringContainsString('class="btn-solid theme-primary btn-lg"', $html);
        $this->assertStringContainsString('<i class="ph ph-trash" aria-hidden="true"></i>', $html);
        $this->assertStringContainsString('href="'.url('users').'"', $html);
        $this->assertMatchesRegularExpression('/class="btn-solid theme-success\s*"/', $html);
        $this->assertMatchesRegularExpression('/class="btn-outline theme-warning\s*"/', $html);
        $this->assertStringContainsString('data-request="onFlashMessage"', $html);
        $this->assertStringContainsString('data-request-data="{&quot;item&quot;:7}"', $html);
        $this->assertStringContainsString('data-request-confirm="Run this action?"', $html);
        $this->assertStringContainsString('data-handler="onLoadDialog"', $html);
        $this->assertStringContainsString('data-size="huge"', $html);
        $this->assertStringContainsString('class="btn-icon-link ph ph-arrows-clockwise"', $html);
        $this->assertStringContainsString('data-bs-toggle="menu"', $html);
        $this->assertStringContainsString("data-request-data=\"action: 'archive'\"", $html);
        $this->assertStringContainsString('data-hotkey="ctrl+s,cmd+s"', $html);
        $this->assertStringContainsString('data-tooltip-text="Save draft" data-tooltip-hotkey="⌃S, ⌘S" data-tooltip-position="top"', $html);
    }

    public function test_the_feedback_page_renders_every_section(): void
    {
        $html = $this->get('/ui/feedback')->assertOk()->getContent();

        foreach (['callouts', 'callout-body', 'callout-icons', 'search-input', 'search-input-ajax', 'scoreboard', 'tabs-primary', 'tabs-secondary', 'tabs-closable', 'loader-container', 'load-indicator', 'load-indicator-api', 'change-monitor'] as $section) {
            $this->assertStringContainsString('id="'.$section.'"', $html);
        }

        foreach (['info', 'success', 'warning', 'danger'] as $type) {
            $this->assertStringContainsString('callout-'.$type, $html);
        }

        $this->assertStringContainsString('<i class="ph ph-warning-octagon" aria-hidden="true"></i>', $html);
        $this->assertStringContainsString('<i class="ph ph-rocket-launch" aria-hidden="true"></i>', $html);
        $this->assertStringContainsString('Two teammates accepted their invites.', $html);
        $this->assertStringContainsString('data-request="onCalloutAction"', $html);
        $this->assertStringContainsString('data-control="search-input"', $html);
        $this->assertStringContainsString('data-request="onSearchCities"', $html);
        $this->assertSame(2, preg_match_all('/<div data-control="search-input"[^>]*>\s*<label class="form-control form-adorn">\s*<span class="form-adorn-icon" aria-hidden="true">\s*<i class="ph ph-magnifying-glass" aria-hidden="true"><\/i>\s*<\/span>\s*<input[^>]*class="form-ghost"[^>]*>\s*<button\s+class="clear-input-text"[^>]*aria-label="Clear"\s+data-search-clear\s*>\s*<i class="ph ph-x" aria-hidden="true"><\/i>\s*<\/button>\s*<\/label>/', $html));
        $this->assertMatchesRegularExpression('/<input[^>]*name="city_search"[^>]*placeholder="Search cities..."[^>]*aria-label="Search cities..."/', $html);
        $this->assertStringNotContainsString('storm-icon-pseudo', $html);
        $this->assertStringContainsString('<div id="uiCityResults">', $html);
        $this->assertStringContainsString('class="scoreboard"', $html);
        $this->assertStringContainsString('class="control-tabs primary-tabs" data-control="tab" data-linkable', $html);
        $this->assertStringContainsString('class="control-tabs secondary-tabs" data-control="tab"', $html);
        $this->assertStringContainsString('data-control="tab" data-closable', $html);
        $this->assertStringContainsString('data-control="loader-container"', $html);
        $this->assertStringContainsString('class="loading-indicator size-small"', $html);
        $this->assertStringContainsString("import { LoadIndicator } from '".asset('vendor/larajax/ui/index.js')."';", $html);
        $this->assertStringContainsString('<form data-change-monitor', $html);
        $this->assertStringContainsString('data-change-monitor-commit', $html);
    }

    public function test_the_controls_page_renders_every_section(): void
    {
        $html = $this->get('/ui/controls')->assertOk()->getContent();

        foreach (['popover', 'input-preset', 'input-trigger', 'input-hotkey', 'custom-select', 'custom-select-ajax', 'checkbox', 'checkbox-range', 'rowlink'] as $section) {
            $this->assertStringContainsString('id="'.$section.'"', $html);
        }

        $this->assertStringContainsString('data-control="popover" data-content-from="#uiShippingPopover" data-placement="left"', $html);
        $this->assertStringContainsString('<template id="uiShippingPopover">', $html);
        $this->assertStringContainsString('data-input-preset="#uiPresetTitle" data-input-preset-type="camel"', $html);
        $this->assertStringContainsString('data-trigger-action="enable"', $html);
        $this->assertStringContainsString('data-trigger-condition="value[standard]"', $html);
        $this->assertStringContainsString('data-hotkey="alt+shift+k"', $html);
        $this->assertStringContainsString('class="form-control custom-select select-no-search"', $html);
        $this->assertStringContainsString('<optgroup label="Business">', $html);
        $this->assertStringContainsString('data-handler="onGetCountryOptions"', $html);
        $this->assertStringContainsString('<div class="form-field is-indeterminate">', $html);
        $this->assertStringContainsString("jax.checkboxRangeRegisterClick(ev, 'li', 'input[type=checkbox]')", $html);
        $this->assertStringContainsString('<table class="table data" data-control="rowlink">', $html);
        $this->assertStringContainsString('<td class="nolink">', $html);
    }

    public function test_every_example_shows_the_source_that_rendered_it(): void
    {
        $html = $this->get('/ui')->getContent()
            .$this->get('/ui/feedback')->getContent()
            .$this->get('/ui/controls')->getContent();

        $examples = File::files(resource_path('views/ui_elements/examples'));

        $this->assertNotEmpty($examples);

        foreach ($examples as $example) {
            $source = e(trim(File::get($example->getPathname())));

            $this->assertStringContainsString('<pre><code>'.$source.'</code></pre>', $html, $example->getFilename().' is not shown.');
        }
    }

    public function test_the_sidebar_links_the_ui_pages(): void
    {
        $html = $this->get('/ui/controls')
            ->assertOk()
            ->assertSee('href="'.url('ui').'"', false)
            ->assertSee('href="'.url('ui/feedback').'"', false)
            ->assertSee('href="'.url('ui/controls').'"', false)
            ->getContent();

        $this->assertMatchesRegularExpression('/href="'.preg_quote(url('ui/controls'), '/').'"\s+aria-current="page"/', $html);
    }

    public function test_on_flash_message_flashes_a_message(): void
    {
        $this->assertFlash($this->ajax('/ui', 'onFlashMessage'), 'success', 'Hello from the server.');
    }

    public function test_on_confirmed_action_flashes_a_message(): void
    {
        $this->assertFlash($this->ajax('/ui', 'onConfirmedAction'), 'success', 'The action ran after you confirmed it.');
    }

    public function test_on_archive_item_reads_the_request_data(): void
    {
        $this->assertFlash($this->ajax('/ui', 'onArchiveItem', ['item' => 7]), 'success', 'Item 7 archived.');
    }

    public function test_on_archive_item_casts_the_item_to_a_number(): void
    {
        $this->assertFlash($this->ajax('/ui', 'onArchiveItem', ['item' => '<b>7</b>']), 'success', 'Item 0 archived.');
    }

    public function test_on_load_dialog_renders_the_dialog_at_the_requested_size(): void
    {
        $response = $this->ajax('/ui', 'onLoadDialog', ['size' => 'large'])->assertOk();

        $html = $response->json('result');

        $this->assertStringContainsString('Large dialog', $html);
        $this->assertStringContainsString('<div class="dialog-header">', $html);
        $this->assertStringContainsString('<div class="dialog-body">', $html);
        $this->assertStringContainsString('<div class="dialog-footer">', $html);
        $this->assertStringContainsString('data-dismiss="popup"', $html);
        $this->assertStringContainsString('data-request="onSubmitDialog"', $html);
    }

    public function test_on_load_dialog_falls_back_to_the_default_size(): void
    {
        $html = $this->ajax('/ui', 'onLoadDialog', ['size' => '<script>'])->assertOk()->json('result');

        $this->assertStringContainsString('Default dialog', $html);
        $this->assertStringNotContainsString('<script>', $html);
    }

    public function test_on_submit_dialog_flashes_a_message(): void
    {
        $this->assertFlash($this->ajax('/ui', 'onSubmitDialog', ['note' => 'Hello']), 'success', 'The dialog was submitted.');
    }

    public function test_on_dropdown_action_runs_the_named_action(): void
    {
        $this->assertFlash($this->ajax('/ui', 'onDropdownAction', ['action' => 'archive']), 'success', 'Archived the project.');
    }

    public function test_on_dropdown_action_ignores_an_unknown_action(): void
    {
        $this->assertFlash($this->ajax('/ui', 'onDropdownAction', ['action' => 'drop']), 'success', 'Nothing to do.');
    }

    public function test_on_refresh_data_flashes_a_message(): void
    {
        $this->assertFlash($this->ajax('/ui', 'onRefreshData'), 'info', 'The data was refreshed.');
    }

    public function test_on_save_draft_flashes_a_message(): void
    {
        $this->assertFlash($this->ajax('/ui', 'onSaveDraft'), 'success', 'Draft saved.');
    }

    public function test_on_callout_action_flashes_a_message(): void
    {
        $this->assertFlash($this->ajax('/ui/feedback', 'onCalloutAction'), 'info', 'Payment details are not part of this demo.');
    }

    public function test_on_search_cities_updates_the_results(): void
    {
        $response = $this->ajax('/ui/feedback', 'onSearchCities', ['city_search' => 'syd'])->assertOk();

        $patch = collect($response->json('__ajax.ops'))->firstWhere('op', 'patchDom');

        $this->assertSame('#uiCityResults', $patch['selector']);
        $this->assertStringContainsString('Sydney', $patch['html']);
        $this->assertStringNotContainsString('Paris', $patch['html']);
    }

    public function test_on_search_cities_shows_the_hint_for_an_empty_term(): void
    {
        $html = $this->ajax('/ui/feedback', 'onSearchCities', ['city_search' => ' '])->assertOk()->json('__ajax.ops.0.html');

        $this->assertStringContainsString('Type a city name', $html);
    }

    public function test_on_search_cities_escapes_a_term_with_no_match(): void
    {
        $html = $this->ajax('/ui/feedback', 'onSearchCities', ['city_search' => '<b>Atlantis</b>'])->assertOk()->json('__ajax.ops.0.html');

        $this->assertStringContainsString('No cities match "&lt;b&gt;Atlantis&lt;/b&gt;"', $html);
    }

    public function test_on_slow_request_sleeps_before_flashing(): void
    {
        $this->assertFlash($this->ajax('/ui/feedback', 'onSlowRequest'), 'success', 'The slow request finished.');

        Sleep::assertSlept(fn (CarbonInterval $duration) => (int) $duration->totalMilliseconds === 1500);
    }

    public function test_on_save_profile_flashes_a_message(): void
    {
        $response = $this->ajax('/ui/feedback', 'onSaveProfile', ['display_name' => 'Ada Lovelace', 'is_public' => '1']);

        $this->assertFlash($response, 'success', 'Profile saved.');
    }

    public function test_on_get_country_options_filters_by_the_term(): void
    {
        $this->ajax('/ui/controls', 'onGetCountryOptions', ['term' => 'ger'])
            ->assertOk()
            ->assertJsonPath('results', [['id' => 'de', 'text' => 'Germany']]);
    }

    public function test_on_get_country_options_lists_every_country_without_a_term(): void
    {
        $results = $this->ajax('/ui/controls', 'onGetCountryOptions')->assertOk()->json('results');

        $this->assertCount(16, $results);
        $this->assertSame(['id' => 'ar', 'text' => 'Argentina'], $results[0]);
    }

    public function test_on_row_action_flashes_a_message(): void
    {
        $this->assertFlash($this->ajax('/ui/controls', 'onRowAction'), 'info', 'The row ran an AJAX handler.');
    }
}
