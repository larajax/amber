<?php

namespace App\Controllers\Reference;

use App\Classes\ReferenceControllerBase;
use Illuminate\Contracts\View\View;

/**
 * FormWidgetsController holds the Form Widgets reference pages.
 */
class FormWidgetsController extends ReferenceControllerBase
{
    /**
     * codeEditor displays the Code Editor page.
     */
    public function codeEditor(): View
    {
        $forms = $this->makeExampleForms('form-widgets/code-editor', [
            'default', 'language', 'size', 'word-wrap', 'indentation', 'appearance', 'typing', 'read-only', 'preview',
        ]);

        $forms['preview']->previewMode = true;

        return view('reference.form-widgets.code-editor', $this->exampleVars());
    }

    /**
     * colorPicker displays the Color Picker page.
     */
    public function colorPicker(): View
    {
        $this->makeExampleForms('form-widgets/color-picker', [
            'default', 'fixed-swatches', 'model-colors', 'alpha', 'input-only', 'read-only',
        ]);

        return view('reference.form-widgets.color-picker', $this->exampleVars());
    }

    /**
     * dataTable displays the Data Table page.
     */
    public function dataTable(): View
    {
        $forms = $this->makeExampleForms('form-widgets/data-table', [
            'default', 'dates', 'dropdowns', 'model-options', 'validation', 'fixed-rows', 'no-toolbar',
            'search-and-csv', 'sorting', 'height', 'empty-state', 'column-options', 'read-only', 'preview',
        ]);

        $forms['preview']->previewMode = true;

        return view('reference.form-widgets.data-table', $this->exampleVars());
    }

    /**
     * datePicker displays the Date Picker page.
     */
    public function datePicker(): View
    {
        $forms = $this->makeExampleForms('form-widgets/date-picker', [
            'default', 'date', 'time', 'twelve-hour', 'hours-only', 'format', 'date-range', 'disabled-weekdays',
            'disabled-dates', 'year-range', 'week-layout', 'midnight', 'timezone', 'read-only', 'disabled', 'preview',
        ]);

        $forms['preview']->previewMode = true;

        return view('reference.form-widgets.date-picker', $this->exampleVars());
    }

    /**
     * fileUpload displays the File Upload page.
     */
    public function fileUpload(): View
    {
        $this->makeExampleForms('form-widgets/file-upload', [
            'image-single', 'image-multi', 'file-single', 'file-multi', 'private', 'attach-on-upload', 'preview',
        ]);

        return view('reference.form-widgets.file-upload', $this->exampleVars());
    }

    /**
     * markdownEditor displays the Markdown Editor page.
     */
    public function markdownEditor(): View
    {
        $forms = $this->makeExampleForms('form-widgets/markdown-editor', [
            'default', 'side-by-side', 'size', 'safe-mode', 'read-only', 'disabled', 'preview',
        ]);

        $forms['preview']->previewMode = true;

        return view('reference.form-widgets.markdown-editor', $this->exampleVars());
    }

    /**
     * nestedForm displays the Nested Form page.
     */
    public function nestedForm(): View
    {
        $this->makeExampleForms('form-widgets/nested-form', ['default', 'tabs', 'no-panel', 'relation', 'preview']);

        return view('reference.form-widgets.nested-form', $this->exampleVars());
    }

    /**
     * recordFinder displays the Record Finder page.
     */
    public function recordFinder(): View
    {
        $this->makeExampleForms('form-widgets/record-finder', [
            'default', 'scopes', 'popup', 'tree', 'without-relation', 'read-only', 'preview',
        ]);

        return view('reference.form-widgets.record-finder', $this->exampleVars());
    }

    /**
     * relation displays the Relation page.
     */
    public function relation(): View
    {
        $this->makeExampleForms('form-widgets/relation', [
            'dropdown', 'checkbox-list', 'options-query', 'tree', 'tree-checkbox-list', 'quick-create', 'read-only', 'disabled',
        ]);

        return view('reference.form-widgets.relation', $this->exampleVars());
    }

    /**
     * repeater displays the Repeater page.
     */
    public function repeater(): View
    {
        $this->makeExampleForms('form-widgets/repeater', [
            'default', 'limits', 'collapsed', 'fixed-order', 'groups', 'builder', 'tabs', 'nested', 'parent-fields', 'relation', 'preview',
        ]);

        return view('reference.form-widgets.repeater', $this->exampleVars());
    }

    /**
     * richEditor displays the Rich Editor page.
     */
    public function richEditor(): View
    {
        $forms = $this->makeExampleForms('form-widgets/rich-editor', [
            'default', 'toolbar', 'full-toolbar', 'uploads', 'line-breaks', 'editor-options', 'size', 'read-only', 'preview',
        ]);

        $forms['preview']->previewMode = true;

        return view('reference.form-widgets.rich-editor', $this->exampleVars());
    }

    /**
     * sensitive displays the Sensitive page.
     */
    public function sensitive(): View
    {
        $this->makeExampleForms('form-widgets/sensitive', ['default', 'allow-copy', 'textarea', 'read-only', 'tab-change']);

        return view('reference.form-widgets.sensitive', $this->exampleVars());
    }

    /**
     * tagList displays the Tag List page.
     */
    public function tagList(): View
    {
        $this->makeExampleForms('form-widgets/tag-list', [
            'array', 'string', 'options', 'use-key', 'relation', 'relation-custom', 'read-only', 'preview',
        ]);

        return view('reference.form-widgets.tag-list', $this->exampleVars());
    }
}
