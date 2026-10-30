<?php

namespace Tests\Feature\Reference\Forms;

use App\Controllers\Reference\FormsController;
use App\Models\Sample;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Larajax\Ui\Widgets\Form;
use Tests\TestCase;

class LayoutTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var array<int, string> examples on the page that edit the reference sample, one YAML file each.
     */
    protected array $examples = [
        'tabs', 'lazy-tabs', 'secondary-tabs', 'spans', 'row-span', 'sections',
        'comments', 'tooltips', 'attributes', 'css-class',
        'horizontal', 'survey', 'read-only', 'disabled',
    ];

    /**
     * makeSample creates the reference sample with the values the page examples read.
     *
     * @param  array<string, mixed>  $attributes
     */
    protected function makeSample(array $attributes = []): Sample
    {
        return Sample::factory()->create($attributes + [
            'name' => 'Aurora Desk Lamp',
            'email' => 'lighting@example.com',
            'status' => 'active',
            'transport' => 'road',
            'stock_status' => 'low',
            'features' => ['wifi', 'parking'],
            'has_discount' => true,
            'is_featured' => true,
            'notes' => 'Lazy note',
            'template' => '<section></section>',
            'step_style' => 'detailed',
        ]);
    }

    /**
     * pageHtml renders the page for a sample made with the given attributes.
     *
     * @param  array<string, mixed>  $attributes
     */
    protected function pageHtml(array $attributes = []): string
    {
        $this->makeSample($attributes);

        return $this->get(route('reference.forms.layout'))->assertOk()->getContent();
    }

    /**
     * exampleHtml returns the markup of one example section of the page.
     */
    protected function exampleHtml(string $html, string $example): string
    {
        preg_match('/<section class="ui-section" id="'.preg_quote($example, '/').'">(.*?)<\/section>/s', $html, $match);

        $this->assertNotEmpty($match, "The page has no {$example} example.");

        return $match[1];
    }

    /**
     * ajax posts one AJAX handler request to the page.
     *
     * @param  array<string, mixed>  $data
     */
    protected function ajax(string $handler, array $data = []): TestResponse
    {
        return $this->withHeaders([
            'X-AJAX-HANDLER' => $handler,
            'X-Requested-With' => 'XMLHttpRequest',
        ])->post(route('reference.forms.layout'), $data);
    }

    /**
     * makeForm builds a form for the given configuration outside the page, bound to its controller.
     *
     * @param  array<string, mixed>  $config
     */
    protected function makeForm(array $config): Form
    {
        $form = Form::createIn(new FormsController, $config + ['model' => Sample::factory()->create()]);

        $form->bindToController();

        return $form;
    }

    public function test_the_page_renders_every_example_beside_its_yaml(): void
    {
        $html = $this->pageHtml();

        foreach ([...$this->examples, 'preview'] as $example) {
            $this->assertStringContainsString("resources/ui/reference/forms/layout/{$example}.yaml", $html);
        }

        $this->assertSame(count($this->examples) + 1, substr_count($html, 'data-control="formwidget"'));
        $this->assertStringContainsString('data-request="onSaveExamples"', $html);
    }

    public function test_saving_the_page_keeps_read_only_values_and_drops_disabled_ones(): void
    {
        $sample = $this->makeSample();

        $this->ajax('onSaveExamples', [
            'code' => 'emberClock',
            'headline' => 'A clock for every wall',
            'quantity' => '12',
            'notes' => 'Saved from the lazy tab',
            'body' => 'Saved from a secondary tab',
            'state' => 'vic',
            'keywords' => 'clock brass',
            'level' => 'basic',
            'plan' => 'business',
            'currency' => 'nzd',
            'stock_status' => 'out',
            'template' => 'Dropped',
            'step_style' => 'simple',
            'Preview' => ['name' => 'Ignored', 'status' => 'draft'],
        ])->assertOk();

        $sample->refresh();
        $this->assertSame('emberClock', $sample->code);
        $this->assertSame('A clock for every wall', $sample->headline);
        $this->assertSame(12, (int) $sample->quantity);
        $this->assertSame('Saved from the lazy tab', $sample->notes);
        $this->assertSame('Saved from a secondary tab', $sample->body);
        $this->assertSame('vic', $sample->state);
        $this->assertSame('clock brass', $sample->keywords);
        $this->assertSame('basic', $sample->level);
        $this->assertSame('business', $sample->plan);
        $this->assertSame('nzd', $sample->currency);

        // Read-only fields still save the posted value, while disabled fields and the preview form are left out
        $this->assertSame('out', $sample->stock_status);
        $this->assertSame('<section></section>', $sample->template);
        $this->assertSame('detailed', $sample->step_style);
        $this->assertSame('Aurora Desk Lamp', $sample->name);
        $this->assertSame('active', $sample->status);
    }

    public function test_tabs_have_icons_a_default_tab_and_fields_outside_them(): void
    {
        $tabs = $this->exampleHtml($this->pageHtml(['code' => 'auroraDeskLamp']), 'tabs');

        foreach (['ph ph-text-aa' => 'Content', 'ph ph-currency-dollar' => 'Pricing'] as $icon => $tab) {
            $this->assertMatchesRegularExpression('/<span class="title">\s*<span>\s*<span class="'.$icon.'"><\/span>\s*'.$tab.'\s*<\/span>/', $tabs);
        }

        $this->assertMatchesRegularExpression('/id="Form-exampleTabs-outsideTabs"\s+class="form-tabless-fields ">\s*<div\s+class="form-group  text-field span-full[^"]*"\s+data-field-name="code"/', $tabs);
        $this->assertMatchesRegularExpression('/<div class="tab-pane\s+active form-tab-pane[^"]*"\s*>\s*<div\s+class="[^"]*"\s+data-field-name="headline"/', $tabs);
        $this->assertMatchesRegularExpression('/<div class="tab-pane\s+form-tab-pane[^"]*"\s*>\s*<div\s+class="[^"]*"\s+data-field-name="price"/', $tabs);
    }

    public function test_a_lazy_tab_waits_for_its_first_opening(): void
    {
        $lazy = $this->exampleHtml($this->pageHtml(), 'lazy-tabs');

        $this->assertMatchesRegularExpression('/<li class="tab-lazy "\s*>\s*<a href="#primarytab-notes"[^>]*data-tab-name="Notes"\s+data-tab-section="primary"\s+data-tab-lazy-handler="exampleLazyTabs::onLazyLoadTab"/', $lazy);
        $this->assertStringContainsString('class="loading-indicator-container m-t"', $lazy);
        $this->assertStringNotContainsString('name="notes"', $lazy);
        $this->assertStringNotContainsString('Lazy note', $lazy);
    }

    public function test_on_lazy_load_tab_renders_the_fields_of_a_lazy_tab(): void
    {
        $this->makeSample(['notes' => 'Lazy note']);

        $resp = $this->ajax('exampleLazyTabs::onLazyLoadTab', [
            'target' => '#lazy-pane',
            'name' => 'Notes',
            'section' => 'primary',
        ]);

        $resp->assertOk();
        $html = $resp->json('__ajax.ops.0.html');
        $this->assertStringEndsWith('lazy-pane', $resp->json('__ajax.ops.0.selector'));
        $this->assertStringContainsString('name="notes"', $html);
        $this->assertStringContainsString('Lazy note', $html);
    }

    public function test_on_lazy_load_tab_reads_the_primary_tabs_when_no_section_is_posted(): void
    {
        $this->makeSample(['notes' => 'Lazy note']);

        $html = $this->ajax('exampleLazyTabs::onLazyLoadTab', [
            'target' => '#lazy-pane',
            'name' => 'Notes',
        ])->assertOk()->json('__ajax.ops.0.html');

        $this->assertStringContainsString('name="notes"', $html);
        $this->assertStringContainsString('Lazy note', $html);
    }

    public function test_a_form_widget_in_a_lazy_tab_renders_when_the_tab_opens(): void
    {
        $form = $this->makeForm([
            'model' => Sample::factory()->create(['css_code' => '.lamp { color: red; }']),
            'tabs' => [
                'lazy' => ['Code'],
                'fields' => [
                    'name' => ['label' => 'Name', 'tab' => 'General'],
                    'css_code' => ['label' => 'Stylesheet', 'type' => 'codeeditor', 'language' => 'css', 'tab' => 'Code'],
                ],
            ],
        ]);

        $this->assertStringNotContainsString('data-control="codeeditor"', (string) $form->render());

        // The handler reads the tab from the posted data, as the tab's AJAX request sends it
        request()->server->set('REQUEST_METHOD', 'POST');
        request()->request->add(['target' => '#code-pane', 'name' => 'Code']);
        $html = (string) $form->onLazyLoadTab()['#code-pane'];

        $this->assertStringContainsString('data-control="codeeditor"', $html);
        $this->assertStringContainsString('.lamp { color: red; }', $html);
    }

    public function test_a_tab_name_with_an_ampersand_is_escaped_and_keeps_its_icon(): void
    {
        $html = (string) $this->makeForm([
            'tabs' => [
                'icons' => ['Dates & Colors' => 'ph ph-palette'],
                'fields' => [
                    'name' => ['label' => 'Name', 'tab' => 'Dates & Colors'],
                ],
            ],
        ])->render();

        $this->assertMatchesRegularExpression('/<span class="title">\s*<span>\s*<span class="ph ph-palette"><\/span>\s*Dates &amp; Colors\s*<\/span>/', $html);
    }

    public function test_secondary_tabs_open_on_the_active_tab(): void
    {
        $secondary = $this->exampleHtml($this->pageHtml(), 'secondary-tabs');

        $this->assertStringContainsString('class="control-tabs primary-tabs', $secondary);
        $this->assertStringContainsString('class="control-tabs secondary-tabs', $secondary);
        $this->assertMatchesRegularExpression('/<li class="active "\s*>\s*<a href="#secondarytab-body"/', $secondary);
        $this->assertMatchesRegularExpression('/<li class=""\s*>\s*<a href="#secondarytab-description"/', $secondary);
    }

    public function test_spans_place_fields_on_a_side_and_rows_share_a_grid(): void
    {
        $html = $this->pageHtml();
        $spans = $this->exampleHtml($html, 'spans');

        foreach (['city' => 'left', 'postcode' => 'right', 'delivery_address' => 'full', 'state' => 'left', 'country' => 'right'] as $field => $span) {
            $this->assertMatchesRegularExpression('/class="form-group  [a-z]+-field span-'.$span.' [^"]*"\s+data-field-name="'.$field.'"/', $spans);
        }

        $row = $this->exampleHtml($html, 'row-span');
        $this->assertMatchesRegularExpression('/<div class="row w-100">\s*<div\s+class="form-group  text-field span-row md:col-6/', $row);
        $this->assertSame(2, substr_count($row, 'span-row md:col-3'));
    }

    public function test_sections_can_collapse_their_fields(): void
    {
        $sections = $this->exampleHtml($this->pageHtml(), 'sections');

        $this->assertSame(2, preg_match_all('/class="form-group  section-field span-full[^"]*"\s+data-field-name="_[a-z]+_section"\s+data-field-collapsible\s+id=/', $sections));
    }

    public function test_comments_tooltips_and_attributes(): void
    {
        $html = $this->pageHtml();

        $comments = $this->exampleHtml($html, 'comments');
        $this->assertMatchesRegularExpression('/Name\s*<\/label>\s*<p class="form-text before-field">commentAbove sits between the label and the field.<\/p>/', $comments);
        $this->assertStringContainsString('<p class="form-text">With commentHtml, a comment can hold <strong>HTML markup</strong>.</p>', $comments);

        $tooltips = $this->exampleHtml($html, 'tooltips');
        $this->assertMatchesRegularExpression('/<span class="field-tooltip">\s*<i class="ph ph-info"\s+data-bs-toggle="tooltip"\s+data-bs-placement="top"\s+data-bs-title="The shelf the sample is listed on."/', $tooltips);
        $this->assertMatchesRegularExpression('/<i class="ph ph-question"\s+data-bs-toggle="tooltip"\s+data-bs-placement="bottom"\s+data-bs-html="true"\s+data-bs-title="<strong>High<\/strong> ships first/', $tooltips);

        $attributes = $this->exampleHtml($html, 'attributes');
        $this->assertMatchesRegularExpression('/name="reference"[^>]*pattern="REF-\[0-9\]\{4\}" maxlength="8" title="REF, a dash and four digits"/s', $attributes);
    }

    public function test_css_class_and_container_attributes_reach_the_field_container(): void
    {
        $cssClass = $this->exampleHtml($this->pageHtml(), 'css-class');

        $this->assertMatchesRegularExpression('/class="form-group  textarea-field span-left [^"]*reference-forms-highlight"\s+data-field-name="private_notes"/', $cssClass);
        $this->assertMatchesRegularExpression('/data-field-name="api_key"\s+data-secret="api-key"\s+id="Form-exampleCssClass-field-api_key-group"/', $cssClass);
    }

    public function test_horizontal_and_survey_modes(): void
    {
        $html = $this->pageHtml();

        $horizontal = $this->exampleHtml($html, 'horizontal');
        $this->assertMatchesRegularExpression('/data-refresh-handler="exampleHorizontal::onRefresh"\s+class="form-widget form-elements form-horizontal"/', $horizontal);
        $this->assertSame(3, substr_count($horizontal, '<div class="col-horizontal">'));
        $this->assertMatchesRegularExpression('/<label for="Form-exampleHorizontal-field-plan" class="col-form-label">\s*Plan/', $horizontal);

        $survey = $this->exampleHtml($html, 'survey');
        $this->assertMatchesRegularExpression('/<h4 class="my-5 fw-normal">\s*Who it is for\s*<\/h4>/', $survey);
        $this->assertMatchesRegularExpression('/<h4 class="my-5 fw-normal">\s*How it is sold\s*<\/h4>/', $survey);
        $this->assertStringNotContainsString('nav-tabs', $survey);
    }

    public function test_read_only_fields_are_locked_but_post_their_values(): void
    {
        $readOnly = $this->exampleHtml($this->pageHtml(), 'read-only');

        $this->assertMatchesRegularExpression('/name="icon"[^>]*readonly="readonly"/s', $readOnly);
        $this->assertMatchesRegularExpression('/<div class="form-control" readonly>\s*<span class="status-indicator" style="background:#f39c12"><\/span>\s*Low stock\s*<\/div>\s*<input\s+type="hidden"\s+name="stock_status"\s+value="low"/', $readOnly);
        $this->assertMatchesRegularExpression('/id="Form-exampleReadOnly-field-has_discount"[^>]*checked[^>]*readonly="readonly" onclick="return false;"/s', $readOnly);
        $this->assertMatchesRegularExpression('/id="Form-exampleReadOnly-field-is_featured"[^>]*checked[^>]*readonly="readonly" onclick="return false;"/s', $readOnly);
        $this->assertSame(4, substr_count($readOnly, 'onclick="return false"'));
    }

    public function test_a_read_only_dropdown_escapes_the_value_it_posts(): void
    {
        $sample = Sample::factory()->create();
        $sample->name = 'quote"value';

        $html = (string) $this->makeForm([
            'model' => $sample,
            'fields' => [
                'name' => [
                    'label' => 'Name',
                    'type' => 'dropdown',
                    'readOnly' => true,
                    'options' => ['quote"value' => 'Quoted'],
                ],
            ],
        ])->render();

        $this->assertStringContainsString('<div class="form-control" readonly>', $html);
        $this->assertStringContainsString('value="quote&quot;value"', $html);
        $this->assertStringNotContainsString('value="quote"value"', $html);
    }

    public function test_disabled_fields_render_disabled(): void
    {
        $disabled = $this->exampleHtml($this->pageHtml(), 'disabled');

        $this->assertMatchesRegularExpression('/<textarea\s+name="template"[^>]*disabled="disabled">/s', $disabled);
        $this->assertMatchesRegularExpression('/<select\s+id="Form-exampleDisabled-field-step_style"[^>]*disabled="disabled"/s', $disabled);
    }

    public function test_preview_mode_renders_values_as_text(): void
    {
        $preview = $this->exampleHtml($this->pageHtml(), 'preview');

        $this->assertMatchesRegularExpression('/class="form-group form-group-preview text-field span-left  is-required  "\s+data-field-name="name"/', $preview);
        $this->assertMatchesRegularExpression('/<!-- Text -->\s*<span class="form-control">Aurora Desk Lamp<\/span>/', $preview);
        $this->assertMatchesRegularExpression('/<div class="form-control" >\s*<i class="ph ph-truck" aria-hidden="true"><\/i>\s*Road\s*<\/div>/', $preview);
        $this->assertStringContainsString('class="control-balloon-selector control-disabled"', $preview);
        $this->assertSame(2, preg_match_all('/name="Preview\[features\]\[\]"[^>]*disabled\s+checked/s', $preview));
        $this->assertStringNotContainsString('<input type="text"', $preview);
        $this->assertStringNotContainsString('<select', $preview);
    }
}
