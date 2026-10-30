<?php

namespace Tests\Feature\Reference\Forms;

use App\Controllers\Reference\FormsController;
use App\Models\Sample;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Larajax\Ui\Widgets\Filter;
use Tests\TestCase;

class FieldsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var array<int, string> examples on the page, one YAML file each.
     */
    protected array $examples = [
        'text', 'email', 'number', 'password', 'textarea', 'textarea-sizes',
        'dropdown', 'dropdown-model-options', 'dropdown-preset', 'option-styles',
        'radio', 'checkbox', 'switch', 'checkboxlist', 'checkboxlist-nested', 'checkboxlist-scrollable',
        'balloon-selector', 'balloon-allow-empty', 'balloon-read-only',
        'section', 'hint', 'ruler', 'partial', 'partial-ajax',
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

        // The read-only balloon selector still posts and saves its value
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
        $this->assertMatchesRegularExpression('/type="number"\s+step="0.01"\s+name="price"\s+id="[^"]+"\s+value="89.50?"/', $html);
        $this->assertMatchesRegularExpression('/type="number"\s+step="1"\s+name="quantity"[^>]*min="0"\s+max="999"/s', $html);
        $this->assertMatchesRegularExpression('/type="password"\s+name="_password"[^>]*value=""/', $html);

        foreach (['tiny' => 'excerpt', 'small' => 'summary', 'large' => 'description', 'huge' => 'body', 'giant' => 'notes'] as $size => $name) {
            $this->assertMatchesRegularExpression('/<textarea\s+name="'.$name.'"[^>]*class="form-control field-textarea size-'.$size.'"/', $html);
        }
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

    public function test_a_balloon_selector_renders_focusable_radio_options_and_one_value_input(): void
    {
        $html = $this->pageHtml(['status' => 'active']);

        $this->assertSame(3, substr_count($html, 'data-control="balloon-selector"'));
        $this->assertStringContainsString('role="radiogroup"', $html);
        $this->assertMatchesRegularExpression('/data-value="active"\s+role="radio"\s+aria-checked="true"\s+tabindex="0"\s+class="active"/', $html);
        $this->assertMatchesRegularExpression('/data-value="draft"\s+role="radio"\s+aria-checked="false"\s+tabindex="0"/', $html);
        $this->assertMatchesRegularExpression('/<input\s+type="hidden"\s+name="status"\s+value="active"/', $html);
        $this->assertSame(1, substr_count($html, 'id="Form-exampleBalloonSelector-field-status"'));
    }

    public function test_a_balloon_selector_can_allow_an_empty_value(): void
    {
        $html = $this->pageHtml(['visibility' => 'public']);

        $this->assertMatchesRegularExpression('/id="Form-exampleBalloonAllowEmpty-field-visibility"\s+class="control-balloon-selector "\s+data-control="balloon-selector"\s+data-selector-allow-empty/', $html);
    }

    public function test_a_read_only_balloon_selector_is_locked_but_keeps_its_value(): void
    {
        $html = $this->pageHtml(['audience' => 'retail']);

        preg_match('/id="Form-exampleBalloonReadOnly-field-audience".*?<\/div>/s', $html, $match);
        $readOnly = $match[0];

        $this->assertStringContainsString('class="control-balloon-selector control-disabled"', $readOnly);
        $this->assertSame(3, substr_count($readOnly, 'aria-disabled="true"'));
        $this->assertStringNotContainsString('tabindex="0"', $readOnly);
        $this->assertMatchesRegularExpression('/name="audience"\s+value="retail"/', $readOnly);
    }

    public function test_section_hint_and_ruler_render_their_labels_and_comments(): void
    {
        $html = $this->pageHtml();

        $this->assertMatchesRegularExpression('/<div class="field-section">\s*<h4>Shipping details<\/h4>\s*<p class="form-text">Where and how the sample is sent.<\/p>/', $html);
        $this->assertMatchesRegularExpression('/<div class="field-section">\s*<label>The fields below work out the delivery cost.<\/label>/', $html);

        foreach (['info', 'success', 'warning', 'danger'] as $mode) {
            $this->assertMatchesRegularExpression('/id="Form-exampleHint-field-_'.$mode.'_hint-group"><div class="callout fade show callout-'.$mode.' /', $html);
        }

        $this->assertStringContainsString('<p>With commentHtml, the comment can hold <strong>HTML markup</strong>.</p>', $html);
        $this->assertStringContainsString('the comment is read as <strong>Markdown</strong>.', $html);

        $this->assertSame(2, substr_count($html, '<div class="field-horizontalrule">'));
        $this->assertMatchesRegularExpression('/class="form-label">\s*Packaging\s*<\/label>\s*<div class="field-horizontalrule">/', $html);
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

    public function test_checkbox_and_switch_filter_scopes_render_small_checks_in_form_fields(): void
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

        $this->assertMatchesRegularExpression('/class="filter-scope scope-checkbox form-field"\s+data-scope-name="verified">\s*<input class="check check-sm" type="checkbox"/', $html);
        $this->assertMatchesRegularExpression('/class="filter-scope form-field is-indeterminate"\s+data-scope-name="blocked">\s*<input class="check check-sm" type="checkbox"[^>]*data-checked="0"/', $html);
        $this->assertStringNotContainsString('form-check', $html);
    }
}
