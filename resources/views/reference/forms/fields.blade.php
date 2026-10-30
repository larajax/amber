@extends('layouts.app')
@section('title', 'Field Types')
@section('content')

@include('reference._header', [
    'intro' => 'The form widget renders a field for each definition in its YAML, picking how it renders from the field <code>type</code>.',
])

<form data-request="onSaveExamples" data-request-flash>
    <section class="ui-section" id="text">
        @include('reference._heading', ['id' => 'text', 'title' => 'Text', 'caption' => '<code>text</code>, the default type, with a <code>placeholder</code>.'])
        @include('reference._form_example', ['name' => 'text'])
    </section>

    <section class="ui-section" id="email">
        @include('reference._heading', ['id' => 'email', 'title' => 'Email', 'caption' => '<code>email</code>, checked again by the model\'s <code>email</code> rule.'])
        @include('reference._form_example', ['name' => 'email'])
    </section>

    <section class="ui-section" id="number">
        @include('reference._heading', ['id' => 'number', 'title' => 'Number', 'caption' => '<code>number</code> with <code>min</code>, <code>max</code> and <code>step</code>.'])
        @include('reference._form_example', ['name' => 'number'])
    </section>

    <section class="ui-section" id="password">
        @include('reference._heading', ['id' => 'password', 'title' => 'Password', 'caption' => '<code>password</code> under an underscore name, so it is never saved.'])
        @include('reference._form_example', ['name' => 'password'])
    </section>

    <section class="ui-section" id="textarea">
        @include('reference._heading', ['id' => 'textarea', 'title' => 'Textarea', 'caption' => '<code>textarea</code>, a multi-line input.'])
        @include('reference._form_example', ['name' => 'textarea'])
    </section>

    <section class="ui-section" id="textarea-sizes">
        @include('reference._heading', ['id' => 'textarea-sizes', 'title' => 'Textarea sizes', 'caption' => '<code>size</code>: <code>tiny</code>, <code>small</code>, <code>huge</code> and <code>giant</code>.'])
        @include('reference._form_example', ['name' => 'textarea-sizes'])
    </section>

    <section class="ui-section" id="dropdown">
        @include('reference._heading', ['id' => 'dropdown', 'title' => 'Dropdown', 'caption' => '<code>emptyOption</code>, <code>placeholder</code> and <code>showSearch: false</code>.'])
        @include('reference._form_example', ['name' => 'dropdown'])
    </section>

    <section class="ui-section" id="dropdown-model-options">
        @include('reference._heading', ['id' => 'dropdown-model-options', 'title' => 'Options from the model', 'caption' => '<code>get{Field}Options</code>, <code>optionsMethod</code> and a static <code>Class::method</code>.'])
        @include('reference._form_example', ['name' => 'dropdown-model-options'])
    </section>

    <section class="ui-section" id="dropdown-preset">
        @include('reference._heading', ['id' => 'dropdown-preset', 'title' => 'Option presets', 'caption' => '<code>optionsPreset: icons@phosphor</code>.'])
        @include('reference._form_example', ['name' => 'dropdown-preset'])
    </section>

    <section class="ui-section" id="option-styles">
        @include('reference._heading', ['id' => 'option-styles', 'title' => 'Option colors and icons', 'caption' => 'Status dots, icons and detailed options from <code>getLevelOptions</code>.'])
        @include('reference._form_example', ['name' => 'option-styles'])
    </section>

    <section class="ui-section" id="radio">
        @include('reference._heading', ['id' => 'radio', 'title' => 'Radio list', 'caption' => '<code>inlineOptions</code>, descriptions and a <code>disabled</code> option.'])
        @include('reference._form_example', ['name' => 'radio'])
    </section>

    <section class="ui-section" id="checkbox">
        @include('reference._heading', ['id' => 'checkbox', 'title' => 'Checkbox', 'caption' => '<code>checkbox</code>, with its label and comment beside the box.'])
        @include('reference._form_example', ['name' => 'checkbox'])
    </section>

    <section class="ui-section" id="switch">
        @include('reference._heading', ['id' => 'switch', 'title' => 'Switch', 'caption' => '<code>switch</code>, saved like a checkbox.'])
        @include('reference._form_example', ['name' => 'switch'])
    </section>

    <section class="ui-section" id="checkboxlist">
        @include('reference._heading', ['id' => 'checkboxlist', 'title' => 'Checkbox list', 'caption' => '<code>checkboxlist</code> with <code>inlineOptions</code> and <code>quickselect</code>.'])
        @include('reference._form_example', ['name' => 'checkboxlist'])
    </section>

    <section class="ui-section" id="checkboxlist-nested">
        @include('reference._heading', ['id' => 'checkboxlist-nested', 'title' => 'Nested options', 'caption' => 'Options with <code>children</code>, ticked together by <code>cumulative</code>.'])
        @include('reference._form_example', ['name' => 'checkboxlist-nested'])
    </section>

    <section class="ui-section" id="checkboxlist-scrollable">
        @include('reference._heading', ['id' => 'checkboxlist-scrollable', 'title' => 'Long lists', 'caption' => 'More than ten options, in a scrolling box.'])
        @include('reference._form_example', ['name' => 'checkboxlist-scrollable'])
    </section>

    <section class="ui-section" id="balloon-selector">
        @include('reference._heading', ['id' => 'balloon-selector', 'title' => 'Balloon selector', 'caption' => '<code>balloon-selector</code>, one option from a row of pills.'])
        @include('reference._form_example', ['name' => 'balloon-selector'])
    </section>

    <section class="ui-section" id="balloon-allow-empty">
        @include('reference._heading', ['id' => 'balloon-allow-empty', 'title' => 'Clearing a balloon selector', 'caption' => '<code>allowEmpty</code> clears the value on a second click.'])
        @include('reference._form_example', ['name' => 'balloon-allow-empty'])
    </section>

    <section class="ui-section" id="balloon-read-only">
        @include('reference._heading', ['id' => 'balloon-read-only', 'title' => 'Read-only balloon selector', 'caption' => '<code>readOnly</code> locks the pills and keeps the value.'])
        @include('reference._form_example', ['name' => 'balloon-read-only'])
    </section>

    <section class="ui-section" id="section">
        @include('reference._heading', ['id' => 'section', 'title' => 'Section', 'caption' => '<code>section</code>, then one with <code>displayMode: simple</code>.'])
        @include('reference._form_example', ['name' => 'section'])
    </section>

    <section class="ui-section" id="hint">
        @include('reference._heading', ['id' => 'hint', 'title' => 'Hint', 'caption' => '<code>hint</code> in each <code>mode</code>.'])
        @include('reference._form_example', ['name' => 'hint'])
    </section>

    <section class="ui-section" id="hint-partial">
        @include('reference._heading', ['id' => 'hint-partial', 'title' => 'Hint from a partial', 'caption' => '<code>hint</code> with a <code>path</code> to a partial.'])
        @include('reference._form_example', ['name' => 'hint-partial'])
        <div class="ui-example reference-forms-related">
            @include('reference._source', ['path' => 'resources/views/reference/forms/_hint_record_checklist.php', 'caption' => 'The partial'])
        </div>
    </section>

    <section class="ui-section" id="ruler">
        @include('reference._heading', ['id' => 'ruler', 'title' => 'Ruler', 'caption' => '<code>ruler</code>, plain and with a label.'])
        @include('reference._form_example', ['name' => 'ruler'])
    </section>

    <section class="ui-section" id="partial">
        @include('reference._heading', ['id' => 'partial', 'title' => 'Partial', 'caption' => '<code>partial</code> rendering a view of the form model.'])
        @include('reference._form_example', ['name' => 'partial'])
        <div class="ui-example reference-forms-related">
            @include('reference._source', ['path' => 'resources/views/reference/forms/_field_record_card.php', 'caption' => 'The partial'])
        </div>
    </section>

    <section class="ui-section" id="partial-ajax">
        @include('reference._heading', ['id' => 'partial-ajax', 'title' => 'A partial with its own input', 'caption' => 'A <code>partial</code> rendering a dropdown fed by a controller handler.'])
        @include('reference._form_example', ['name' => 'partial-ajax'])
        <div class="ui-example reference-forms-related">
            @include('reference._source', ['path' => 'resources/views/reference/forms/_field_timezone.php', 'caption' => 'The partial'])
        </div>
    </section>

    @include('reference._save_bar')
</form>

@endsection
