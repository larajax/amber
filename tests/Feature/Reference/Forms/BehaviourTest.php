<?php

namespace Tests\Feature\Reference\Forms;

use App\Controllers\Reference\FormsController;
use App\Models\Sample;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Larajax\Ui\Classes\FormField;
use Larajax\Ui\Widgets\Form;
use Tests\TestCase;

class BehaviourTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var array<int, string> examples on the page that edit the reference sample, one YAML file each.
     */
    protected array $examples = [
        'validation', 'dependent-fields',
        'trigger-checked', 'trigger-value', 'trigger-actions', 'trigger-multi-value', 'trigger-parent',
        'change-handler', 'refresh', 'nested-names', 'contexts',
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
            'reference' => 'REF-1001',
            'country' => 'au',
            'state' => 'nsw',
            'headline' => 'The desk lamp that bends to your work',
            'price' => 20,
            'quantity' => 2,
            'seo' => ['title' => 'Aurora SEO title', 'robots' => 'index'],
            'packaging' => ['boxes' => 1, 'crate_notes' => ''],
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

        return $this->get(route('reference.forms.behaviour'))->assertOk()->getContent();
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
        ])->post(route('reference.forms.behaviour'), $data);
    }

    /**
     * triggerSelector returns the data-trigger selector a field renders for a trigger on another field.
     */
    protected function triggerSelector(?string $arrayName, string $triggerField, string $action = 'show'): string
    {
        $field = new FormField([
            'fieldName' => 'details',
            'arrayName' => $arrayName,
            'trigger' => ['action' => $action, 'field' => $triggerField, 'condition' => 'checked'],
        ]);

        $position = in_array($action, ['show', 'hide']) ? 'container' : 'field';

        return $field->getAttributes($position, false)['data-trigger'];
    }

    public function test_the_page_renders_every_example_beside_its_source(): void
    {
        $html = $this->pageHtml();

        foreach ([...$this->examples, 'defaults', 'presets'] as $example) {
            $this->assertStringContainsString("resources/ui/reference/forms/behaviour/{$example}.yaml", $html);
        }

        foreach ($this->examples as $example) {
            $this->assertStringContainsString('data-refresh-handler="example'.str_replace('-', '', ucwords($example, '-')).'::onRefresh"', $html);
        }

        $this->assertStringContainsString('resources/views/reference/forms/_field_stock_value.php', $html);
        $this->assertStringContainsString('app/Controllers/Reference/FormsController/BehaviourHandlers.php', $html);
        $this->assertStringContainsString('data-input-preset="#presetClassTitle"', $html);
        $this->assertStringContainsString('data-trigger="#triggerAgreeTerms"', $html);
    }

    public function test_saving_the_page_stores_every_example_on_the_reference_sample(): void
    {
        $sample = $this->makeSample(['status' => 'active']);

        $this->ajax('onSaveExamples', [
            'name' => 'Ember Wall Clock',
            'email' => 'ember@example.com',
            'code' => '',
            'country' => 'us',
            'state' => 'ny',
            'city' => 'buffalo',
            'postcode' => '14201',
            'has_discount' => '1',
            'discount_code' => 'EMBER10',
            'discount_percent' => '10',
            'delivery' => 'courier_express',
            'delivery_address' => '3 Bell Lane',
            'is_featured' => '0',
            'priority' => 'low',
            'excerpt' => 'A brass clock.',
            'notes' => 'Waiting on photos',
            'features' => ['parking'],
            'summary' => 'Two spaces out the back',
            'size' => 'l',
            'packaging' => ['boxes' => '3', 'crate_notes' => 'Use the large crate'],
            'headline' => 'A clock for every wall',
            'price' => '42.50',
            'quantity' => '4',
            'seo' => ['title' => 'Ember SEO title', 'robots' => 'noindex', 'description' => 'A brass wall clock.'],
            'reference' => 'REF-9999',
            'NewSample' => ['title' => 'Ignored', 'status' => 'archived'],
        ])->assertOk();

        $sample->refresh();
        $this->assertSame('Ember Wall Clock', $sample->name);
        $this->assertSame('ember@example.com', $sample->email);
        $this->assertSame('', (string) $sample->code);
        $this->assertSame(['us', 'ny', 'buffalo', '14201'], [$sample->country, $sample->state, $sample->city, $sample->postcode]);
        $this->assertTrue($sample->has_discount);
        $this->assertSame('EMBER10', $sample->discount_code);
        $this->assertSame(10, (int) $sample->discount_percent);
        $this->assertSame('courier_express', $sample->delivery);
        $this->assertSame('3 Bell Lane', $sample->delivery_address);
        $this->assertFalse($sample->is_featured);
        $this->assertSame('low', $sample->priority);
        $this->assertSame('A brass clock.', $sample->excerpt);
        $this->assertSame('Waiting on photos', $sample->notes);
        $this->assertSame(['parking'], $sample->features);
        $this->assertSame('Two spaces out the back', $sample->summary);
        $this->assertSame('l', $sample->size);
        $this->assertEquals(['boxes' => 3, 'crate_notes' => 'Use the large crate'], $sample->packaging);
        $this->assertSame('A clock for every wall', $sample->headline);
        $this->assertEquals(42.5, (float) $sample->price);
        $this->assertSame(['title' => 'Ember SEO title', 'robots' => 'noindex', 'description' => 'A brass wall clock.'], $sample->seo);

        // The update context makes the reference read-only, yet it still saves the posted value
        $this->assertSame('REF-9999', $sample->reference);

        // The new record examples are left out
        $this->assertSame('active', $sample->status);
        $this->assertSame(1, Sample::count());
    }

    public function test_saving_requires_a_name(): void
    {
        $sample = $this->makeSample();

        $this->ajax('onSaveExamples', ['name' => ''])->assertStatus(422);

        $this->assertSame('Aurora Desk Lamp', $sample->fresh()->name);
    }

    public function test_saving_checks_the_email_rule(): void
    {
        $sample = $this->makeSample(['email' => 'lighting@example.com']);

        $this->ajax('onSaveExamples', ['email' => 'not an email'])->assertStatus(422);

        $this->assertSame('lighting@example.com', $sample->fresh()->email);
    }

    public function test_required_markers_come_from_the_rules_or_the_required_property(): void
    {
        $validation = $this->exampleHtml($this->pageHtml(), 'validation');

        $this->assertMatchesRegularExpression('/class="form-group  text-field span-left  is-required  "\s+data-field-name="name"/', $validation);
        $this->assertMatchesRegularExpression('/class="form-group  text-field span-full  is-required  "\s+data-field-name="code"/', $validation);
        $this->assertMatchesRegularExpression('/class="form-group  email-field span-right    "\s+data-field-name="email"/', $validation);
        $this->assertMatchesRegularExpression('/name="name"[^>]*autofocus="autofocus"/s', $validation);
        $this->assertSame(1, substr_count($validation, 'autofocus='));
    }

    public function test_dependent_fields_name_the_fields_they_follow(): void
    {
        $dependent = $this->exampleHtml($this->pageHtml(), 'dependent-fields');

        $this->assertStringContainsString('data-refresh-handler="exampleDependentFields::onRefresh"', $dependent);
        $this->assertMatchesRegularExpression('/data-field-depends="\[&quot;country&quot;\]"\s+data-field-name="state"/', $dependent);
        $this->assertMatchesRegularExpression('/data-field-depends="\[&quot;country&quot;,&quot;state&quot;\]"\s+data-field-name="city"/', $dependent);
        $this->assertMatchesRegularExpression('/<option\s+selected="selected"\s+value="nsw"\s*>New South Wales<\/option>/', $dependent);
    }

    public function test_filter_fields_hides_the_postcode_until_a_country_is_picked(): void
    {
        $html = $this->pageHtml(['country' => null, 'state' => null]);

        $this->assertDoesNotMatchRegularExpression('/<input[^>]*name="postcode"/', $html);

        Sample::query()->update(['country' => 'us']);
        $html = $this->get(route('reference.forms.behaviour'))->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/<input[^>]*name="postcode"/', $html);
        $this->assertMatchesRegularExpression('/ZIP Code\s*<\/label>/', $html);
    }

    public function test_refreshing_the_state_reloads_its_options_for_the_posted_country(): void
    {
        $sample = $this->makeSample();

        $resp = $this->ajax('exampleDependentFields::onRefresh', [
            'country' => 'us',
            'fields' => ['state', 'postcode'],
        ]);

        $resp->assertOk();
        $ops = $resp->json('__ajax.ops');
        $this->assertCount(2, $ops);
        $this->assertStringEndsWith('-field-state-group', $ops[0]['selector']);
        $html = collect($ops)->pluck('html')->implode('');
        $this->assertStringContainsString('California', $html);
        $this->assertStringNotContainsString('New South Wales', $html);
        $this->assertStringContainsString('ZIP Code', $html);
        $this->assertSame('au', $sample->fresh()->country);
    }

    public function test_triggers_render_on_the_container_or_the_input(): void
    {
        $html = $this->pageHtml();

        $checked = $this->exampleHtml($html, 'trigger-checked');
        $this->assertMatchesRegularExpression('/data-field-name="discount_code"\s+data-trigger="\[name=&quot;has_discount&quot;\]" data-trigger-action="show" data-trigger-condition="checked" data-trigger-closest-parent="form, div\[data-control=&quot;formwidget&quot;\]"/', $checked);
        $this->assertMatchesRegularExpression('/name="discount_percent"[^>]*data-trigger="\[name=&quot;has_discount&quot;\]" data-trigger-action="enable" data-trigger-condition="checked"/s', $checked);

        $value = $this->exampleHtml($html, 'trigger-value');
        $this->assertStringContainsString('data-trigger-condition="value[courier*][post]"', $value);
        $this->assertStringContainsString('data-trigger-condition="value[pickup]"', $value);
        $this->assertStringContainsString('data-trigger-condition="value[]"', $value);

        $actions = $this->exampleHtml($html, 'trigger-actions');
        $this->assertStringContainsString('data-trigger-action="fill[high]"', $actions);
        $this->assertMatchesRegularExpression('/name="excerpt"[^>]*data-trigger-action="disable" data-trigger-condition="unchecked"/s', $actions);
        $this->assertStringContainsString('data-trigger-action="hide|empty"', $actions);

        $multi = $this->exampleHtml($html, 'trigger-multi-value');
        $this->assertStringContainsString('data-trigger="[name=&quot;features[]&quot;]" data-trigger-action="show" data-trigger-condition="value[parking]"', $multi);

        // A parent reference from inside the nested form names the top form's field
        $parent = $this->exampleHtml($html, 'trigger-parent');
        $this->assertMatchesRegularExpression('/data-field-name="crate_notes"\s+data-trigger="\[name=&quot;size&quot;\]" data-trigger-action="show" data-trigger-condition="value\[l\]"/', $parent);
        $this->assertStringContainsString('name="packaging[crate_notes]"', $parent);
    }

    public function test_a_trigger_names_a_sibling_field_inside_the_same_form(): void
    {
        $this->assertSame('[name="has_discount"]', $this->triggerSelector(null, 'has_discount'));
        $this->assertSame('[name="Sample[has_discount]"]', $this->triggerSelector('Sample', 'has_discount'));
        $this->assertSame('[name="steps[0][title]"]', $this->triggerSelector('steps[0]', 'title', 'enable'));
        $this->assertSame('[name="tags[]"]', $this->triggerSelector(null, 'tags[]'));
    }

    public function test_a_parent_reference_from_a_repeater_item_reaches_the_top_form(): void
    {
        $this->assertSame('[name="step_style"]', $this->triggerSelector('steps[0]', '^step_style'));
        $this->assertSame('[name="step_style"]', $this->triggerSelector('steps[12]', '^step_style', 'enable'));
        $this->assertSame('[name="Sample[step_style]"]', $this->triggerSelector('Sample[steps][0]', '^step_style'));
    }

    public function test_parent_references_climb_one_nested_level_each(): void
    {
        $this->assertSame('[name="content[0][title]"]', $this->triggerSelector('content[0][items][1]', '^title'));
        $this->assertSame('[name="name"]', $this->triggerSelector('content[0][items][1]', '^^name'));
        $this->assertSame('[name="Sample[name]"]', $this->triggerSelector('Sample[content][0][items][1]', '^^name'));
    }

    public function test_a_parent_reference_from_a_nested_form_climbs_its_single_name_level(): void
    {
        $this->assertSame('[name="country"]', $this->triggerSelector('address', '^country'));
        $this->assertSame('[name="Sample[country]"]', $this->triggerSelector('Sample[address]', '^country'));
        $this->assertSame('[name="steps[0][title]"]', $this->triggerSelector('steps[0][address]', '^title'));
        $this->assertSame('[name="tags[]"]', $this->triggerSelector('steps[0]', '^tags[]'));
    }

    public function test_the_headline_field_names_its_change_handler(): void
    {
        $html = $this->exampleHtml($this->pageHtml(), 'change-handler');

        $this->assertMatchesRegularExpression('/data-change-handler="onChangeHeadline"\s+data-field-name="headline"/', $html);
        $this->assertMatchesRegularExpression('/<span class="badge theme-secondary">\s*37 characters\s*<\/span>/', $html);
    }

    public function test_the_headline_change_handler_refreshes_the_stats_partial(): void
    {
        $sample = $this->makeSample();

        $resp = $this->ajax('onChangeHeadline', [
            'headline' => 'A very long headline that goes on and on well past the sixty character limit',
        ]);

        $resp->assertOk();
        $op = $resp->json('__ajax.ops.0');
        $this->assertStringEndsWith('-field-_headline_stats-group', $op['selector']);
        $this->assertStringContainsString('76 characters', $op['html']);
        $this->assertStringContainsString('Too long', $op['html']);

        // The handler only previews the value, it does not save it
        $this->assertSame('The desk lamp that bends to your work', $sample->fresh()->headline);
    }

    public function test_the_stock_value_partial_asks_the_form_to_refresh_it(): void
    {
        $html = $this->exampleHtml($this->pageHtml(['price' => 20, 'quantity' => 2]), 'refresh');

        $this->assertStringContainsString('data-request="exampleRefresh::onRefresh" data-request-data="{&quot;fields&quot;:[&quot;_stock_value&quot;]}"', $html);
        $this->assertMatchesRegularExpression('/<strong>\s*\$40.00\s*<\/strong>/', $html);
    }

    public function test_on_refresh_renders_the_requested_fields_with_the_posted_values(): void
    {
        $sample = $this->makeSample(['price' => 20, 'quantity' => 2]);

        $resp = $this->ajax('exampleRefresh::onRefresh', [
            'fields' => ['_stock_value'],
            'price' => '10.50',
            'quantity' => '3',
        ]);

        $resp->assertOk();
        $ops = $resp->json('__ajax.ops');
        $this->assertCount(1, $ops);
        $this->assertStringEndsWith('-field-_stock_value-group', $ops[0]['selector']);
        $this->assertMatchesRegularExpression('/<strong>\s*\$31.50\s*<\/strong>/', $ops[0]['html']);
        $this->assertEquals(20, (float) $sample->fresh()->price);
    }

    public function test_on_refresh_without_fields_renders_the_whole_form(): void
    {
        $this->makeSample();

        $resp = $this->ajax('exampleRefresh::onRefresh', ['price' => '5', 'quantity' => '2']);

        $resp->assertOk();
        $this->assertSame('#Form-exampleRefresh', $resp->json('__ajax.ops.0.selector'));
        $html = $resp->json('__ajax.ops.0.html');
        $this->assertStringContainsString('name="price"', $html);
        $this->assertStringContainsString('name="quantity"', $html);
        $this->assertMatchesRegularExpression('/<strong>\s*\$10.00\s*<\/strong>/', $html);
    }

    public function test_nested_names_read_keys_of_an_array_attribute(): void
    {
        $nested = $this->exampleHtml($this->pageHtml(), 'nested-names');

        $this->assertMatchesRegularExpression('/name="seo\[title\]"[^>]*value="Aurora SEO title"/s', $nested);
        $this->assertMatchesRegularExpression('/<option\s+selected="selected"\s+value="index"/', $nested);
    }

    public function test_context_specific_fields_follow_the_form_context(): void
    {
        $contexts = $this->exampleHtml($this->pageHtml(['reference' => 'REF-1001']), 'contexts');

        [$updateForm, $createForm] = explode('The same YAML on a new record', $contexts);

        // The saved record is in the update context
        $this->assertStringContainsString('This hint has context update', $updateForm);
        $this->assertStringNotContainsString('<p>This hint has context create.</p>', $updateForm);
        $this->assertMatchesRegularExpression('/name="reference"[^>]*value="REF-1001"[^>]*readonly/s', $updateForm);

        // The new record is in the create context
        $this->assertStringContainsString('<p>This hint has context create.</p>', $createForm);
        $this->assertStringNotContainsString('This hint has context update', $createForm);
        $this->assertMatchesRegularExpression('/name="NewSample\[reference\]"[^>]*value=""[^>]*placeholder="REF-0001"/s', $createForm);
        $this->assertDoesNotMatchRegularExpression('/name="NewSample\[reference\]"[^>]*readonly/', $createForm);
    }

    public function test_defaults_fill_a_new_record(): void
    {
        $defaults = $this->exampleHtml($this->pageHtml(), 'defaults');

        $this->assertMatchesRegularExpression('/<input\s+type="hidden"\s+name="NewSample\[status\]"\s+value="draft"/', $defaults);
        $this->assertMatchesRegularExpression('/<option\s+selected="selected"\s+value="normal"/', $defaults);
        $this->assertMatchesRegularExpression('/name="NewSample\[quantity\]"\s+id="[^"]+"\s+value="1"/', $defaults);
        $this->assertMatchesRegularExpression('/name="NewSample\[is_featured\]"\s+value="1"\s+checked/', $defaults);
        $this->assertMatchesRegularExpression('/name="NewSample\[headline\]"\s+id="[^"]+"\s+value="Untitled sample"/', $defaults);
    }

    public function test_defaults_never_replace_saved_values(): void
    {
        $sample = $this->makeSample(['status' => 'active', 'quantity' => 9, 'headline' => 'Saved headline']);

        $form = Form::createIn(new FormsController, [
            'model' => $sample,
            'context' => 'update',
            'fields' => '~/resources/ui/reference/forms/behaviour/defaults.yaml',
        ]);

        $form->bindToController();
        $html = (string) $form->render();

        $this->assertMatchesRegularExpression('/<input\s+type="hidden"\s+name="status"\s+value="active"/', $html);
        $this->assertMatchesRegularExpression('/name="quantity"\s+id="[^"]+"\s+value="9"/', $html);
        $this->assertMatchesRegularExpression('/name="headline"\s+id="[^"]+"\s+value="Saved headline"/', $html);
    }

    public function test_presets_follow_the_title_of_the_new_record(): void
    {
        $presets = $this->exampleHtml($this->pageHtml(), 'presets');

        foreach (['slug' => 'slug', 'url' => 'url', 'code' => 'camel', 'file_name' => 'file', 'display_title' => 'exact'] as $field => $type) {
            $this->assertMatchesRegularExpression('/name="NewSample\['.$field.'\]"[^>]*data-input-preset="\[name=&quot;NewSample\[title\]&quot;\]" data-input-preset-type="'.$type.'" data-input-preset-closest-parent="form"/s', $presets);
        }
    }
}
