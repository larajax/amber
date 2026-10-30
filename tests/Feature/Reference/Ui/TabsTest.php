<?php

namespace Tests\Feature\Reference\Ui;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TabsTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_page_renders_every_example_beside_its_source(): void
    {
        $html = $this->get(route('reference.ui.tabs'))->assertOk()->getContent();

        foreach (['tabs-primary', 'tabs-secondary', 'tabs-pill', 'tabs-underline', 'tabs-overflow', 'tabs-closable', 'tabs-modified'] as $example) {
            $source = e(trim(file_get_contents(resource_path("views/reference/ui/examples/{$example}.blade.php"))));
            $this->assertStringContainsString('<pre><code>'.$source.'</code></pre>', $html, "{$example} is not shown.");
            $this->assertStringContainsString('id="'.$example.'"', $html);
        }
    }

    public function test_the_page_renders_each_tab_style_and_option(): void
    {
        $html = $this->get(route('reference.ui.tabs'))->assertOk()->getContent();

        $this->assertSame(7, substr_count($html, 'data-control="tab"'));
        $this->assertStringContainsString('class="control-tabs primary-tabs" data-control="tab" data-linkable', $html);
        $this->assertStringContainsString('class="control-tabs secondary-tabs" data-control="tab"', $html);
        $this->assertStringContainsString('class="control-tabs pill-tabs" data-control="tab"', $html);
        $this->assertStringContainsString('class="control-tabs underline-tabs" data-control="tab"', $html);
        $this->assertStringContainsString('data-control="tab" data-closable', $html);
        $this->assertStringContainsString("jax.fetchControl(document.getElementById('uiClosableTabs'), 'tab').addTab(", $html);
        $this->assertStringContainsString('data-close-confirmation="This file has unsaved changes. Close it anyway?"', $html);
        $this->assertStringContainsString("new CustomEvent('tab:modified', { bubbles: true })", $html);
        $this->assertMatchesRegularExpression('/<a href="javascript:;">\s*December report\s*<\/a>/', $html);
        $this->assertStringContainsString('The December pane.', $html);
    }
}
