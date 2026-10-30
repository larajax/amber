@extends('layouts.app')
@section('title', 'Form Layout')
@section('content')

@include('reference._header', [
    'intro' => 'A form places its fields in up to three sections: fields outside any tabs, primary tabs and secondary tabs. Within a section, <code>span</code> puts each field on a side, and form options switch the whole form to a horizontal, survey or preview layout.',
])

<form data-request="onSaveExamples" data-request-flash>
    <section class="ui-section" id="tabs">
        <h2>
            Tabs
        </h2>
        <p class="fg-3">
            Fields under <code>tabs</code> are grouped by their <code>tab</code> property, and fields with no tab land in <code>defaultTab</code>. <code>icons</code> puts an icon beside a tab title. Fields under the top level <code>fields</code> sit above the tabs.
        </p>
        @include('reference._form_example', ['name' => 'tabs'])
    </section>

    <section class="ui-section" id="lazy-tabs">
        <h2>
            Lazy tabs
        </h2>
        <p class="fg-3">
            A tab named in <code>lazy</code> renders a loading indicator, then fetches its fields from the form's <code>onLazyLoadTab</code> handler the first time it opens. It suits tabs holding heavy widgets.
        </p>
        @include('reference._form_example', ['name' => 'lazy-tabs'])
    </section>

    <section class="ui-section" id="secondary-tabs">
        <h2>
            Secondary tabs
        </h2>
        <p class="fg-3">
            <code>secondaryTabs</code> renders a second tab set below the primary tabs, often for large content fields. <code>activeTab</code> picks the tab that opens first, by name or by position.
        </p>
        @include('reference._form_example', ['name' => 'secondary-tabs'])
    </section>

    <section class="ui-section" id="spans">
        <h2>
            Spans
        </h2>
        <p class="fg-3">
            <code>span</code> places a field on the <code>left</code> or <code>right</code> half, or across the <code>full</code> width. <code>auto</code> takes the right half after a left field, and the left half otherwise.
        </p>
        @include('reference._form_example', ['name' => 'spans'])
    </section>

    <section class="ui-section" id="row-span">
        <h2>
            Rows
        </h2>
        <p class="fg-3">
            Fields with <code>span: row</code> share one grid row, each sized by the column classes in its <code>spanClass</code>.
        </p>
        @include('reference._form_example', ['name' => 'row-span'])
    </section>

    <section class="ui-section" id="sections">
        <h2>
            Collapsible sections
        </h2>
        <p class="fg-3">
            A <code>section</code> field with the container attribute <code>data-field-collapsible</code> starts collapsed, and clicking it shows or hides every field up to the next section.
        </p>
        @include('reference._form_example', ['name' => 'sections'])
    </section>

    <section class="ui-section" id="comments">
        <h2>
            Comments
        </h2>
        <p class="fg-3">
            <code>comment</code> sits below a field and <code>commentAbove</code> between its label and the input. Both are escaped unless <code>commentHtml</code> is on.
        </p>
        @include('reference._form_example', ['name' => 'comments'])
    </section>

    <section class="ui-section" id="tooltips">
        <h2>
            Tooltips
        </h2>
        <p class="fg-3">
            <code>tooltip</code> adds an icon beside the label that shows a hint on hover. As a string it is the hint text. As an array it takes <code>title</code>, <code>icon</code>, <code>placement</code> and <code>isHtml</code>.
        </p>
        @include('reference._form_example', ['name' => 'tooltips'])
    </section>

    <section class="ui-section" id="attributes">
        <h2>
            Input attributes
        </h2>
        <p class="fg-3">
            <code>attributes</code> adds HTML attributes to the input itself, here a <code>pattern</code>, <code>maxlength</code> and <code>title</code> for the browser to check.
        </p>
        @include('reference._form_example', ['name' => 'attributes'])
    </section>

    <section class="ui-section" id="css-class">
        <h2>
            Container class and attributes
        </h2>
        <p class="fg-3">
            <code>cssClass</code> adds a class to the element wrapping the field, and <code>containerAttributes</code> adds HTML attributes to it.
        </p>
        @include('reference._form_example', ['name' => 'css-class'])
    </section>

    <section class="ui-section" id="horizontal">
        <h2>
            Horizontal mode
        </h2>
        <p class="fg-3">
            The form option <code>horizontalMode</code> puts each label in a column beside its field. It belongs to the widget rather than the YAML, so the action sets it with <code>$form-&gt;horizontalMode = true</code>, or passes <code>horizontalMode: true</code> to <code>Form::make()</code>.
        </p>
        @include('reference._form_example', ['name' => 'horizontal'])
    </section>

    <section class="ui-section" id="survey">
        <h2>
            Survey mode
        </h2>
        <p class="fg-3">
            The form option <code>surveyMode</code> renders the tabs one after another, each under a heading, so the whole form reads top to bottom. The action sets it like horizontal mode.
        </p>
        @include('reference._form_example', ['name' => 'survey'])
    </section>

    <section class="ui-section" id="read-only">
        <h2>
            Read-only fields
        </h2>
        <p class="fg-3">
            <code>readOnly</code> stops a field from being changed while still posting its value, so it saves. Checkboxes, switches and radio lists ignore clicks, Select All and Select None leave a checkbox list alone, and a dropdown shows its value as text.
        </p>
        @include('reference._form_example', ['name' => 'read-only'])
    </section>

    <section class="ui-section" id="disabled">
        <h2>
            Disabled fields
        </h2>
        <p class="fg-3">
            <code>disabled</code> locks a field and leaves it out of the saved data, even if a value is posted for it. <code>hidden</code> does the same without rendering the field at all.
        </p>
        @include('reference._form_example', ['name' => 'disabled'])
    </section>

    <section class="ui-section" id="preview">
        <h2>
            Preview mode
        </h2>
        <p class="fg-3">
            The form option <code>previewMode</code> shows every field as plain, uneditable text, for a page that only displays a record. This form is built with <code>Form::make(..., previewMode: true)</code>, and Save leaves it out.
        </p>
        <div class="ui-example is-stacked reference-form-example">
            <div class="ui-example-preview">
                {{ $standaloneForms['preview'] }}
            </div>

            @include('reference._source', ['path' => 'resources/ui/reference/forms/layout/preview.yaml'])
        </div>
    </section>

    @include('reference._save_bar')
</form>

@endsection
