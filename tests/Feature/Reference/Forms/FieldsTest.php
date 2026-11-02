<?php

namespace Tests\Feature\Reference\Forms;

use App\Controllers\Reference\FormsController;
use App\Models\Sample;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Larajax\Ui\Widgets\Filter;
use Larajax\Ui\Widgets\Form;
use Tests\TestCase;

class FieldsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var array<int, string> examples on the page, one YAML file each.
     */
    protected array $examples = [
        'text', 'email', 'number', 'number-format', 'password', 'password-reveal', 'adorn-text', 'adorn-icons', 'textarea', 'textarea-sizes',
        'dropdown', 'dropdown-model-options', 'dropdown-preset', 'option-styles',
        'radio', 'checkbox', 'switch', 'checkboxlist', 'checkboxlist-nested', 'checkboxlist-scrollable',
        'selector', 'selector-allow-empty', 'selector-read-only',
        'section', 'hint', 'hint-partial', 'ruler', 'partial', 'partial-ajax',
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
            'status' => 'active',
            'visibility' => 'public',
            'audience' => 'retail',
            'level' => 'basic',
            'currency' => 'aud',
            'stock_status' => 'low',
            'transport' => 'road',
            'icon' => 'ph ph-lamp',
            'timezone' => 'Australia/Sydney',
        ]);
    }

    /**
     * renderField renders one field on the given sample in a form outside the page.
     *
     * @param  array<string, mixed>  $config
     */
    protected function renderField(Sample $sample, string $name, array $config): string
    {
        $form = Form::createIn(new FormsController, [
            'model' => $sample,
            'fields' => [$name => $config],
        ]);

        $form->bindToController();

        return (string) $form->render();
    }

    /**
     * pageHtml renders the page for a sample made with the given attributes.
     *
     * @param  array<string, mixed>  $attributes
     */
    protected function pageHtml(array $attributes = []): string
    {
        $this->makeSample($attributes);

        return $this->get(route('reference.forms.fields'))->assertOk()->getContent();
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
        ])->post(route('reference.forms.fields'), $data);
    }

    public function test_the_page_renders_every_example_beside_its_yaml(): void
    {
        $html = $this->pageHtml();

        foreach ($this->examples as $example) {
            $this->assertStringContainsString("resources/ui/reference/forms/fields/{$example}.yaml", $html);
        }

        $this->assertSame(count($this->examples), substr_count($html, 'data-control="formwidget"'));
        $this->assertStringContainsString('resources/views/reference/forms/_field_record_card.php', $html);
        $this->assertStringContainsString('resources/views/reference/forms/_field_timezone.php', $html);
        $this->assertStringContainsString('data-request="onSaveExamples"', $html);
        $this->assertMatchesRegularExpression('/name="_session_key" value="[^"]{40}"/', $html);
    }

    public function test_saving_the_page_stores_every_example_on_the_reference_sample(): void
    {
        $sample = $this->makeSample();

        $this->ajax('onSaveExamples', [
            'code' => 'emberClock',
            'headline' => 'A clock for every wall',
            'email' => 'ember@example.com',
            'price' => '42.50',
            'quantity' => '7',
            '_password' => 'never-stored',
            'description' => 'A brass wall clock.',
            'priority' => 'high',
            'delivery' => 'post',
            'country' => 'nz',
            'category' => 'desks',
            'currency' => 'usd',
            'icon' => 'ph ph-clock',
            'stock_status' => 'out',
            'transport' => 'sea',
            'level' => 'pro-monthly',
            'size' => 'l',
            'plan' => 'business',
            'has_discount' => '1',
            'is_featured' => '0',
            'features' => ['wifi', 'gym'],
            'permissions' => ['users', 'users_invite'],
            'interests' => ['sailing'],
            'status' => 'archived',
            'visibility' => '',
            'audience' => 'trade',
            'timezone' => 'Pacific/Auckland',
        ])->assertOk();

        $sample->refresh();
        $this->assertSame('emberClock', $sample->code);
        $this->assertSame('A clock for every wall', $sample->headline);
        $this->assertSame('ember@example.com', $sample->email);
        $this->assertEquals(42.5, (float) $sample->price);
        $this->assertSame(7, (int) $sample->quantity);
        $this->assertSame('A brass wall clock.', $sample->description);
        $this->assertSame('high', $sample->priority);
        $this->assertSame('post', $sample->delivery);
        $this->assertSame('nz', $sample->country);
        $this->assertSame('desks', $sample->category);
        $this->assertSame('usd', $sample->currency);
        $this->assertSame('ph ph-clock', $sample->icon);
        $this->assertSame('out', $sample->stock_status);
        $this->assertSame('sea', $sample->transport);
        $this->assertSame('pro-monthly', $sample->level);
        $this->assertSame('l', $sample->size);
        $this->assertSame('business', $sample->plan);
        $this->assertTrue($sample->has_discount);
        $this->assertFalse($sample->is_featured);
        $this->assertSame(['wifi', 'gym'], $sample->features);
        $this->assertSame(['users', 'users_invite'], $sample->permissions);
        $this->assertSame(['sailing'], $sample->interests);
        $this->assertSame('archived', $sample->status);
        $this->assertSame('', (string) $sample->visibility);
        $this->assertSame('Pacific/Auckland', $sample->timezone);
        $this->assertArrayNotHasKey('_password', $sample->getAttributes());

        // The read-only selector still posts and saves its value
        $this->assertSame('trade', $sample->audience);
    }

    public function test_saving_an_emptied_number_field_stores_null(): void
    {
        $sample = $this->makeSample(['price' => 89.5, 'quantity' => 24]);

        $this->ajax('onSaveExamples', ['price' => '', 'quantity' => ''])->assertOk();

        $sample->refresh();
        $this->assertNull($sample->price);
        $this->assertNull($sample->quantity);
    }

    public function test_text_number_password_and_textarea_render_their_inputs(): void
    {
        $html = $this->pageHtml(['price' => 89.5, 'quantity' => 24]);

        $this->assertMatchesRegularExpression('/type="text"\s+name="code"[^>]*placeholder="auroraDeskLamp"/', $html);
        $this->assertMatchesRegularExpression('/type="email"\s+name="email"[^>]*placeholder="name@example.com"/', $html);
        $this->assertMatchesRegularExpression('/type="text"\s+inputmode="decimal"\s+name="price"\s+id="[^"]+"\s+value="89.50?"[^>]*data-control="number-input"\s+data-min="0"\s+data-step="0.01"/', $html);
        $this->assertMatchesRegularExpression('/type="text"\s+inputmode="numeric"\s+name="quantity"[^>]*data-control="number-input"\s+data-min="0"\s+data-max="999"\s+data-step="1"/s', $html);
        $this->assertMatchesRegularExpression('/type="password"\s+name="_password"[^>]*value=""/', $html);

        foreach (['tiny' => 'excerpt', 'small' => 'summary', 'large' => 'description', 'huge' => 'body', 'giant' => 'notes'] as $size => $name) {
            $this->assertMatchesRegularExpression('/<textarea\s+name="'.$name.'"[^>]*class="form-control field-textarea size-'.$size.'"/', $html);
        }
    }

    public function test_prefix_and_suffix_text_wrap_the_input_in_an_adorned_field(): void
    {
        $html = $this->pageHtml(['shipping_fee' => 12.5, 'discount_percent' => 10]);

        $this->assertMatchesRegularExpression('/<label class="form-control form-adorn">\s*<span class="form-adorn-text">\s*\$\s*<\/span>\s*<input\s+type="text"[^>]*name="shipping_fee"[^>]*class="form-ghost[^"]*"[^>]*\/>\s*<span class="form-adorn-text">\s*AUD\s*<\/span>\s*<\/label>/s', $html);
        $this->assertMatchesRegularExpression('/name="discount_percent"[^>]*class="form-ghost[^"]*"[^>]*\/>\s*<span class="form-adorn-text">\s*%\s*<\/span>\s*<\/label>/s', $html);
    }

    public function test_prefix_and_suffix_icons_sit_before_and_after_the_input(): void
    {
        $html = $this->pageHtml(['support_email' => 'help@aurora.test', 'reference' => 'REF-1001']);

        $this->assertMatchesRegularExpression('/<label class="form-control form-adorn">\s*<span class="form-adorn-icon" aria-hidden="true">\s*<i class="ph ph-envelope-simple"><\/i>\s*<\/span>\s*<input\s+type="email"\s+name="support_email"[^>]*class="form-ghost"/s', $html);
        $this->assertMatchesRegularExpression('/name="reference"[^>]*class="form-ghost"[^>]*\/>\s*<span class="form-adorn-icon" aria-hidden="true">\s*<i class="ph ph-tag"><\/i>\s*<\/span>\s*<\/label>/s', $html);
        $this->assertMatchesRegularExpression('/<i class="ph ph-lock-simple"><\/i>\s*<\/span>\s*<input\s+type="password"\s+name="_adorned_password"/s', $html);
    }

    public function test_a_field_without_adornment_keeps_its_plain_input(): void
    {
        $html = $this->renderField($this->makeSample(), 'code', ['label' => 'Code']);

        $this->assertStringNotContainsString('form-adorn', $html);
        $this->assertMatchesRegularExpression('/name="code"[^>]*class="form-control"/', $html);
    }

    public function test_adornments_show_around_the_value_in_preview_mode(): void
    {
        $form = Form::createIn(new FormsController, [
            'model' => $this->makeSample(['shipping_fee' => 12.5]),
            'previewMode' => true,
            'fields' => ['shipping_fee' => ['label' => 'Shipping Fee', 'type' => 'number', 'prefix' => '$', 'suffix' => 'AUD']],
        ]);
        $form->bindToController();

        $this->assertMatchesRegularExpression('/<span class="form-control form-adorn">\s*<span class="form-adorn-text">\s*\$\s*<\/span>\s*<span\s+class="form-ghost"\s*>12.50?<\/span>\s*<span class="form-adorn-text">\s*AUD\s*<\/span>\s*<\/span>/', (string) $form->render());
    }

    public function test_an_adornment_set_at_runtime_renders_like_one_from_yaml(): void
    {
        $form = Form::createIn(new FormsController, [
            'model' => $this->makeSample(),
            'fields' => ['shipping_fee' => ['label' => 'Shipping Fee', 'type' => 'number']],
        ]);
        $form->bindToController();
        $form->getField('shipping_fee')->prefix = '€';

        $this->assertMatchesRegularExpression('/<span class="form-adorn-text">\s*€\s*<\/span>\s*<input\s+type="text"\s+inputmode="decimal"/', (string) $form->render());
    }

    public function test_saving_an_adorned_field_stores_the_bare_value(): void
    {
        $sample = $this->makeSample(['shipping_fee' => 12.5]);

        $this->ajax('onSaveExamples', ['shipping_fee' => '9.95', 'discount_percent' => '15'])->assertOk();

        $sample->refresh();
        $this->assertEquals(9.95, (float) $sample->shipping_fee);
        $this->assertSame(15, (int) $sample->discount_percent);
    }

    public function test_a_formatted_number_shows_separators_and_posts_the_plain_number_from_a_hidden_input(): void
    {
        $html = $this->pageHtml(['annual_revenue' => 1250000, 'units_sold' => 48250]);

        $this->assertMatchesRegularExpression('/<span class="form-adorn-text">\s*\$\s*<\/span>\s*<input\s+type="text"\s+inputmode="decimal"\s+id="[^"]+"\s+value="1,250,000.00"[^>]*data-control="number-input"\s+data-min="0"\s+data-number-format="\{&quot;locale&quot;:&quot;en&quot;,&quot;decimals&quot;:2,&quot;grouping&quot;:true\}"[^>]*\/>\s*<input\s+type="hidden"\s+name="annual_revenue"\s+value="1250000(\.00)?"\s+data-number-value/', $html);
        $this->assertMatchesRegularExpression('/<input\s+type="text"\s+inputmode="numeric"\s+id="[^"]+"\s+value="48,250"[^>]*\/>\s*<input\s+type="hidden"\s+name="units_sold"\s+value="48250"/', $html);
    }

    public function test_a_formatted_number_in_another_locale_carries_the_locale_for_the_browser(): void
    {
        $html = $this->pageHtml(['export_price' => 1299]);
        $shown = class_exists(\NumberFormatter::class) ? '1.299,00' : '1299(\.00)?';

        $this->assertMatchesRegularExpression('/value="'.$shown.'"[^>]*data-number-format="\{&quot;locale&quot;:&quot;de-DE&quot;,&quot;decimals&quot;:2,&quot;grouping&quot;:true\}"[^>]*\/>\s*<input\s+type="hidden"\s+name="export_price"\s+value="1299(\.00)?"/', $html);
    }

    public function test_a_formatted_number_keeps_every_digit_and_rounds_only_what_it_shows(): void
    {
        $fields = ['annual_revenue' => ['label' => 'Revenue', 'type' => 'number', 'format' => ['decimals' => 2]]];

        $long = $this->renderField(new Sample(['annual_revenue' => '-12345678901234567.5']), 'annual_revenue', $fields['annual_revenue']);
        $this->assertMatchesRegularExpression('/value="-12,345,678,901,234,567.50"[^>]*\/>\s*<input\s+type="hidden"\s+name="annual_revenue"\s+value="-12345678901234567.5"/', $long);

        $rounded = $this->renderField(new Sample(['annual_revenue' => '999.995']), 'annual_revenue', $fields['annual_revenue']);
        $this->assertMatchesRegularExpression('/value="1,000.00"[^>]*\/>\s*<input\s+type="hidden"\s+name="annual_revenue"\s+value="999.995"/', $rounded);
    }

    public function test_a_formatted_number_shows_formatted_in_preview_mode(): void
    {
        $form = Form::createIn(new FormsController, [
            'model' => $this->makeSample(['annual_revenue' => 1250000]),
            'previewMode' => true,
            'fields' => ['annual_revenue' => ['label' => 'Revenue', 'type' => 'number', 'prefix' => '$', 'format' => ['decimals' => 2]]],
        ]);
        $form->bindToController();
        $html = (string) $form->render();

        $this->assertMatchesRegularExpression('/<span\s+class="form-ghost"\s+data-control="number-input"\s+data-number-format="[^"]+"\s+data-number-value="1250000(\.00)?"\s*>1,250,000.00<\/span>/', $html);
        $this->assertStringNotContainsString('type="hidden"', $html);
    }

    public function test_a_number_without_a_format_keeps_its_name_on_the_input(): void
    {
        $html = $this->renderField($this->makeSample(['price' => 89.5]), 'price', ['label' => 'Price', 'type' => 'number']);

        $this->assertMatchesRegularExpression('/<input\s+type="text"\s+inputmode="decimal"\s+name="price"/', $html);
        $this->assertStringNotContainsString('data-number-value', $html);
        $this->assertStringNotContainsString('data-number-format', $html);
    }

    public function test_a_disabled_formatted_number_posts_nothing(): void
    {
        $html = $this->renderField($this->makeSample(['annual_revenue' => 5]), 'annual_revenue', [
            'label' => 'Revenue',
            'type' => 'number',
            'disabled' => true,
            'format' => true,
        ]);

        $this->assertMatchesRegularExpression('/<input\s+type="hidden"\s+name="annual_revenue"\s+value="5(\.00)?"\s+data-number-value\s+disabled/', $html);
    }

    public function test_a_number_saves_the_digits_as_typed_and_an_empty_one_as_null(): void
    {
        $form = Form::createIn(new FormsController, [
            'model' => new Sample,
            'fields' => [
                'annual_revenue' => ['type' => 'number', 'format' => true],
                'units_sold' => ['type' => 'number'],
                'price' => ['type' => 'number'],
            ],
        ]);
        $form->bindToController();
        $form->setSaveDataOverride('annual_revenue', ' 12345678901234567.50 ');
        $form->setSaveDataOverride('units_sold', '');
        $form->setSaveDataOverride('price', '12.50');

        $data = $form->getSaveData();

        $this->assertSame('12345678901234567.50', $data['annual_revenue']);
        $this->assertNull($data['units_sold']);
        $this->assertSame('12.50', $data['price']);
    }

    public function test_saving_the_formatted_numbers_stores_plain_numbers(): void
    {
        $sample = $this->makeSample();

        $this->ajax('onSaveExamples', ['annual_revenue' => '1250000.50', 'units_sold' => '48251', 'export_price' => '1299.5'])->assertOk();

        $sample->refresh();
        $this->assertEquals(1250000.5, (float) $sample->annual_revenue);
        $this->assertSame(48251, (int) $sample->units_sold);
        $this->assertEquals(1299.5, (float) $sample->export_price);
    }

    public function test_a_password_ends_its_field_with_a_show_button_by_default(): void
    {
        $html = $this->pageHtml();

        $this->assertMatchesRegularExpression('/<label class="form-control form-adorn">\s*<input\s+type="password"\s+name="_new_password"\s+id="([^"]+)"[^>]*class="form-ghost"[^>]*\/>\s*<button\s+type="button"\s+class="btn-icon"\s+aria-label="Show password"\s+aria-pressed="false"\s+aria-controls="\1"\s+data-control="password-reveal"\s+data-icon-show="ph ph-eye"\s+data-icon-hide="ph ph-eye-slash"\s*>\s*<i [^>]*><\/i>\s*<\/button>\s*<\/label>/', $html);
        $this->assertMatchesRegularExpression('/<i (?=[^>]*class="ph ph-eye")(?=[^>]*data-reveal-icon)(?=[^>]*aria-hidden="true")[^>]*><\/i>\s*<\/button>/', $html);
    }

    public function test_a_password_keeps_its_prefix_icon_before_the_input_and_the_show_button_after(): void
    {
        $html = $this->pageHtml();

        $this->assertMatchesRegularExpression('/<i class="ph ph-lock-simple"><\/i>\s*<\/span>\s*<input\s+type="password"\s+name="_adorned_password"[^>]*class="form-ghost"[^>]*\/>\s*<button\s+type="button"\s+class="btn-icon"[^>]*data-control="password-reveal"/s', $html);
    }

    public function test_revealable_false_leaves_the_show_button_out(): void
    {
        $html = $this->pageHtml();

        $this->assertMatchesRegularExpression('/<input\s+type="password"\s+name="_access_code"\s+id="([^"]+)"[^>]*class="form-control"/', $html);
        preg_match('/name="_access_code"\s+id="([^"]+)"/', $html, $match);
        $this->assertStringNotContainsString('aria-controls="'.$match[1].'"', $html);

        $html = $this->renderField($this->makeSample(), '_password', ['label' => 'Password', 'type' => 'password', 'revealable' => false]);

        $this->assertStringNotContainsString('password-reveal', $html);
        $this->assertStringNotContainsString('form-adorn', $html);
    }

    public function test_revealable_only_applies_to_password_fields(): void
    {
        $html = $this->renderField($this->makeSample(), 'code', ['label' => 'Code', 'revealable' => true]);

        $this->assertStringNotContainsString('password-reveal', $html);
        $this->assertStringNotContainsString('form-adorn', $html);
    }

    public function test_a_disabled_or_read_only_revealable_password_disables_its_button(): void
    {
        $sample = $this->makeSample();

        foreach (['disabled', 'readOnly'] as $option) {
            $html = $this->renderField($sample, '_password', ['label' => 'Password', 'type' => 'password', $option => true]);

            $this->assertMatchesRegularExpression('/data-control="password-reveal"[^>]*\sdisabled\s*>/', $html, $option);
        }
    }

    public function test_a_revealable_password_shows_no_button_in_preview_mode(): void
    {
        $form = Form::createIn(new FormsController, [
            'model' => $this->makeSample(),
            'previewMode' => true,
            'fields' => ['_password' => ['label' => 'Password', 'type' => 'password']],
        ]);
        $form->bindToController();
        $html = (string) $form->render();

        $this->assertStringNotContainsString('password-reveal', $html);
        $this->assertStringContainsString('********', $html);
    }

    public function test_a_checkbox_field_renders_a_check_beside_its_label_and_comment(): void
    {
        $html = $this->pageHtml(['has_discount' => true]);

        $this->assertMatchesRegularExpression('/id="Form-exampleCheckbox-field-has_discount-group"><!-- Checkbox -->\s*<div class="form-field"\s*>\s*<input\s+type="hidden"\s+name="has_discount"\s+value="0"/', $html);
        $this->assertMatchesRegularExpression('/<input\s+class="check"\s+type="checkbox"\s+id="Form-exampleCheckbox-field-has_discount"\s+name="has_discount"\s+value="1"\s+checked/', $html);
        $this->assertMatchesRegularExpression('/<div class="form-field-content">\s*<label for="Form-exampleCheckbox-field-has_discount">\s*Offer a discount/', $html);
        $this->assertStringContainsString('<p class="form-text">The label and comment sit beside the box.</p>', $html);
        $this->assertStringNotContainsString('form-check', $html);
    }

    public function test_a_switch_field_wraps_its_input_in_a_switch_track(): void
    {
        $html = $this->pageHtml(['is_featured' => true]);

        $this->assertMatchesRegularExpression('/<div class="switch">\s*<input\s+type="checkbox"\s+role="switch"\s+switch\s+id="Form-exampleSwitch-field-is_featured"[^>]*checked/', $html);
        $this->assertStringNotContainsString('form-switch', $html);
    }

    public function test_radio_and_checkbox_lists_render_one_form_field_per_option(): void
    {
        $html = $this->pageHtml(['size' => 'm', 'plan' => 'team', 'features' => ['wifi']]);

        // Inline radio options, and options with descriptions and a disabled option
        $this->assertSame(3, preg_match_all('/<div class="form-field is-inline">\s*<input\s+class="radio"\s+id="checkbox_Form-exampleRadio-field-size_/', $html));
        $this->assertMatchesRegularExpression('/id="checkbox_Form-exampleRadio-field-size_m"\s+name="size"\s+value="m"\s+type="radio"\s+checked/', $html);
        $this->assertMatchesRegularExpression('/<label for="checkbox_Form-exampleRadio-field-plan_team">\s*Team\s*<\/label>\s*<p class="form-text">For up to ten people<\/p>/', $html);
        $this->assertMatchesRegularExpression('/id="checkbox_Form-exampleRadio-field-plan_enterprise"\s+name="plan"\s+value="enterprise"\s+type="radio"\s+disabled/', $html);

        // Inline checkboxes with quick selection
        $this->assertSame(4, preg_match_all('/<div class="form-field is-inline">\s*<input\s+class="check"\s+type="checkbox"\s+id="checkbox_Form-exampleCheckboxlist-field-features_/', $html));
        $this->assertMatchesRegularExpression('/name="features\[\]"\s+value="wifi"\s+checked="checked"/', $html);
        $this->assertSame(2, substr_count($html, 'data-field-checkboxlist-all'));
    }

    public function test_nested_and_long_checkbox_lists(): void
    {
        $html = $this->pageHtml();

        $this->assertStringContainsString('<div class="field-checkboxlist is-cumulative  " >', $html);
        $this->assertMatchesRegularExpression('/<ul class="checkboxlist-group">\s*<li class="checkboxlist-group-item">\s*<div class="form-field ">\s*<input\s+class="check"\s+type="checkbox"\s+id="checkbox_Form-exampleCheckboxlistNested-field-permissions_content_create"/', $html);
        $this->assertStringContainsString('<p class="form-text">Ticks every content permission below it</p>', $html);

        // More than ten options scroll and turn on the quick selection buttons
        $this->assertStringContainsString('<div class="field-checkboxlist  is-scrollable " >', $html);
        $this->assertSame(14, substr_count($html, 'id="checkbox_Form-exampleCheckboxlistScrollable-field-interests_'));
    }

    public function test_dropdowns_take_options_from_yaml_the_model_a_static_method_and_presets(): void
    {
        $html = $this->pageHtml(['priority' => 'high', 'delivery' => 'courier', 'country' => 'au', 'category' => 'lamps']);

        // Inline options, with an empty option or a placeholder without search
        $this->assertMatchesRegularExpression('/<option\s+value=""\s*>Not set<\/option>/', $html);
        $this->assertMatchesRegularExpression('/name="delivery"\s+class="form-control custom-select select-no-search"\s+data-placeholder="Choose a method"/', $html);

        // get{Field}Options by convention, a named method with option groups, and a static method
        $this->assertMatchesRegularExpression('/<option\s+selected="selected"\s+value="au"\s*>Australia<\/option>/', $html);
        $this->assertMatchesRegularExpression('/<optgroup label="Lighting">\s*<option\s+value="ceiling"\s*>Ceiling Lights<\/option>\s*<option\s+selected="selected"\s+value="lamps"/', $html);
        $this->assertMatchesRegularExpression('/<option\s+selected="selected"\s+value="aud"\s*>Australian Dollar<\/option>/', $html);
        $this->assertStringContainsString('US Dollar', $html);

        // The Phosphor icon preset
        $this->assertMatchesRegularExpression('/<option\s+selected="selected"\s+data-icon="ph ph-lamp"\s+value="ph ph-lamp"/', $html);
    }

    public function test_options_carry_colors_and_icons_from_yaml(): void
    {
        $html = $this->pageHtml();

        $this->assertMatchesRegularExpression('/<option\s+selected="selected"\s+data-status="#f39c12"\s+value="low"\s*>Low stock<\/option>/', $html);
        $this->assertMatchesRegularExpression('/<option\s+data-status="#e74c3c"\s+value="out"/', $html);
        $this->assertMatchesRegularExpression('/<option\s+selected="selected"\s+data-icon="ph ph-truck"\s+value="road"\s*>Road<\/option>/', $html);
        $this->assertMatchesRegularExpression('/<option\s+data-icon="ph ph-airplane-tilt"\s+value="air"/', $html);
    }

    public function test_a_dropdown_disables_a_disabled_option_unless_it_is_the_saved_value(): void
    {
        $html = $this->pageHtml(['level' => 'basic']);

        $this->assertMatchesRegularExpression('/<option\s+disabled\s+data-icon="ph ph-archive"\s+value="legacy"/', $html);
        $this->assertMatchesRegularExpression('/<option\s+selected="selected"\s+data-icon="ph ph-cube"\s+value="basic"/', $html);
        $this->assertStringContainsString('data-status="#3498db"', $html);
        $this->assertMatchesRegularExpression('/value="pro-yearly"\s*>(&nbsp;){3}Professional, billed yearly<\/option>/', $html);

        Sample::query()->update(['level' => 'legacy']);
        $html = $this->get(route('reference.forms.fields'))->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/<option\s+selected="selected"\s+data-icon="ph ph-archive"\s+value="legacy"/', $html);
    }

    public function test_a_selector_renders_radio_toggle_buttons_after_an_empty_value_input(): void
    {
        $html = $this->pageHtml(['status' => 'active']);

        $this->assertSame(3, substr_count($html, 'data-control="selector"'));
        $this->assertSame(1, substr_count($html, 'id="Form-exampleSelector-field-status"'));
        $this->assertMatchesRegularExpression('/id="Form-exampleSelector-field-status"\s+data-control="selector"\s*>\s*<input\s+type="hidden"\s+name="status"\s+value=""\s*\/>\s*<div\s+class="btn-group"\s+role="radiogroup"\s+aria-label="Status"\s*>/', $html);
        $this->assertMatchesRegularExpression('/<label class="btn-check btn-outline theme-primary">\s*<input\s+type="radio"\s+name="status"\s+value="active"\s+autocomplete="off"\s+checked\s*\/>\s*Active\s*<\/label>/', $html);
        $this->assertMatchesRegularExpression('/<input\s+type="radio"\s+name="status"\s+value="draft"\s+autocomplete="off"\s*\/>\s*Draft\s*<\/label>/', $html);
    }

    public function test_a_selector_can_allow_an_empty_value(): void
    {
        $html = $this->pageHtml(['visibility' => 'public']);

        $this->assertMatchesRegularExpression('/id="Form-exampleSelectorAllowEmpty-field-visibility"\s+data-control="selector"\s+data-selector-allow-empty/', $html);
    }

    public function test_a_read_only_selector_is_locked_but_keeps_its_value(): void
    {
        $html = $this->pageHtml(['audience' => 'retail']);

        preg_match('/id="Form-exampleSelectorReadOnly-field-audience".*?<\/div>\s*<\/div>/s', $html, $match);
        $readOnly = $match[0];

        $this->assertMatchesRegularExpression('/role="radiogroup"\s+aria-label="Audience"\s+aria-readonly="true"/', $readOnly);
        $this->assertMatchesRegularExpression('/name="audience"\s+value="retail"\s+autocomplete="off"\s+checked/', $readOnly);
        $this->assertStringNotContainsString('disabled', $readOnly);
    }

    public function test_a_disabled_selector_posts_nothing(): void
    {
        $html = $this->renderField($this->makeSample(['status' => 'active']), 'status', [
            'label' => 'Status',
            'type' => 'selector',
            'disabled' => true,
            'options' => ['draft' => 'Draft', 'active' => 'Active'],
        ]);

        $this->assertMatchesRegularExpression('/<input\s+type="hidden"\s+name="status"\s+value=""\s+disabled/', $html);
        $this->assertSame(2, preg_match_all('/<input\s+type="radio"\s+name="status"[^>]*\sdisabled/s', $html));
        $this->assertStringNotContainsString('aria-readonly', $html);
    }

    public function test_section_hint_and_ruler_render_their_labels_and_comments(): void
    {
        $html = $this->pageHtml();

        $this->assertMatchesRegularExpression('/<div class="field-section">\s*<h4>Shipping details<\/h4>\s*<p class="form-text">Where and how the sample is sent.<\/p>/', $html);
        $this->assertMatchesRegularExpression('/<div class="field-section">\s*<label>The fields below work out the delivery cost.<\/label>/', $html);

        foreach (['info', 'success', 'warning', 'danger'] as $mode) {
            $this->assertMatchesRegularExpression('/id="Form-exampleHint-field-_'.$mode.'_hint-group"><div class="callout fade show callout-'.$mode.'(?: no-subheader)?">/', $html);
        }

        $this->assertStringContainsString('<p>With commentHtml, the comment can hold <strong>HTML markup</strong>.</p>', $html);
        $this->assertStringContainsString('the comment is read as <strong>Markdown</strong>.', $html);

        $this->assertSame(2, substr_count($html, '<div class="field-horizontalrule">'));
        $this->assertMatchesRegularExpression('/class="form-label">\s*Packaging\s*<\/label>\s*<div class="field-horizontalrule">/', $html);
    }

    public function test_a_hint_with_a_path_renders_the_partial_inside_its_callout(): void
    {
        $html = $this->pageHtml(['name' => 'Aurora Desk Lamp', 'slug' => 'aurora-desk-lamp']);

        $this->assertMatchesRegularExpression('/id="Form-exampleHintPartial-field-_record_hint-group"><div class="callout fade show callout-warning no-subheader">\s*<div class="header">.*?<h3>\s*Before you publish\s*<\/h3>\s*<\/div>\s*<div class="content">\s*<ul class="mb-0">\s*<li>\s*Aurora Desk Lamp needs a price/s', $html);
        $this->assertStringContainsString('The slug <code>aurora-desk-lamp</code>', $html);
    }

    public function test_a_read_only_dropdown_shows_a_saved_value_inside_an_option_group_or_among_children(): void
    {
        $sample = $this->makeSample(['category' => 'lamps', 'level' => 'pro-yearly']);

        $grouped = $this->renderField($sample, 'category', ['label' => 'Category', 'type' => 'dropdown', 'readOnly' => true]);
        $this->assertMatchesRegularExpression('/<div class="form-control"\s+readonly>\s*Lamps\s*<\/div>/', $grouped);

        $nested = $this->renderField($sample, 'level', ['label' => 'Level', 'type' => 'dropdown', 'readOnly' => true]);
        $this->assertMatchesRegularExpression('/<div class="form-control"\s+readonly>(?:(?!<\/div>).)*Professional, billed yearly\s*<\/div>/s', $nested);
    }

    public function test_a_read_only_radio_list_cancels_clicks_on_every_option(): void
    {
        $sample = $this->makeSample(['size' => 'm']);

        $html = $this->renderField($sample, 'size', [
            'label' => 'Size',
            'type' => 'radio',
            'readOnly' => true,
            'options' => ['s' => 'Small', 'm' => 'Medium', 'l' => 'Large'],
        ]);

        $this->assertSame(3, substr_count($html, 'type="radio"'));
        $this->assertSame(3, substr_count($html, 'onclick="return false;"'));
        $this->assertMatchesRegularExpression('/value="m"\s+type="radio"\s+checked/', $html);
    }

    public function test_partial_fields_render_app_partials_with_the_form_model(): void
    {
        $html = $this->pageHtml(['name' => 'Aurora Desk Lamp', 'timezone' => 'Australia/Sydney']);

        $this->assertMatchesRegularExpression('/<strong>\s*Aurora Desk Lamp\s*<\/strong>\s*<span class="badge theme-secondary">\s*Record \d+/', $html);
        $this->assertMatchesRegularExpression('/name="timezone"\s+class="form-control custom-select"\s+data-handler="onSearchTimezones"/', $html);
        $this->assertMatchesRegularExpression('/<option value="Australia\/Sydney" selected>\s*Australia\/Sydney\s*<\/option>/', $html);
    }

    public function test_the_timezone_handler_searches_the_timezones(): void
    {
        $this->makeSample();

        $resp = $this->ajax('onSearchTimezones', ['term' => 'sydney']);

        $resp->assertOk();
        $this->assertSame([['id' => 'Australia/Sydney', 'text' => 'Australia/Sydney']], $resp->json('results'));
    }

    public function test_checkbox_and_switch_filter_scopes_render_as_a_toggle_and_a_menu_chip(): void
    {
        $filter = Filter::createIn(new FormsController, [
            'model' => new User,
            'scopes' => [
                'verified' => [
                    'label' => 'Verified',
                    'type' => 'checkbox',
                    'conditions' => 'email_verified_at is not null',
                ],
                'blocked' => [
                    'label' => 'Blocked',
                    'type' => 'switch',
                    'conditions' => ['is_mail_blocked <> true', 'is_mail_blocked = true'],
                ],
            ],
        ]);

        $filter->bindToController();
        $html = (string) $filter->render();

        $this->assertMatchesRegularExpression('/aria-pressed="false"\s+class="filter-scope scope-checkbox\s*"\s+data-scope-name="verified"/', $html);
        $this->assertMatchesRegularExpression('/class="filter-scope filter-chip scope-switch\s*"\s+data-scope-name="blocked">\s*<button[^>]*class="combobox-toggle"/', $html);
        preg_match_all('/data-bs-value="(\d)"/', $html, $states);
        $this->assertSame(['0', '2', '1'], $states[1]);
        $this->assertStringNotContainsString('form-check', $html);
        $this->assertStringNotContainsString('type="radio"', $html);
    }
}
