<?php

namespace Tests\Feature\Reference\FormWidgets;

use App\Models\Sample;
use App\Models\UserGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class NestedFormTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ajax posts one AJAX handler request to the nested form page.
     */
    protected function ajax(string $handler, array $data = []): TestResponse
    {
        return $this->withHeaders([
            'X-AJAX-HANDLER' => $handler,
            'X-Requested-With' => 'XMLHttpRequest',
        ])->post(route('reference.form-widgets.nested-form'), $data);
    }

    /**
     * section returns the page markup of one example section.
     */
    protected function section(string $html, string $id): string
    {
        preg_match('/<section class="ui-section" id="'.preg_quote($id, '/').'">(.*?)<\/section>/s', $html, $match);

        return $match[1] ?? '';
    }

    public function test_the_page_renders_every_example_beside_its_yaml(): void
    {
        Sample::factory()->create();

        $html = $this->get(route('reference.form-widgets.nested-form'))->assertOk()->getContent();

        foreach (['default', 'tabs', 'no-panel', 'relation', 'preview'] as $example) {
            $this->assertStringContainsString("resources/ui/reference/form-widgets/nested-form/{$example}.yaml", $html);
        }

        $this->assertSame(5, substr_count($html, 'class="nested-form'));
        $this->assertStringContainsString('data-request="onSaveExamples"', $html);
    }

    public function test_a_json_attribute_renders_its_values_in_a_panel(): void
    {
        Sample::factory()->create(['address' => ['recipient' => 'Lumen Works', 'line1' => '12 Harbour Street']]);

        $html = $this->section($this->get(route('reference.form-widgets.nested-form'))->getContent(), 'default');

        $this->assertStringContainsString('class="nested-form is-paneled"', $html);
        $this->assertMatchesRegularExpression('/name="address\[recipient\]"[^>]*value="Lumen Works"/', $html);
        $this->assertMatchesRegularExpression('/name="address\[line1\]"[^>]*value="12 Harbour Street"/', $html);
    }

    public function test_the_inner_form_brings_its_own_tabs(): void
    {
        Sample::factory()->create(['shipping' => ['line1' => '3 Bell Lane', 'instructions' => 'Ring twice.']]);

        $html = $this->section($this->get(route('reference.form-widgets.nested-form'))->getContent(), 'tabs');

        $this->assertMatchesRegularExpression('/<span class="title">\s*<span>\s*Street\s*<\/span>/', $html);
        $this->assertMatchesRegularExpression('/<span class="title">\s*<span>\s*Delivery\s*<\/span>/', $html);
        $this->assertStringContainsString('is-nested', $html);
        $this->assertMatchesRegularExpression('/name="shipping\[line1\]"[^>]*value="3 Bell Lane"/', $html);
        $this->assertStringContainsString('Ring twice.', $html);
    }

    public function test_show_panel_false_drops_the_panel(): void
    {
        Sample::factory()->create(['contact' => ['person' => 'Mia Lumen']]);

        $html = $this->section($this->get(route('reference.form-widgets.nested-form'))->getContent(), 'no-panel');

        $this->assertStringContainsString('class="nested-form "', $html);
        $this->assertMatchesRegularExpression('/name="contact\[person\]"[^>]*value="Mia Lumen"/', $html);
    }

    public function test_a_relation_renders_the_related_record(): void
    {
        $group = UserGroup::factory()->create(['name' => 'Editors', 'code' => 'editors']);
        Sample::factory()->create(['primary_group_id' => $group->id]);

        $html = $this->section($this->get(route('reference.form-widgets.nested-form'))->getContent(), 'relation');

        $this->assertMatchesRegularExpression('/name="primary_group\[name\]"[^>]*value="Editors"/', $html);
        $this->assertMatchesRegularExpression('/name="primary_group\[code\]"[^>]*value="editors"/', $html);
    }

    public function test_a_disabled_nested_form_renders_in_preview_mode(): void
    {
        Sample::factory()->create(['address' => ['recipient' => 'Lumen Works']]);

        $html = $this->section($this->get(route('reference.form-widgets.nested-form'))->getContent(), 'preview');

        $this->assertStringContainsString('Lumen Works', $html);
        $this->assertStringNotContainsString('name="address_preview[recipient]"', $html);
    }

    public function test_saving_the_page_stores_the_json_objects_and_the_related_record(): void
    {
        $group = UserGroup::factory()->create(['name' => 'Editors', 'code' => 'editors']);
        $sample = Sample::factory()->create(['primary_group_id' => $group->id]);

        $this->ajax('onSaveExamples', [
            'address' => ['recipient' => 'Ember Works', 'line1' => '3 Bell Lane', 'suburb' => 'Hobart', 'region_code' => '7000'],
            'shipping' => ['recipient' => 'Ember Works', 'line1' => '3 Bell Lane', 'instructions' => 'Ring twice.', 'leave_at_door' => '1'],
            'contact' => ['person' => 'Ada', 'phone' => '555'],
            'primary_group' => ['name' => 'Writers', 'code' => 'writers'],
            'address_preview' => ['recipient' => 'Dropped'],
        ])->assertOk();

        $sample->refresh();
        $this->assertSame(['recipient' => 'Ember Works', 'line1' => '3 Bell Lane', 'suburb' => 'Hobart', 'region_code' => '7000'], $sample->address);
        $this->assertSame('Ring twice.', $sample->shipping['instructions']);
        $this->assertSame('1', $sample->shipping['leave_at_door']);
        $this->assertSame(['person' => 'Ada', 'phone' => '555'], $sample->contact);

        // The related record saves itself, so the sample keeps the same group
        $this->assertSame($group->id, $sample->primary_group_id);
        $this->assertSame('Writers', $group->refresh()->name);
        $this->assertSame('writers', $group->code);
    }
}
