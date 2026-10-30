<?php

namespace App\Controllers\Reference;

use App\Classes\ReferenceControllerBase;
use App\Controllers\Reference\FormsController\BehaviourHandlers;
use App\Controllers\Reference\FormsController\FieldsHandlers;
use App\Controllers\Reference\FormsController\StandaloneForms;
use App\Models\Sample;
use Illuminate\Contracts\View\View;

/**
 * FormsController holds the Forms reference pages, with their AJAX handlers in the FieldsHandlers and BehaviourHandlers traits.
 */
class FormsController extends ReferenceControllerBase
{
    use BehaviourHandlers;
    use FieldsHandlers;
    use StandaloneForms;

    /**
     * fields displays the Field Types page.
     */
    public function fields(): View
    {
        $this->makeExampleForms('forms/fields', [
            'text', 'email', 'number', 'password', 'textarea', 'textarea-sizes',
            'dropdown', 'dropdown-model-options', 'dropdown-preset', 'option-styles',
            'radio', 'checkbox', 'switch', 'checkboxlist', 'checkboxlist-nested', 'checkboxlist-scrollable',
            'balloon-selector', 'balloon-allow-empty', 'balloon-read-only',
            'section', 'hint', 'hint-partial', 'ruler', 'partial', 'partial-ajax',
        ]);

        return view('reference.forms.fields', $this->exampleVars());
    }

    /**
     * layout displays the Layout page, where the horizontal and survey examples set their form options on the widget.
     */
    public function layout(): View
    {
        $forms = $this->makeExampleForms('forms/layout', [
            'tabs', 'lazy-tabs', 'secondary-tabs', 'spans', 'row-span', 'sections',
            'comments', 'tooltips', 'attributes', 'css-class',
            'horizontal', 'survey', 'read-only', 'disabled',
        ]);

        $forms['horizontal']->horizontalMode = true;
        $forms['survey']->surveyMode = true;

        return view('reference.forms.layout', $this->exampleVars() + [
            'standaloneForms' => $this->makeStandaloneForms('forms/layout', ['preview'], $this->referenceSample(), [
                'arrayName' => 'Preview',
                'previewMode' => true,
            ]),
        ]);
    }

    /**
     * behaviour displays the Behaviour page, where the context, default and preset examples also edit a new record.
     */
    public function behaviour(): View
    {
        $this->makeExampleForms('forms/behaviour', [
            'validation', 'dependent-fields',
            'trigger-checked', 'trigger-value', 'trigger-actions', 'trigger-multi-value', 'trigger-parent',
            'change-handler', 'refresh', 'nested-names', 'contexts',
        ]);

        return view('reference.forms.behaviour', $this->exampleVars() + [
            'standaloneForms' => $this->makeStandaloneForms('forms/behaviour', ['contexts', 'defaults', 'presets'], new Sample(['name' => 'Untitled sample']), [
                'arrayName' => 'NewSample',
                'context' => 'create',
            ]),
        ]);
    }
}
