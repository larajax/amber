@extends('layouts.app')
@section('title', 'Form Behaviour')
@section('content')

@include('reference._header', [
    'intro' => 'Fields can react to each other in the browser with triggers and presets, or on the server, where the form refreshes fields, calls change handlers and asks the model to adjust them. The model also validates the saved values and supplies defaults for new records.',
])

<form data-request="onSaveExamples" data-request-flash>
    <section class="ui-section" id="validation">
        <h2>
            Validation
        </h2>
        <p class="fg-3">
            The model's <code>$rules</code> check the values on save, and a failed rule stops the save and shows its message. A field the rules require gets the required marker, while the <code>required</code> property shows the marker without checking anything. <code>autoFocus</code> puts the cursor in the name field when the page loads.
        </p>
        @include('reference._form_example', ['name' => 'validation'])
    </section>

    <section class="ui-section" id="dependent-fields">
        <h2>
            Dependent fields
        </h2>
        <p class="fg-3">
            A field lists the fields it relies on in <code>dependsOn</code>. When one of them changes, the form posts its values to its <code>onRefresh</code> handler and redraws the dependent field, so its options can follow the new values. The model's <code>filterFields</code> method runs on every render and refresh, and here hides and relabels the postcode.
        </p>
        @include('reference._form_example', ['name' => 'dependent-fields'])
    </section>

    <section class="ui-section" id="trigger-checked">
        <h2>
            Triggers on a checkbox
        </h2>
        <p class="fg-3">
            <code>trigger</code> changes a field in the browser when another field meets a condition, with no request to the server. The <code>checked</code> and <code>unchecked</code> conditions watch a checkbox or switch, and <code>show</code> or <code>enable</code> apply while the condition holds.
        </p>
        @include('reference._form_example', ['name' => 'trigger-checked'])
    </section>

    <section class="ui-section" id="trigger-value">
        <h2>
            Triggers on a value
        </h2>
        <p class="fg-3">
            <code>value[a][b]</code> holds when the field's value is any of the listed values. A <code>*</code> matches any text, so <code>value[*]</code> is any value, and <code>value[]</code> holds while the field is empty.
        </p>
        @include('reference._form_example', ['name' => 'trigger-value'])
    </section>

    <section class="ui-section" id="trigger-actions">
        <h2>
            Trigger actions
        </h2>
        <p class="fg-3">
            Besides <code>show</code> and <code>enable</code>, the actions are <code>hide</code>, <code>disable</code>, <code>empty</code>, which clears the field, and <code>fill[value]</code>, which sets it. Join actions with a pipe, as in <code>hide|empty</code>.
        </p>
        @include('reference._form_example', ['name' => 'trigger-actions'])
    </section>

    <section class="ui-section" id="trigger-multi-value">
        <h2>
            Triggers on a list
        </h2>
        <p class="fg-3">
            A field that posts several values, such as a checkbox list, is named with <code>[]</code> in the trigger, so the condition reads every ticked option.
        </p>
        @include('reference._form_example', ['name' => 'trigger-multi-value'])
    </section>

    <section class="ui-section" id="trigger-parent">
        <h2>
            Triggers on a parent field
        </h2>
        <p class="fg-3">
            Inside a nested form or a repeater, a trigger names fields of the same level. A leading <code>^</code> climbs one level up to the parent form, and <code>^^</code> climbs two.
        </p>
        @include('reference._form_example', ['name' => 'trigger-parent'])
    </section>

    <section class="ui-section" id="change-handler">
        <h2>
            Change handler
        </h2>
        <p class="fg-3">
            <code>changeHandler</code> names an AJAX handler on the controller, called with the form's values whenever the field changes. This one fills the form from the posted values and redraws the stats partial, without saving.
        </p>
        @include('reference._form_example', ['name' => 'change-handler'])
        <div class="ui-example reference-forms-related">
            @include('reference._source', ['path' => 'app/Controllers/Reference/FormsController/BehaviourHandlers.php', 'caption' => 'The handler'])
        </div>
    </section>

    <section class="ui-section" id="refresh">
        <h2>
            Refreshing the form
        </h2>
        <p class="fg-3">
            Every form has an <code>onRefresh</code> handler, named after its alias, that redraws the form from the posted values without saving them. Posting <code>fields</code> redraws just those fields, as the Recalculate button in this partial does. With no fields, it redraws the whole form.
        </p>
        @include('reference._form_example', ['name' => 'refresh'])
        <div class="ui-example reference-forms-related">
            @include('reference._source', ['path' => 'resources/views/reference/forms/_field_stock_value.php', 'caption' => 'The partial'])
        </div>
    </section>

    <section class="ui-section" id="nested-names">
        <h2>
            Nested names
        </h2>
        <p class="fg-3">
            A name like <code>seo[title]</code> reads and saves a key inside an array attribute, here the <code>seo</code> column, which the model stores as JSON.
        </p>
        @include('reference._form_example', ['name' => 'nested-names'])
    </section>

    <section class="ui-section" id="contexts">
        <h2>
            Contexts
        </h2>
        <p class="fg-3">
            A form has a context, usually <code>create</code> or <code>update</code>, and a field with <code>context</code>, or a name ending in <code>@@context</code>, only appears in that context. This example edits the saved record, so its context is <code>update</code>.
        </p>
        @include('reference._form_example', ['name' => 'contexts'])
        <p class="fg-3 mt-4">
            The same YAML on a new record, in the <code>create</code> context. This form and the two below edit a new record that is never saved, so their fields post under <code>NewSample[...]</code> and Save leaves them out.
        </p>
        <div class="ui-example is-stacked reference-form-example">
            <div class="ui-example-preview">
                {{ $standaloneForms['contexts'] }}
            </div>
        </div>
    </section>

    <section class="ui-section" id="defaults">
        <h2>
            Default values
        </h2>
        <p class="fg-3">
            <code>default</code> fills a field on a new record, and for option fields it names the option to select. <code>defaultFrom</code> copies another attribute of the record instead. Defaults never replace saved values.
        </p>
        <div class="ui-example is-stacked reference-form-example">
            <div class="ui-example-preview">
                {{ $standaloneForms['defaults'] }}
            </div>

            @include('reference._source', ['path' => 'resources/ui/reference/forms/behaviour/defaults.yaml'])
        </div>
    </section>

    <section class="ui-section" id="presets">
        <h2>
            Input presets
        </h2>
        <p class="fg-3">
            <code>preset</code> fills a field from another one as you type, converted by its <code>type</code>: <code>slug</code>, <code>url</code>, <code>camel</code>, <code>file</code> or <code>exact</code>. A preset only fills an empty field, and stops once you edit the field yourself, so it suits create forms.
        </p>
        <div class="ui-example is-stacked reference-form-example">
            <div class="ui-example-preview">
                {{ $standaloneForms['presets'] }}
            </div>

            @include('reference._source', ['path' => 'resources/ui/reference/forms/behaviour/presets.yaml'])
        </div>
    </section>

    <section class="ui-section" id="input-preset">
        <h2>
            The input preset control
        </h2>
        <p class="fg-3">
            Presets work on any input through <code>data-input-preset</code>, a selector for the source input. The control also has <code>snake</code> and <code>namespace</code> types, and <code>data-input-preset-remove-words="false"</code> keeps short words such as "a" and "the".
        </p>
        @include('reference._example', ['view' => 'reference.forms.examples.input-preset', 'stacked' => true])
    </section>

    <section class="ui-section" id="input-trigger">
        <h2>
            The input trigger control
        </h2>
        <p class="fg-3">
            Triggers work on any element through <code>data-trigger</code>, a selector for the inputs to watch, with <code>data-trigger-action</code> and <code>data-trigger-condition</code>.
        </p>
        @include('reference._example', ['view' => 'reference.forms.examples.input-trigger', 'stacked' => true])
    </section>

    @include('reference._save_bar')
</form>

@endsection
