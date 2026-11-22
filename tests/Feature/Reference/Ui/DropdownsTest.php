<?php

namespace Tests\Feature\Reference\Ui;

use App\Controllers\Reference\UiController;
use App\Models\Sample;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Larajax\Ui\Facades\Ui;
use Larajax\Ui\Widgets\Filter;
use Larajax\Ui\Widgets\Form;
use Tests\TestCase;

class DropdownsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ajax posts an AJAX handler request to the Dropdowns page.
     *
     * @param  array<string, mixed>  $data
     */
    protected function ajax(string $handler, array $data = []): TestResponse
    {
        return $this->withHeaders([
            'X-AJAX-HANDLER' => $handler,
            'X-Requested-With' => 'XMLHttpRequest',
        ])->post(route('reference.ui.dropdowns'), $data);
    }

    /**
     * renderDropdownButton captures a slot-based dropdown button holding one item and a divider.
     *
     * @param  array<string, mixed>  $arguments
     */
    protected function renderDropdownButton(array $arguments): string
    {
        ob_start();
        Ui::dropdownButton(...$arguments)->slot();
        echo Ui::dropdownItem(label: 'Edit', handler: 'onEdit');
        echo Ui::dropdownDivider();
        Ui::end();

        return ob_get_clean();
    }

    public function test_the_page_renders_every_example_beside_its_source(): void
    {
        $html = $this->get(route('reference.ui.dropdowns'))->assertOk()->getContent();

        $sections = [
            'dropdown-buttons' => 'dropdown-button',
            'dropdown-caret' => 'dropdown-caret',
            'dropdown-items' => 'dropdown-items',
            'dropdown-headers' => 'dropdown-headers',
            'dropdown-submenus' => 'dropdown-submenus',
            'dropdown-split' => 'split-button',
            'dropdown-markup' => 'dropdown-markup',
            'dropdown-placement' => 'dropdown-placement',
        ];

        foreach ($sections as $example => $section) {
            $source = e(trim(file_get_contents(resource_path("views/reference/ui/examples/{$example}.blade.php"))));
            $this->assertStringContainsString('<pre><code>'.$source.'</code></pre>', $html, "{$example} is not shown.");
            $this->assertStringContainsString('id="'.$section.'"', $html);
        }
    }

    public function test_the_page_renders_each_menu(): void
    {
        $html = $this->get(route('reference.ui.dropdowns'))->assertOk()->getContent();

        $this->assertSame(10, substr_count($html, '<div class="menu control-dropdown" role="menu">'));
        $this->assertStringContainsString("data-request-data=\"action: 'archive'\"", $html);
        $this->assertStringContainsString('href="'.route('reference.index').'"', $html);
        $this->assertStringContainsString('class="menu-item disabled" role="menuitem" href="javascript:;" aria-disabled="true"', $html);
        $this->assertStringContainsString('class="menu-item theme-danger"', $html);
        $this->assertStringContainsString('data-request-confirm="Delete the project?"', $html);
        $this->assertStringContainsString('data-bs-placement="top-start"', $html);
        $this->assertStringContainsString('data-bs-placement="bottom-end"', $html);
        $this->assertStringContainsString('<hr class="menu-divider">', $html);
    }

    public function test_the_dropdown_button_renders_a_bootstrap_menu(): void
    {
        $html = $this->renderDropdownButton(['label' => 'Actions']);

        $this->assertStringContainsString('<div class="btn-group">', $html);
        $this->assertStringContainsString('data-bs-toggle="menu"', $html);
        $this->assertStringContainsString('data-bs-strategy="fixed"', $html);
        $this->assertStringContainsString('aria-expanded="false"', $html);
        $this->assertStringContainsString('<div class="menu control-dropdown" role="menu">', $html);
        $this->assertStringContainsString('class="menu-item" role="menuitem"', $html);
        $this->assertStringContainsString('data-request="onEdit"', $html);
        $this->assertStringContainsString('<hr class="menu-divider">', $html);
        $this->assertStringContainsString(Ui::iconClass('dropdown.caret'), $html);
        $this->assertStringNotContainsString('dropdown-', $html);
        $this->assertStringNotContainsString('<li', $html);
    }

    public function test_the_dropdown_button_caret_can_be_turned_off(): void
    {
        $html = $this->renderDropdownButton(['label' => 'Actions', 'caret' => false]);

        $this->assertStringContainsString('data-bs-toggle="menu"', $html);
        $this->assertStringNotContainsString(Ui::iconClass('dropdown.caret'), $html);
    }

    public function test_the_dropdown_item_merges_extra_classes(): void
    {
        $html = (string) Ui::dropdownItem(label: 'Delete', class: 'theme-danger');

        $this->assertStringContainsString('class="menu-item theme-danger"', $html);
        $this->assertStringContainsString('href="javascript:;"', $html);
        $this->assertStringNotContainsString('data-request', $html);
    }

    public function test_the_dropdown_item_takes_popups_request_data_hotkeys_and_states(): void
    {
        $popup = (string) Ui::dropdownItem(label: 'Details', handler: 'onLoadDialog', popup: true, requestData: ['id' => 4]);
        $this->assertStringContainsString('data-control="popup" data-handler="onLoadDialog" data-request-data="{&quot;id&quot;:4}"', $popup);
        $this->assertStringNotContainsString('data-request="', $popup);

        $hotkey = (string) Ui::dropdownItem(label: 'Refresh', handler: 'onRefresh', hotkey: 'shift+r');
        $this->assertMatchesRegularExpression('/data-hotkey="shift\+r">\s*Refresh\s*<kbd class="menu-item-hotkey">⇧R<\/kbd>/', $hotkey);

        $selected = (string) Ui::dropdownItem(label: 'Newest', selected: true);
        $this->assertStringContainsString('class="menu-item selected" role="menuitem" href="javascript:;" aria-current="true"', $selected);
        $this->assertStringContainsString(Ui::iconClass('dropdown.check').' menu-item-check', $selected);

        $disabled = (string) Ui::dropdownItem(label: 'Export', disabled: true);
        $this->assertStringContainsString('class="menu-item disabled" role="menuitem" href="javascript:;" aria-disabled="true"', $disabled);
    }

    public function test_the_dropdown_item_slot_opens_a_submenu(): void
    {
        ob_start();
        Ui::dropdownItem(label: 'Export', icon: 'ph ph-export')->slot();
        echo Ui::dropdownItem(label: 'As PDF', handler: 'onPdf');
        Ui::end();
        $html = ob_get_clean();

        $this->assertMatchesRegularExpression('/^<div class="submenu">\s*<a class="menu-item" role="menuitem" href="javascript:;" aria-haspopup="true">.*?Export\s*<\/a>\s*<div class="menu control-dropdown" role="menu">\s*<a class="menu-item"[^>]*data-request="onPdf"/s', trim($html));
    }

    public function test_the_dropdown_header_labels_a_section(): void
    {
        $this->assertMatchesRegularExpression('/^<div class="menu-header extra">\s*Density &amp; size\s*<\/div>$/', trim((string) Ui::dropdownHeader(label: 'Density & size', class: 'extra')));
    }

    public function test_the_split_button_runs_its_action_beside_a_caret_menu(): void
    {
        ob_start();
        Ui::splitButton(
            label: 'Save',
            handler: 'onSave',
            requestData: ['close' => 0],
            primary: true,
            toggleAttributes: ['data-list-checked-trigger' => 'true'],
            dataRequestFlash: true
        )->slot();
        echo Ui::dropdownItem(label: 'Save and close', handler: 'onSave');
        Ui::end();
        $html = ob_get_clean();

        $this->assertStringStartsWith('<div class="btn-group split-button">', trim($html));
        $this->assertMatchesRegularExpression('/<button class="btn-solid theme-primary" type="button" data-request="onSave" data-request-data="\{&quot;close&quot;:0\}" data-request-flash="true">/', $html);
        $this->assertMatchesRegularExpression('/<button class="btn-solid theme-primary split-toggle" type="button" title="More options" aria-label="More options" data-bs-toggle="menu" data-bs-strategy="fixed" data-bs-placement="bottom-end" aria-expanded="false" data-list-checked-trigger="true">/', $html);
        $this->assertMatchesRegularExpression('/<div class="menu control-dropdown" role="menu">\s*<a class="menu-item"[^>]*>\s*Save and close/', $html);

        $link = (string) Ui::splitButton(label: 'Open', href: 'https://example.com', popup: false);
        $this->assertMatchesRegularExpression('/<a\s+href="https:\/\/example.com" class="btn" type="button">/', $link);
    }

    public function test_the_filter_setup_menu_uses_bootstrap_menu_markup(): void
    {
        $filter = Filter::createIn(new UiController, [
            'model' => new User,
            'scopes' => [
                'verified' => ['label' => 'Verified', 'type' => 'checkbox', 'conditions' => 'email_verified_at is not null'],
            ],
        ]);

        $filter->bindToController();

        $html = (string) $filter->render();

        $this->assertMatchesRegularExpression('/class="filter-group filter-setup">\s*<a href="javascript:;"\s+class="filter-action"\s+data-bs-toggle="menu"/', $html);
        $this->assertMatchesRegularExpression('/<div class="menu control-dropdown" role="menu" id="[^"]+-setupMenu">\s*<a\s+data-filter-clear\s+class="menu-item"/', $html);
        $this->assertStringNotContainsString('data-bs-toggle="dropdown"', $html);
        $this->assertStringNotContainsString('dropdown-menu', $html);
    }

    public function test_the_repeater_item_menu_uses_bootstrap_menu_markup(): void
    {
        $form = Form::createIn(new UiController, [
            'model' => Sample::factory()->create(['steps' => [['title' => 'Unbox it']]]),
            'fields' => [
                'steps' => [
                    'label' => 'Steps',
                    'type' => 'repeater',
                    'form' => ['fields' => ['title' => ['label' => 'Title']]],
                ],
            ],
        ]);

        $form->bindToController();

        $html = (string) $form->render();

        $this->assertStringContainsString('class="repeater-item-menu" data-bs-toggle="menu" data-bs-placement="bottom-end"', $html);
        $this->assertStringContainsString('<div class="menu control-dropdown" role="menu"></div>', $html);
        $this->assertMatchesRegularExpression('/<a\s+data-repeater-remove\s+class="menu-item"/', $html);
        $this->assertStringContainsString('<hr class="menu-divider">', $html);
        $this->assertStringNotContainsString('dropdown-menu', $html);
        $this->assertStringNotContainsString('data-bs-toggle="dropdown"', $html);
    }

    public function test_on_dropdown_action_runs_the_named_action(): void
    {
        $response = $this->ajax('onDropdownAction', ['action' => 'archive'])->assertOk();

        $flash = collect($response->json('__ajax.ops'))->firstWhere('op', 'flash');
        $this->assertSame('success', $flash['level']);
        $this->assertSame('Archived the project.', $flash['text']);
    }

    public function test_on_dropdown_action_ignores_an_unknown_action(): void
    {
        $response = $this->ajax('onDropdownAction', ['action' => 'drop'])->assertOk();

        $this->assertSame('Nothing to do.', collect($response->json('__ajax.ops'))->firstWhere('op', 'flash')['text']);
    }
}
