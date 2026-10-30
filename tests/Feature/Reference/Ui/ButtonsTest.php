<?php

namespace Tests\Feature\Reference\Ui;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Larajax\Ui\Facades\Ui;
use Tests\TestCase;

class ButtonsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var array<int, string> examples on the page, by example view name.
     */
    protected array $examples = [
        'button-variants', 'button-outline', 'button-sizes', 'button-icons', 'button-links', 'button-attributes',
        'button-groups', 'ajax-buttons', 'button-loading', 'icon-buttons', 'hotkey-buttons', 'button-fluent',
    ];

    /**
     * ajax posts an AJAX handler request to the Buttons page.
     *
     * @param  array<string, mixed>  $data
     */
    protected function ajax(string $handler, array $data = []): TestResponse
    {
        return $this->withHeaders([
            'X-AJAX-HANDLER' => $handler,
            'X-Requested-With' => 'XMLHttpRequest',
        ])->post(route('reference.ui.buttons'), $data);
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
        $html = $this->get(route('reference.ui.buttons'))->assertOk()->getContent();

        foreach ($this->examples as $example) {
            $source = e(trim(file_get_contents(resource_path("views/reference/ui/examples/{$example}.blade.php"))));
            $this->assertStringContainsString('<pre><code>'.$source.'</code></pre>', $html, "{$example} is not shown.");
        }

        foreach (['variants', 'outline', 'sizes', 'icons', 'links', 'attributes', 'groups', 'ajax-button', 'loading', 'icon-button', 'hotkeys', 'fluent'] as $section) {
            $this->assertStringContainsString('id="'.$section.'"', $html);
        }

        $this->assertMatchesRegularExpression('/href="'.preg_quote(route('reference.ui.buttons'), '/').'"\s+aria-current="page"/', $html);
    }

    public function test_the_page_renders_each_button_kind(): void
    {
        $html = $this->get(route('reference.ui.buttons'))->assertOk()->getContent();

        $this->assertStringContainsString('class="btn-solid theme-primary"', $html);
        $this->assertStringContainsString('class="btn btn-outline theme-primary"', $html);
        $this->assertStringContainsString('class="btn-solid theme-primary btn-lg"', $html);
        $this->assertStringContainsString('<i class="ph ph-trash" aria-hidden="true"></i>', $html);
        $this->assertStringContainsString('href="'.route('reference.index').'"', $html);
        $this->assertStringContainsString('title="Any argument the button does not know becomes an attribute"', $html);
        $this->assertStringContainsString('disabled="disabled"', $html);
        $this->assertStringContainsString('<div class="btn-group">', $html);
        $this->assertStringContainsString('<span class="button-text">', $html);
        $this->assertStringContainsString('data-request="onFlashMessage"', $html);
        $this->assertStringContainsString('data-request-data="{&quot;item&quot;:7}"', $html);
        $this->assertStringContainsString('data-request-confirm="Run this action?"', $html);
        $this->assertStringContainsString('data-attach-loading="true"', $html);
        $this->assertStringContainsString('class="btn-icon-link ph ph-arrows-clockwise"', $html);
        $this->assertStringContainsString('data-handler="onLoadDialog"', $html);
        $this->assertStringContainsString('data-hotkey="ctrl+s,cmd+s"', $html);
        $this->assertStringContainsString('data-tooltip-text="Save draft" data-tooltip-hotkey="⌃S, ⌘S" data-tooltip-position="top"', $html);
    }

    public function test_the_fluent_example_sets_props_by_chaining(): void
    {
        $html = $this->get(route('reference.ui.buttons'))->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/<button class="btn-solid theme-primary" type="button">\s*<i class="ph ph-paper-plane-tilt" aria-hidden="true"><\/i>\s*Publish/', $html);
        $this->assertMatchesRegularExpression('/class="btn-solid theme-secondary" type="button" data-request="onArchiveItem" data-request-data="\{&quot;item&quot;:7\}"/', $html);
    }

    public function test_a_first_argument_without_a_name_is_the_label(): void
    {
        $this->assertMatchesRegularExpression('/<button class="btn" type="button">\s*Save\s*<\/button>/', (string) Ui::button('Save'));
        $this->assertMatchesRegularExpression('/data-request="onSave">\s*Save\s*<\/button>/', (string) Ui::ajaxButton('Save', handler: 'onSave'));
        $this->assertMatchesRegularExpression('/data-handler="onOpen"[^>]*>\s*Open\s*<\/button>/', (string) Ui::popupButton('Open', handler: 'onOpen'));
    }

    public function test_button_flags_render_a_solid_variant_with_a_theme(): void
    {
        $this->assertStringContainsString('class="btn-solid theme-primary"', (string) Ui::button(label: 'Save', primary: true));
        $this->assertStringContainsString('class="btn-solid theme-secondary"', (string) Ui::button(label: 'Back', secondary: true));
        $this->assertStringContainsString('class="btn-solid theme-danger"', (string) Ui::button(label: 'Delete', danger: true));
    }

    public function test_a_button_without_a_flag_renders_the_base_class(): void
    {
        $this->assertStringContainsString('class="btn"', (string) Ui::button(label: 'Plain'));
        $this->assertStringContainsString('class="btn"', (string) Ui::ajaxButton(label: 'Plain', handler: 'onPlain'));
    }

    public function test_ajax_popup_and_dropdown_buttons_render_the_variant_classes(): void
    {
        $this->assertStringContainsString('class="btn-solid theme-danger"', (string) Ui::ajaxButton(label: 'Delete', handler: 'onDelete', danger: true));
        $this->assertStringContainsString('class="btn-solid theme-primary"', (string) Ui::popupButton(label: 'Open', handler: 'onOpen', primary: true));
        $this->assertStringContainsString('class="btn-solid theme-secondary"', (string) Ui::dropdownButton(label: 'Actions', secondary: true));
    }

    public function test_the_icon_button_renders_the_icon_link_class_not_the_square_icon_button(): void
    {
        $html = (string) Ui::iconButton(label: 'Remove', icon: 'icon-trash', danger: true);

        $this->assertMatchesRegularExpression('/class="btn-icon-link [^"]*danger"/', $html);
        $this->assertDoesNotMatchRegularExpression('/class="[^"]*\bbtn-icon\b(?!-)/', $html);
    }

    public function test_a_true_data_attribute_renders_as_true(): void
    {
        $html = (string) Ui::ajaxButton(label: 'Save', handler: 'onSave', dataRequestFlash: true, dataChangeMonitorCommit: true);

        $this->assertStringContainsString('data-request-flash="true"', $html);
        $this->assertStringContainsString('data-change-monitor-commit="true"', $html);
        $this->assertStringNotContainsString('data-request-flash="data-request-flash"', $html);
    }

    public function test_a_true_data_attribute_renders_as_true_on_every_component(): void
    {
        $this->assertStringContainsString('data-backdrop="true"', (string) Ui::popupButton(label: 'Open', handler: 'onOpen', dataBackdrop: true));
        $this->assertStringContainsString('data-list-checked-trigger="true"', (string) Ui::button(label: 'Delete', dataListCheckedTrigger: true));
        $this->assertStringContainsString('data-request-flash="true"', (string) Ui::dropdownItem(label: 'Archive', handler: 'onArchive', dataRequestFlash: true));
    }

    public function test_string_and_false_data_attributes_are_unchanged(): void
    {
        $html = (string) Ui::ajaxButton(label: 'Save', handler: 'onSave', dataRequestFlash: 'success,error', dataRequestConfirm: 'Sure?', dataAttachLoading: false);

        $this->assertStringContainsString('data-request-flash="success,error"', $html);
        $this->assertStringContainsString('data-request-confirm="Sure?"', $html);
        $this->assertStringNotContainsString('data-attach-loading', $html);
    }

    public function test_a_true_attribute_outside_data_keeps_its_name(): void
    {
        $this->assertStringContainsString('disabled="disabled"', (string) Ui::button(label: 'Locked', disabled: true));
    }

    public function test_on_flash_message_flashes_a_message(): void
    {
        $this->assertFlash($this->ajax('onFlashMessage'), 'success', 'Hello from the server.');
    }

    public function test_on_confirmed_action_flashes_a_message(): void
    {
        $this->assertFlash($this->ajax('onConfirmedAction'), 'success', 'The action ran after you confirmed it.');
    }

    public function test_on_archive_item_reads_the_request_data(): void
    {
        $this->assertFlash($this->ajax('onArchiveItem', ['item' => 7]), 'success', 'Item 7 archived.');
    }

    public function test_on_archive_item_casts_the_item_to_a_number(): void
    {
        $this->assertFlash($this->ajax('onArchiveItem', ['item' => '<b>7</b>']), 'success', 'Item 0 archived.');
    }

    public function test_on_refresh_data_flashes_a_message(): void
    {
        $this->assertFlash($this->ajax('onRefreshData'), 'info', 'The data was refreshed.');
    }

    public function test_on_save_draft_flashes_a_message(): void
    {
        $this->assertFlash($this->ajax('onSaveDraft'), 'success', 'Draft saved.');
    }
}
