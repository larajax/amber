@extends('layouts.app')
@section('title', 'Form Layout')
@section('content')

@include('reference._header', [
    'intro' => 'Tabs, spans and sections place the fields, and form options switch the whole form to a horizontal, survey or preview layout.',
])

<form data-request="onSaveExamples" data-request-flash>
    <section class="ui-section" id="tabs">
        @include('reference._heading', ['id' => 'tabs', 'title' => 'Tabs', 'caption' => '<code>tabs</code> with <code>defaultTab</code>, <code>icons</code> and a field above them.'])
        @include('reference._form_example', ['name' => 'tabs'])
    </section>

    <section class="ui-section" id="lazy-tabs">
        @include('reference._heading', ['id' => 'lazy-tabs', 'title' => 'Lazy tabs', 'caption' => '<code>lazy</code> loads the Notes tab the first time it opens.'])
        @include('reference._form_example', ['name' => 'lazy-tabs'])
    </section>

    <section class="ui-section" id="secondary-tabs">
        @include('reference._heading', ['id' => 'secondary-tabs', 'title' => 'Secondary tabs', 'caption' => '<code>secondaryTabs</code> opening on their <code>activeTab</code>.'])
        @include('reference._form_example', ['name' => 'secondary-tabs'])
    </section>

    <section class="ui-section" id="spans">
        @include('reference._heading', ['id' => 'spans', 'title' => 'Spans', 'caption' => '<code>span</code>: <code>left</code>, <code>right</code>, <code>full</code> and <code>auto</code>.'])
        @include('reference._form_example', ['name' => 'spans'])
    </section>

    <section class="ui-section" id="row-span">
        @include('reference._heading', ['id' => 'row-span', 'title' => 'Rows', 'caption' => '<code>span: row</code>, each field sized by its <code>spanClass</code>.'])
        @include('reference._form_example', ['name' => 'row-span'])
    </section>

    <section class="ui-section" id="sections">
        @include('reference._heading', ['id' => 'sections', 'title' => 'Collapsible sections', 'caption' => 'Sections with the <code>data-field-collapsible</code> container attribute.'])
        @include('reference._form_example', ['name' => 'sections'])
    </section>

    <section class="ui-section" id="comments">
        @include('reference._heading', ['id' => 'comments', 'title' => 'Comments', 'caption' => '<code>comment</code>, <code>commentAbove</code> and <code>commentHtml</code>.'])
        @include('reference._form_example', ['name' => 'comments'])
    </section>

    <section class="ui-section" id="tooltips">
        @include('reference._heading', ['id' => 'tooltips', 'title' => 'Tooltips', 'caption' => '<code>tooltip</code> as text, and as an array.'])
        @include('reference._form_example', ['name' => 'tooltips'])
    </section>

    <section class="ui-section" id="attributes">
        @include('reference._heading', ['id' => 'attributes', 'title' => 'Input attributes', 'caption' => '<code>attributes</code> adding <code>pattern</code>, <code>maxlength</code> and <code>title</code> to the input.'])
        @include('reference._form_example', ['name' => 'attributes'])
    </section>

    <section class="ui-section" id="css-class">
        @include('reference._heading', ['id' => 'css-class', 'title' => 'Container class and attributes', 'caption' => '<code>cssClass</code> and <code>containerAttributes</code>.'])
        @include('reference._form_example', ['name' => 'css-class'])
    </section>

    <section class="ui-section" id="horizontal">
        @include('reference._heading', ['id' => 'horizontal', 'title' => 'Horizontal mode', 'caption' => 'The <code>horizontalMode</code> form option, set by the page action.'])
        @include('reference._form_example', ['name' => 'horizontal'])
    </section>

    <section class="ui-section" id="survey">
        @include('reference._heading', ['id' => 'survey', 'title' => 'Survey mode', 'caption' => 'The <code>surveyMode</code> form option, set by the page action.'])
        @include('reference._form_example', ['name' => 'survey'])
    </section>

    <section class="ui-section" id="read-only">
        @include('reference._heading', ['id' => 'read-only', 'title' => 'Read-only fields', 'caption' => '<code>readOnly</code> on each kind of field.'])
        @include('reference._form_example', ['name' => 'read-only'])
    </section>

    <section class="ui-section" id="disabled">
        @include('reference._heading', ['id' => 'disabled', 'title' => 'Disabled fields', 'caption' => '<code>disabled</code> fields, left out of the saved data.'])
        @include('reference._form_example', ['name' => 'disabled'])
    </section>

    <section class="ui-section" id="preview">
        @include('reference._heading', ['id' => 'preview', 'title' => 'Preview mode', 'caption' => 'The <code>previewMode</code> form option, which Save leaves out.'])
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
