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

        foreach (['dropdown-buttons', 'dropdown-caret', 'dropdown-items', 'dropdown-markup', 'dropdown-placement'] as $example) {
            $source = e(trim(file_get_contents(resource_path("views/reference/ui/examples/{$example}.blade.php"))));
            $this->assertStringContainsString('<pre><code>'.$source.'</code></pre>', $html, "{$example} is not shown.");
            $this->assertStringContainsString('id="'.str_replace('dropdown-buttons', 'dropdown-button', $example).'"', $html);
        }
    }

    public function test_the_page_renders_each_menu(): void
    {
        $html = $this->get(route('reference.ui.dropdowns'))->assertOk()->getContent();

        $this->assertSame(6, substr_count($html, '<div class="menu control-dropdown" role="menu">'));
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

        $this->assertMatchesRegularExpression('/class="filter-group filter-setup">\s*<a href="javascript:;"\s+data-bs-toggle="menu"/', $html);
        $this->assertMatchesRegularExpression('/<div class="menu control-dropdown" role="menu">\s*<a\s+data-filter-clear\s+class="menu-item"/', $html);
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
