<?php

namespace Tests\Feature;

use Larajax\Ui\Classes\FormField;
use Tests\TestCase;

class TriggerTest extends TestCase
{
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

    public function test_a_trigger_names_a_sibling_field_inside_the_same_form(): void
    {
        $this->assertSame('[name="has_discount"]', $this->triggerSelector(null, 'has_discount'));
        $this->assertSame('[name="Sample[has_discount]"]', $this->triggerSelector('Sample', 'has_discount'));
        $this->assertSame('[name="steps[0][title]"]', $this->triggerSelector('steps[0]', 'title', 'enable'));
        $this->assertSame('[name="tags[]"]', $this->triggerSelector(null, 'tags[]'));
    }

    public function test_a_parent_reference_from_a_repeater_item_reaches_a_top_form_without_an_array_name(): void
    {
        $this->assertSame('[name="step_style"]', $this->triggerSelector('steps[0]', '^step_style'));
        $this->assertSame('[name="step_style"]', $this->triggerSelector('steps[12]', '^step_style', 'enable'));
    }

    public function test_a_parent_reference_from_a_repeater_item_keeps_the_top_form_array_name(): void
    {
        $this->assertSame('[name="Sample[step_style]"]', $this->triggerSelector('Sample[steps][0]', '^step_style'));
    }

    public function test_parent_references_climb_one_nested_repeater_level_each(): void
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
    }

    public function test_a_parent_reference_keeps_a_multi_value_suffix(): void
    {
        $this->assertSame('[name="tags[]"]', $this->triggerSelector('steps[0]', '^tags[]'));
    }
}
