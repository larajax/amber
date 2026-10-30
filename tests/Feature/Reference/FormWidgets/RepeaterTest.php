<?php

namespace Tests\Feature\Reference\FormWidgets;

use App\Models\Sample;
use App\Models\SampleLink;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class RepeaterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ajax posts one AJAX handler request to the repeater page.
     */
    protected function ajax(string $handler, array $data = []): TestResponse
    {
        return $this->withHeaders([
            'X-AJAX-HANDLER' => $handler,
            'X-Requested-With' => 'XMLHttpRequest',
        ])->post(route('reference.form-widgets.repeater'), $data);
    }

    /**
     * section returns the page markup of one example section.
     */
    protected function section(string $html, string $id): string
    {
        preg_match('/<section class="ui-section" id="'.preg_quote($id, '/').'">(.*?)<\/section>/s', $html, $match);

        return $match[1] ?? '';
    }

    /**
     * page returns the repeater page markup.
     */
    protected function page(): string
    {
        return $this->get(route('reference.form-widgets.repeater'))->assertOk()->getContent();
    }

    public function test_the_page_renders_every_example_beside_its_yaml(): void
    {
        Sample::factory()->create();

        $html = $this->page();

        foreach (['default', 'limits', 'collapsed', 'fixed-order', 'groups', 'builder', 'tabs', 'nested', 'parent-fields', 'relation', 'preview'] as $example) {
            $this->assertStringContainsString("resources/ui/reference/form-widgets/repeater/{$example}.yaml", $html);
        }

        $this->assertSame(10, substr_count($html, 'data-control="repeateraccordion"'));
        $this->assertSame(1, substr_count($html, 'data-control="repeaterbuilder"'));
        $this->assertStringContainsString('data-request="onSaveExamples"', $html);
    }

    public function test_the_page_renders_the_saved_items(): void
    {
        Sample::factory()->create(['faqs' => [
            ['question' => 'Which bulbs?', 'answer' => 'Any E27 bulb.'],
            ['question' => 'Can it clamp?', 'answer' => 'Yes.'],
        ]]);

        $html = $this->section($this->page(), 'default');

        $this->assertStringContainsString('data-title-from="question"', $html);
        $this->assertMatchesRegularExpression('/name="faqs\[0\]\[question\]"[^>]*value="Which bulbs\?"/', $html);
        $this->assertMatchesRegularExpression('/name="faqs\[1\]\[question\]"[^>]*value="Can it clamp\?"/', $html);
        $this->assertStringContainsString('exampleDefaultFaqs::onAddItem', $html);
        $this->assertStringContainsString('Add question', $html);
        $this->assertStringContainsString('repeater-item-handle', $html);
        $this->assertStringContainsString('data-repeater-duplicate', $html);
    }

    public function test_adding_an_item_returns_it_with_the_next_index(): void
    {
        Sample::factory()->create(['faqs' => [['question' => 'Which bulbs?']]]);

        $response = $this->ajax('exampleDefaultFaqs::onAddItem', [
            'exampleDefaultFaqs_loaded' => 1,
            'faqs' => [['question' => 'Which bulbs?', '_index' => 0]],
        ]);

        $response->assertOk();
        $content = $response->getContent();
        $this->assertStringContainsString('faqs[1][question]', $content);
        $this->assertStringContainsString('field-repeater-item', $content);
    }

    public function test_item_limits_pad_the_field_and_render_on_the_control(): void
    {
        Sample::factory()->create(['highlights' => null]);

        $html = $this->section($this->page(), 'limits');

        $this->assertStringContainsString('data-min-items="2"', $html);
        $this->assertStringContainsString('data-max-items="4"', $html);
        $this->assertStringContainsString('name="highlights[0][text]"', $html);
        $this->assertStringContainsString('name="highlights[1][text]"', $html);
        $this->assertStringNotContainsString('name="highlights[2][text]"', $html);
    }

    public function test_collapsed_items_start_closed(): void
    {
        Sample::factory()->create(['milestones' => [['title' => 'Prototype'], ['title' => 'Launch']]]);

        $html = $this->section($this->page(), 'collapsed');

        $this->assertStringContainsString('data-items-expanded=""', $html);
        $this->assertSame(2, substr_count($html, 'class="field-repeater-item collapsed"'));
        $this->assertSame(2, substr_count($html, 'data-lazy-controls'));
    }

    public function test_fixed_order_leaves_out_the_handle_and_the_duplicate_option(): void
    {
        Sample::factory()->create(['dimensions' => [['label' => 'Height', 'value' => '48 cm']]]);

        $html = $this->section($this->page(), 'fixed-order');

        $this->assertStringContainsString('data-use-reorder=""', $html);
        $this->assertStringContainsString('data-use-duplicate=""', $html);
        $this->assertStringNotContainsString('repeater-item-handle', $html);
        $this->assertStringNotContainsString('data-repeater-duplicate', $html);
        $this->assertStringNotContainsString('data-repeater-move-up', $html);
    }

    public function test_groups_render_a_palette_and_store_the_group_key(): void
    {
        Sample::factory()->create(['sections' => [
            ['type' => 'text', 'title' => 'Designed for reading'],
            ['type' => 'image', 'caption' => 'At full stretch'],
        ]]);

        $html = $this->section($this->page(), 'groups');

        $this->assertStringContainsString('class="field-repeater repeater-mode-accordion is-grouped"', $html);
        $this->assertStringContainsString('data-repeater-cmd="add-group"', $html);
        $this->assertStringContainsString('data-repeater-add-group="image"', $html);
        $this->assertStringContainsString('A caption and how wide the image runs', $html);
        $this->assertStringContainsString('data-item-title="Image"', $html);
        $this->assertStringContainsString('data-title-from="caption"', $html);
        $this->assertMatchesRegularExpression('/name="sections\[1\]\[type\]" value="image"/', $html);
        $this->assertMatchesRegularExpression('/name="sections\[1\]\[caption\]"[^>]*value="At full stretch"/', $html);
    }

    public function test_the_builder_renders_its_groups_with_tabs_and_a_nested_repeater(): void
    {
        Sample::factory()->create(['content' => [
            ['_group' => 'heading', 'title' => 'Light that follows you'],
            ['_group' => 'paragraph', 'text' => 'Holds its place.', 'align' => 'left'],
            ['_group' => 'checklist', 'items' => [['label' => 'Unbox the lamp', 'done' => '1']]],
        ]]);

        $html = $this->section($this->page(), 'builder');

        $this->assertStringContainsString('data-control="repeaterbuilder"', $html);
        $this->assertStringContainsString('field-repeater-builder', $html);
        $this->assertMatchesRegularExpression('/name="content\[1\]\[text\]"/', $html);
        $this->assertMatchesRegularExpression('/<span class="title">\s*<span>\s*Options\s*<\/span>/', $html);
        $this->assertMatchesRegularExpression('/name="content\[2\]\[items\]\[0\]\[label\]"[^>]*value="Unbox the lamp"/', $html);
        $this->assertMatchesRegularExpression('/name="content\[0\]\[_group\]" value="heading"/', $html);
    }

    public function test_use_tabs_moves_item_fields_into_tabs_named_by_their_tab_property(): void
    {
        Sample::factory()->create(['specs' => [['property' => 'Power', 'value' => '9', 'unit' => 'W', 'note' => 'At full brightness']]]);

        $html = $this->section($this->page(), 'tabs');

        $this->assertStringContainsString('secondary-tabs', $html);
        $this->assertMatchesRegularExpression('/<span class="title">\s*<span>\s*Value\s*<\/span>/', $html);
        $this->assertMatchesRegularExpression('/<span class="title">\s*<span>\s*Notes\s*<\/span>/', $html);
        $this->assertMatchesRegularExpression('/name="specs\[0\]\[property\]"[^>]*value="Power"/', $html);
        $this->assertMatchesRegularExpression('/name="specs\[0\]\[unit\]"[^>]*value="W"/', $html);
    }

    public function test_a_nested_repeater_renders_inside_each_item(): void
    {
        Sample::factory()->create(['itinerary' => [
            ['day' => 'Monday', 'stops' => [['place' => 'Factory floor'], ['place' => 'Quality lab']]],
        ]]);

        $html = $this->section($this->page(), 'nested');

        $this->assertSame(2, substr_count($html, 'data-control="repeateraccordion"'));
        $this->assertMatchesRegularExpression('/name="itinerary\[0\]\[stops\]\[1\]\[place\]"[^>]*value="Quality lab"/', $html);
    }

    public function test_item_fields_can_watch_the_parent_form(): void
    {
        Sample::factory()->create(['step_style' => 'detailed', 'steps' => [['title' => 'Assemble the base', 'minutes' => 5]]]);

        $html = $this->section($this->page(), 'parent-fields');

        $this->assertStringContainsString('data-trigger="[name=&quot;step_style&quot;]"', $html);
        $this->assertStringContainsString('data-trigger-condition="value[detailed]"', $html);
        $this->assertMatchesRegularExpression('/name="steps\[0\]\[title\]"[^>]*value="Assemble the base"/', $html);
    }

    public function test_a_relation_renders_each_related_record_as_an_item(): void
    {
        $sample = Sample::factory()->create();
        $link = SampleLink::factory()->for($sample)->create(['title' => 'Assembly guide']);

        $html = $this->section($this->page(), 'relation');

        $this->assertMatchesRegularExpression('/name="links\['.$link->id.'\]\[title\]"[^>]*value="Assembly guide"/', $html);
        $this->assertStringContainsString('data-repeater-index="'.$link->id.'"', $html);
    }

    public function test_adding_a_related_item_creates_a_record_held_against_the_session_key(): void
    {
        $sample = Sample::factory()->create();

        $response = $this->ajax('exampleRelationLinks::onAddItem', ['_session_key' => 'session-1']);

        $response->assertOk();
        $link = SampleLink::sole();
        $this->assertNull($link->sample_id);
        $this->assertSame([$link->id], $sample->links()->withDeferred('session-1')->pluck('id')->all());
        $this->assertStringContainsString("links[{$link->id}][title]", $response->getContent());
    }

    public function test_saving_commits_the_related_items_with_their_order(): void
    {
        $sample = Sample::factory()->create();
        $first = SampleLink::factory()->for($sample)->create(['title' => 'First']);
        $second = SampleLink::factory()->create(['sample_id' => null, 'title' => 'Second']);
        $sample->links()->add($second, 'session-1');

        $this->ajax('onSaveExamples', [
            '_session_key' => 'session-1',
            'exampleRelationLinks_loaded' => 1,
            'links' => [
                $second->id => ['title' => 'Warranty', 'url' => 'https://example.com/warranty'],
                $first->id => ['title' => 'Assembly guide', 'url' => 'https://example.com/assembly'],
            ],
        ])->assertOk();

        $this->assertSame(['Warranty', 'Assembly guide'], $sample->fresh()->links->pluck('title')->all());
    }

    public function test_saving_the_page_stores_the_items_as_json(): void
    {
        $sample = Sample::factory()->create();

        $this->ajax('onSaveExamples', [
            'exampleDefaultFaqs_loaded' => 1,
            'faqs' => [
                ['question' => 'Docs?', 'answer' => 'Online.', '_index' => 0],
                ['question' => 'About?', 'answer' => 'Below.', '_index' => 1],
            ],
            'exampleGroupsSections_loaded' => 1,
            'sections' => [
                ['type' => 'image', 'caption' => 'Wide shot', 'alignment' => 'wide', '_index' => 0],
            ],
            'exampleNestedItinerary_loaded' => 1,
            'itinerary' => [
                ['day' => 'Monday', 'stops' => [['place' => 'Warehouse', '_index' => 0]], '_index' => 0],
            ],
            'exampleParentFieldsSteps_loaded' => 1,
            'step_style' => 'detailed',
            'steps' => [['title' => 'Plug it in', 'minutes' => '1', 'details' => 'Use the cable.', '_index' => 0]],
        ])->assertOk();

        $sample->refresh();
        $this->assertSame([
            ['question' => 'Docs?', 'answer' => 'Online.'],
            ['question' => 'About?', 'answer' => 'Below.'],
        ], $sample->faqs);
        $this->assertSame([['caption' => 'Wide shot', 'alignment' => 'wide', 'type' => 'image']], $sample->sections);
        $this->assertSame([['day' => 'Monday', 'stops' => [['place' => 'Warehouse']]]], $sample->itinerary);
        $this->assertSame('detailed', $sample->step_style);
        $this->assertSame('Use the cable.', $sample->steps[0]['details']);
    }

    public function test_saving_with_no_items_clears_the_value(): void
    {
        $sample = Sample::factory()->create(['faqs' => [['question' => 'Old?']]]);

        $this->ajax('onSaveExamples', ['exampleDefaultFaqs_loaded' => 1, 'faqs' => ''])->assertOk();

        $this->assertNull($sample->refresh()->faqs);
    }

    public function test_preview_mode_renders_the_items_without_the_toolbar(): void
    {
        Sample::factory()->create(['faqs' => [['question' => 'Which bulbs?', 'answer' => 'Any E27 bulb.']]]);

        $html = $this->section($this->page(), 'preview');

        $this->assertStringContainsString('Which bulbs?', $html);
        $this->assertStringContainsString('data-repeater-expand', $html);
        $this->assertStringNotContainsString('name="faqs_preview[0][question]"', $html);
        $this->assertStringNotContainsString('data-repeater-cmd="add"', $html);
        $this->assertStringNotContainsString('repeater-item-checkbox', $html);
    }

    public function test_preview_mode_refuses_to_add_items(): void
    {
        Sample::factory()->create(['faqs' => [['question' => 'Which bulbs?']]]);

        $this->ajax('examplePreviewFaqsPreview::onAddItem')
            ->assertStatus(400)
            ->assertJsonPath('__ajax.message', 'Access Denied');
    }
}
