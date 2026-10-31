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

        foreach (['icon-extend', 'icon-packs', 'icon-pack-keys'] as $example) {
            $source = e(trim(file_get_contents(resource_path("views/reference/extending/examples/{$example}.blade.php"))));
            $this->assertStringContainsString('<pre><code>'.$source.'</code></pre>', $html, "{$example} is not shown.");
        }

        foreach (['extend', 'packs', 'all-keys'] as $section) {
            $this->assertStringContainsString('id="'.$section.'"', $html);
        }

        $path = 'app/Controllers/Reference/ExtendingController/IconRegistrations.php';
        $this->assertStringContainsString($path, $html);
        $this->assertStringContainsString('<pre><code>'.e(trim(file_get_contents(base_path($path)))).'</code></pre>', $html);
    }

    public function test_the_page_examples_never_call_the_internal_icon_methods(): void
    {
        foreach (glob(resource_path('views/reference/extending/examples/icon-*.blade.php')) as $file) {
            $this->assertStringNotContainsString('Ui::icon', file_get_contents($file), basename($file).' calls an internal icon method.');
        }
    }

    public function test_overridden_keys_change_the_icons_the_widgets_draw(): void
    {
        $html = $this->get(route('reference.extending.icons'))->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/<div class="callout fade show callout-info">\s*<div class="header">\s*<div class="custom-icon">\s*<i class="ph ph-lightbulb" aria-hidden="true"><\/i>/', $html);
        $this->assertMatchesRegularExpression('/<span class="form-adorn-icon" aria-hidden="true">\s*<i class="ph ph-binoculars" aria-hidden="true"><\/i>/', $html);

        // The bootstrap pack gets its own glyphs for the same keys from the closure
        $this->assertSame('bi bi-lightbulb', Ui::iconClass('callout.info', 'bootstrap'));
        $this->assertSame('bi bi-binoculars', Ui::iconClass('search.icon', 'bootstrap'));
    }

    public function test_the_pack_table_compares_keys_across_the_packs(): void
    {
        $html = $this->get(route('reference.extending.icons'))->assertOk()->getContent();

        $this->assertStringContainsString('<i class="ph ph-trash" aria-hidden="true"></i>', $html);
        $this->assertStringContainsString('<i class="bi bi-trash" aria-hidden="true"></i>', $html);
        $this->assertStringContainsString('<i class="ph ph-trash-simple" aria-hidden="true"></i>', $html);

        // The amber pack builds on phosphor, so keys it does not swap keep their phosphor icon
        $this->assertSame('ph ph-caret-down', Ui::iconClass('dropdown.caret', 'amber'));
    }

    public function test_the_key_grid_lists_every_key_of_the_default_pack(): void
    {
        $html = $this->get(route('reference.extending.icons'))->assertOk()->getContent();

        foreach (['action.delete', 'pagination.next', 'file.type.pdf', 'search.icon'] as $key) {
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
