@extends('layouts.app')
@section('title', 'Form Behaviour')
@section('content')

@include('reference._header', [
    'intro' => 'Fields react to each other in the browser and on the server, while the model validates them and supplies defaults.',
])

<form data-request="onSaveExamples" data-request-flash>
    <section class="ui-section" id="validation">
        @include('reference._heading', ['id' => 'validation', 'title' => 'Validation', 'caption' => 'The model\'s <code>$rules</code>, the <code>required</code> property and <code>autoFocus</code>.'])
        @include('reference._form_example', ['name' => 'validation'])
    </section>

    <section class="ui-section" id="dependent-fields">
        @include('reference._heading', ['id' => 'dependent-fields', 'title' => 'Dependent fields', 'caption' => 'A <code>dependsOn</code> chain and the model\'s <code>filterFields</code>.'])
        @include('reference._form_example', ['name' => 'dependent-fields'])
    </section>

    <section class="ui-section" id="trigger-checked">
        @include('reference._heading', ['id' => 'trigger-checked', 'title' => 'Triggers on a checkbox', 'caption' => 'The <code>checked</code> condition with the <code>show</code> and <code>enable</code> actions.'])
        @include('reference._form_example', ['name' => 'trigger-checked'])
    </section>

    <section class="ui-section" id="trigger-value">
        @include('reference._heading', ['id' => 'trigger-value', 'title' => 'Triggers on a value', 'caption' => '<code>value[...]</code> conditions, with a wildcard and an empty value.'])
        @include('reference._form_example', ['name' => 'trigger-value'])
    </section>

    <section class="ui-section" id="trigger-actions">
        @include('reference._heading', ['id' => 'trigger-actions', 'title' => 'Trigger actions', 'caption' => 'The <code>fill[high]</code>, <code>disable</code> and <code>hide|empty</code> actions.'])
        @include('reference._form_example', ['name' => 'trigger-actions'])
    </section>

    <section class="ui-section" id="trigger-multi-value">
        @include('reference._heading', ['id' => 'trigger-multi-value', 'title' => 'Triggers on a list', 'caption' => 'A trigger on a checkbox list, named <code>features[]</code>.'])
        @include('reference._form_example', ['name' => 'trigger-multi-value'])
    </section>

    <section class="ui-section" id="trigger-parent">
        @include('reference._heading', ['id' => 'trigger-parent', 'title' => 'Triggers on a parent field', 'caption' => '<code>^size</code> reaching the parent form from a nested form.'])
        @include('reference._form_example', ['name' => 'trigger-parent'])
    </section>

    <section class="ui-section" id="change-handler">
        @include('reference._heading', ['id' => 'change-handler', 'title' => 'Change handler', 'caption' => '<code>changeHandler</code> calling <code>onChangeHeadline</code> on the controller.'])
        @include('reference._form_example', ['name' => 'change-handler'])
        <div class="ui-example reference-forms-related">
            @include('reference._source', ['path' => 'app/Controllers/Reference/FormsController/BehaviourHandlers.php', 'caption' => 'The handler'])
        </div>
    </section>

    <section class="ui-section" id="refresh">
        @include('reference._heading', ['id' => 'refresh', 'title' => 'Refreshing the form', 'caption' => 'A partial posting <code>fields</code> to the form\'s <code>onRefresh</code> handler.'])
        @include('reference._form_example', ['name' => 'refresh'])
        <div class="ui-example reference-forms-related">
            @include('reference._source', ['path' => 'resources/views/reference/forms/_field_stock_value.php', 'caption' => 'The partial'])
        </div>
    </section>

    <section class="ui-section" id="nested-names">
        @include('reference._heading', ['id' => 'nested-names', 'title' => 'Nested names', 'caption' => 'Names like <code>seo[title]</code>, saved inside one JSON column.'])
        @include('reference._form_example', ['name' => 'nested-names'])
    </section>

    <section class="ui-section" id="contexts">
        @include('reference._heading', ['id' => 'contexts', 'title' => 'Contexts', 'caption' => '<code>context</code> and <code>@update</code> names, on the saved record.'])
        @include('reference._form_example', ['name' => 'contexts'])
        <p class="fg-3 mt-4">
            The same YAML on a new record, in the <code>create</code> context.
        </p>
        <div class="ui-example is-stacked reference-form-example">
            <div class="ui-example-preview">
                {{ $standaloneForms['contexts'] }}
            </div>
        </div>
    </section>

    <section class="ui-section" id="defaults">
        @include('reference._heading', ['id' => 'defaults', 'title' => 'Default values', 'caption' => '<code>default</code> and <code>defaultFrom</code> on a new record.'])
        <div class="ui-example is-stacked reference-form-example">
            <div class="ui-example-preview">
                {{ $standaloneForms['defaults'] }}
            </div>

            @include('reference._source', ['path' => 'resources/ui/reference/forms/behaviour/defaults.yaml'])
        </div>
    </section>

    <section class="ui-section" id="presets">
        @include('reference._heading', ['id' => 'presets', 'title' => 'Input presets', 'caption' => '<code>preset</code> with each <code>type</code>, on a new record.'])
        <div class="ui-example is-stacked reference-form-example">
            <div class="ui-example-preview">
                {{ $standaloneForms['presets'] }}
            </div>

            @include('reference._source', ['path' => 'resources/ui/reference/forms/behaviour/presets.yaml'])
        </div>
    </section>

    <section class="ui-section" id="input-preset">
        @include('reference._heading', ['id' => 'input-preset', 'title' => 'The input preset control', 'caption' => '<code>data-input-preset</code> with the <code>namespace</code> and <code>snake</code> types.'])
        @include('reference._example', ['view' => 'reference.forms.examples.input-preset', 'stacked' => true])
    </section>

    <section class="ui-section" id="input-trigger">
        @include('reference._heading', ['id' => 'input-trigger', 'title' => 'The input trigger control', 'caption' => '<code>data-trigger</code> on plain HTML, with no form widget.'])
        @include('reference._example', ['view' => 'reference.forms.examples.input-trigger', 'stacked' => true])
    </section>

    @include('reference._save_bar')
</form>

@endsection
