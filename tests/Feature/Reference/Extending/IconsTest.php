<?php

namespace Tests\Feature\Reference\Extending;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Larajax\Ui\Classes\Icons\IconPack;
use Larajax\Ui\Facades\Ui;
use Tests\TestCase;

class IconsTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_page_renders_every_example_beside_its_source(): void
    {
        $html = $this->get(route('reference.extending.icons'))->assertOk()->getContent();

        foreach (['icon-keys', 'icon-attributes', 'icon-class', 'icon-packs', 'icon-extend', 'icon-pack-keys'] as $example) {
            $source = e(trim(file_get_contents(resource_path("views/reference/extending/examples/{$example}.blade.php"))));
            $this->assertStringContainsString('<pre><code>'.$source.'</code></pre>', $html, "{$example} is not shown.");
        }

        foreach (['keys', 'attributes', 'icon-class', 'packs', 'extend', 'all-keys'] as $section) {
            $this->assertStringContainsString('id="'.$section.'"', $html);
        }

        $path = 'app/Controllers/Reference/ExtendingController/IconRegistrations.php';
        $this->assertStringContainsString($path, $html);
        $this->assertStringContainsString('<pre><code>'.e(trim(file_get_contents(base_path($path)))).'</code></pre>', $html);
    }

    public function test_the_page_renders_icons_from_keys_attributes_and_packs(): void
    {
        $html = $this->get(route('reference.extending.icons'))->assertOk()->getContent();

        $this->assertStringContainsString('<i class="ph ph-trash" aria-hidden="true"></i>', $html);
        $this->assertStringContainsString('<i class="ph ph-check-circle fg-success" title="Saved" aria-hidden="true"></i>', $html);
        $this->assertStringContainsString('<i aria-hidden="false" aria-label="Delete" class="ph ph-trash"></i>', $html);
        $this->assertStringContainsString('<i class="ph ph-rocket-launch" aria-hidden="true"></i>', $html);
        $this->assertStringContainsString('<i class="ph ph-magnifying-glass" aria-hidden="true"></i>', $html);
        $this->assertStringContainsString('<i class="bi bi-search" aria-hidden="true"></i>', $html);
        $this->assertStringContainsString('<i class="bi bi-trash" aria-hidden="true"></i>', $html);
    }

    public function test_the_page_registers_the_amber_pack_and_the_status_keys(): void
    {
        $html = $this->get(route('reference.extending.icons'))->assertOk()->getContent();

        $this->assertStringContainsString('<i class="ph ph-trash-simple" aria-hidden="true"></i>', $html);
        $this->assertStringContainsString('<i class="ph ph-lightbulb" aria-hidden="true"></i>', $html);
        $this->assertStringContainsString('<i class="ph ph-globe-simple" aria-hidden="true"></i>', $html);
        $this->assertStringContainsString('<i class="bi bi-globe" aria-hidden="true"></i>', $html);
        $this->assertStringContainsString('<i class="bi bi-pencil-square" aria-hidden="true"></i>', $html);

        // The amber pack builds on phosphor, so keys it does not swap keep their phosphor icon
        $this->assertSame('ph ph-caret-down', Ui::iconClass('dropdown.caret', 'amber'));
        $this->assertSame('ph ph-binoculars', Ui::iconClass('search.icon', 'amber'));
    }

    public function test_the_key_grid_lists_every_key_of_the_default_pack(): void
    {
        $html = $this->get(route('reference.extending.icons'))->assertOk()->getContent();

        foreach (['action.delete', 'pagination.next', 'file.type.pdf', 'status.published'] as $key) {
            $this->assertMatchesRegularExpression('/<code>\s*'.preg_quote($key, '/').'\s*<\/code>/', $html);
        }
    }

    public function test_icon_resolves_a_key_and_merges_attributes(): void
    {
        $this->assertSame('<i class="ph ph-trash" aria-hidden="true"></i>', Ui::icon('action.delete'));
        $this->assertSame('<i class="ph ph-trash big" title="Remove" aria-hidden="true"></i>', Ui::icon('action.delete', ['class' => 'big', 'title' => 'Remove']));
        $this->assertSame('<i class="bi bi-trash" aria-hidden="true"></i>', Ui::icon('action.delete', [], 'bootstrap'));
    }

    public function test_an_unknown_name_passes_through_as_classes_and_an_empty_name_renders_nothing(): void
    {
        $this->assertSame('ph ph-rocket', Ui::iconClass('ph ph-rocket'));
        $this->assertSame('', Ui::iconClass('  '));
        $this->assertSame('', Ui::icon(''));

        config(['larajax-icons.fallback' => false]);

        $this->assertSame('', Ui::iconClass('ph ph-rocket'));
        $this->assertSame('ph ph-trash', Ui::iconClass('action.delete'));
    }

    public function test_a_pack_can_be_an_array_a_class_or_a_closure(): void
    {
        Ui::registerIconPack('plain', ['action.delete' => 'plain-trash']);
        Ui::registerIconPack('lazy', fn () => ['action.delete' => 'lazy-trash']);
        Ui::registerIconPack('classy', new class implements IconPack {
            public function icons(): array
            {
                return ['action.delete' => 'classy-trash'];
            }
        });

        $this->assertSame('plain-trash', Ui::iconClass('action.delete', 'plain'));
        $this->assertSame('lazy-trash', Ui::iconClass('action.delete', 'lazy'));
        $this->assertSame('classy-trash', Ui::iconClass('action.delete', 'classy'));
    }

    public function test_the_default_pack_and_overrides_come_from_config(): void
    {
        config(['larajax-icons.default' => 'bootstrap']);
        $this->assertSame('bi bi-trash', Ui::iconClass('action.delete'));

        config(['larajax-icons.overrides' => ['action.delete' => 'my-trash']]);
        Ui::registerIconPack('fresh', ['action.delete' => 'fresh-trash']);
        $this->assertSame('my-trash', Ui::iconClass('action.delete', 'fresh'));
    }

    public function test_extensions_apply_to_every_pack_in_order(): void
    {
        Ui::extendIcons(['status.live' => 'ph ph-broadcast']);
        Ui::extendIcons(fn (array $icons, string $pack) => $pack === 'bootstrap' ? ['status.live' => 'bi bi-broadcast'] + $icons : $icons);

        $this->assertSame('ph ph-broadcast', Ui::iconClass('status.live'));
        $this->assertSame('bi bi-broadcast', Ui::iconClass('status.live', 'bootstrap'));
    }
}
