@extends('layouts.app')
@section('title', 'Field Types')
@section('content')

@include('reference._header', [
    'intro' => 'The form widget renders a field for each definition in its YAML, picking the partial from the field <code>type</code>. These are the built-in types and the options each one takes. Form widgets, such as the color picker, have pages of their own.',
])

<form data-request="onSaveExamples" data-request-flash>
    <section class="ui-section" id="text">
        <h2>
            Text
        </h2>
        <p class="fg-3">
            <code>text</code> is the default type, so a field with no <code>type</code> renders a single line input. <code>placeholder</code> shows while the field is empty.
        </p>
        @include('reference._form_example', ['name' => 'text'])
    </section>

    <section class="ui-section" id="email">
        <h2>
            Email
        </h2>
        <p class="fg-3">
            <code>email</code> renders an email input, which brings up the email keyboard on phones. The format is checked on save by the model's <code>email</code> rule.
        </p>
        @include('reference._form_example', ['name' => 'email'])
    </section>

    <section class="ui-section" id="number">
        <h2>
            Number
        </h2>
        <p class="fg-3">
            <code>number</code> passes <code>min</code>, <code>max</code> and <code>step</code> to the input. With no <code>step</code>, any decimal is accepted and the spinner is hidden. An empty number saves as null.
        </p>
        @include('reference._form_example', ['name' => 'number'])
    </section>

    <section class="ui-section" id="password">
        <h2>
            Password
        </h2>
        <p class="fg-3">
            <code>password</code> always renders empty. A name that starts with an underscore is purged before saving, so this value is never stored.
        </p>
        @include('reference._form_example', ['name' => 'password'])
    </section>

    <section class="ui-section" id="textarea">
        <h2>
            Textarea
        </h2>
        <p class="fg-3">
            <code>textarea</code> renders a multi-line input.
        </p>
        @include('reference._form_example', ['name' => 'textarea'])
    </section>

    <section class="ui-section" id="textarea-sizes">
        <h2>
            Textarea sizes
        </h2>
        <p class="fg-3">
            <code>size</code> sets the height of a textarea: <code>tiny</code>, <code>small</code>, <code>large</code> (the default), <code>huge</code> or <code>giant</code>.
        </p>
        @include('reference._form_example', ['name' => 'textarea-sizes'])
    </section>

    <section class="ui-section" id="dropdown">
        <h2>
            Dropdown
        </h2>
        <p class="fg-3">
            <code>dropdown</code> renders a searchable select from its <code>options</code>. <code>emptyOption</code> adds an option that clears the value. <code>placeholder</code> prompts while the field is empty, with a clear button in place of an empty option. <code>showSearch: false</code> removes the search box.
        </p>
        @include('reference._form_example', ['name' => 'dropdown'])
    </section>

    <section class="ui-section" id="dropdown-model-options">
        <h2>
            Options from the model
        </h2>
        <p class="fg-3">
            With no <code>options</code>, the field calls <code>get{Field}Options</code> on the model. <code>optionsMethod</code> names any model method, or a static method as <code>Class::method</code>. An option with <code>optgroup: true</code> and <code>children</code> becomes an option group. Radio lists, checkbox lists and balloon selectors take options the same ways.
        </p>
        @include('reference._form_example', ['name' => 'dropdown-model-options'])
    </section>

    <section class="ui-section" id="dropdown-preset">
        <h2>
            Option presets
        </h2>
        <p class="fg-3">
            <code>optionsPreset</code> takes a built-in list: <code>icons</code>, <code>icons@phosphor</code>, <code>icons@bootstrap</code>, <code>locales</code>, <code>flags</code>, <code>flags@short</code> or <code>timezones</code>.
        </p>
        @include('reference._form_example', ['name' => 'dropdown-preset'])
    </section>

    <section class="ui-section" id="option-styles">
        <h2>
            Option colors and icons
        </h2>
        <p class="fg-3">
            An option written as <code>[label, value]</code> shows a status dot when the value is a hex color, an image when it is a path, and an icon otherwise. A model method can return detailed options with <code>icon</code>, <code>color</code>, <code>comment</code>, <code>disabled</code> and <code>children</code>. A disabled option cannot be picked, unless it is the saved value.
        </p>
        @include('reference._form_example', ['name' => 'option-styles'])
    </section>

    <section class="ui-section" id="radio">
        <h2>
            Radio list
        </h2>
        <p class="fg-3">
            <code>radio</code> picks one option from a list. <code>inlineOptions</code> puts the options on one line, <code>[label, description]</code> adds a description under an option, and a detailed option can be <code>disabled</code>.
        </p>
        @include('reference._form_example', ['name' => 'radio'])
    </section>

    <section class="ui-section" id="checkbox">
        <h2>
            Checkbox
        </h2>
        <p class="fg-3">
            <code>checkbox</code> saves <code>1</code> when ticked. A hidden input posts <code>0</code> when it is not, so unticking is saved too.
        </p>
        @include('reference._form_example', ['name' => 'checkbox'])
    </section>

    <section class="ui-section" id="switch">
        <h2>
            Switch
        </h2>
        <p class="fg-3">
            <code>switch</code> saves like a checkbox, drawn as a switch.
        </p>
        @include('reference._form_example', ['name' => 'switch'])
    </section>

    <section class="ui-section" id="checkboxlist">
        <h2>
            Checkbox list
        </h2>
        <p class="fg-3">
            <code>checkboxlist</code> ticks any number of options, saved as an array. <code>quickselect</code> adds Select All and Select None, and Shift selects a range of boxes.
        </p>
        @include('reference._form_example', ['name' => 'checkboxlist'])
    </section>

    <section class="ui-section" id="checkboxlist-nested">
        <h2>
            Nested options
        </h2>
        <p class="fg-3">
            Options with <code>children</code> nest below their parent. With <code>cumulative</code>, ticking a parent ticks its children too.
        </p>
        @include('reference._form_example', ['name' => 'checkboxlist-nested'])
    </section>

    <section class="ui-section" id="checkboxlist-scrollable">
        <h2>
            Long lists
        </h2>
        <p class="fg-3">
            More than ten options scroll inside a box, turn on the quick selection buttons, and stack even with <code>inlineOptions</code>.
        </p>
        @include('reference._form_example', ['name' => 'checkboxlist-scrollable'])
    </section>

    <section class="ui-section" id="balloon-selector">
        <h2>
            Balloon selector
        </h2>
        <p class="fg-3">
            <code>balloon-selector</code> picks one option from a row of pills and stores it in a hidden input. Each option is focusable, and Enter or Space picks it.
        </p>
        @include('reference._form_example', ['name' => 'balloon-selector'])
    </section>

    <section class="ui-section" id="balloon-allow-empty">
        <h2>
            Clearing a balloon selector
        </h2>
        <p class="fg-3">
            <code>allowEmpty</code> lets the active option be clicked again to clear the value.
        </p>
        @include('reference._form_example', ['name' => 'balloon-allow-empty'])
    </section>

    <section class="ui-section" id="balloon-read-only">
        <h2>
            Read-only balloon selector
        </h2>
        <p class="fg-3">
            With <code>readOnly</code>, the options leave the tab order and ignore clicks, and the hidden input still posts the value.
        </p>
        @include('reference._form_example', ['name' => 'balloon-read-only'])
    </section>

    <section class="ui-section" id="section">
        <h2>
            Section
        </h2>
        <p class="fg-3">
            <code>section</code> renders a heading from <code>label</code> and a line of text from <code>comment</code>, to open a group of fields. <code>displayMode: simple</code> shows the label as plain text instead.
        </p>
        @include('reference._form_example', ['name' => 'section'])
    </section>

    <section class="ui-section" id="hint">
        <h2>
            Hint
        </h2>
        <p class="fg-3">
            <code>hint</code> renders a callout from <code>label</code> and <code>comment</code>, in the <code>mode</code> <code>info</code>, <code>tip</code>, <code>success</code>, <code>warning</code> or <code>danger</code>.
        </p>
        @include('reference._form_example', ['name' => 'hint'])
    </section>

    <section class="ui-section" id="ruler">
        <h2>
            Ruler
        </h2>
        <p class="fg-3">
            <code>ruler</code> draws a horizontal line between fields, with an optional label and comment.
        </p>
        @include('reference._form_example', ['name' => 'ruler'])
    </section>

    <section class="ui-section" id="partial">
        <h2>
            Partial
        </h2>
        <p class="fg-3">
            <code>partial</code> renders an app partial from <code>path</code>, with <code>$formModel</code>, <code>$field</code> and <code>$value</code> in scope. The label and comment still render around it.
        </p>
        @include('reference._form_example', ['name' => 'partial'])
        <div class="ui-example reference-forms-related">
            @include('reference._source', ['path' => 'resources/views/reference/forms/_field_record_card.php', 'caption' => 'The partial'])
        </div>
    </section>

    <section class="ui-section" id="partial-ajax">
        <h2>
            A partial with its own input
        </h2>
        <p class="fg-3">
            A partial can render its own input under the field name, so the form saves it like any other field. This one renders a dropdown whose options come from a handler on the controller.
        </p>
        @include('reference._form_example', ['name' => 'partial-ajax'])
        <div class="ui-example reference-forms-related">
            @include('reference._source', ['path' => 'resources/views/reference/forms/_field_timezone.php', 'caption' => 'The partial'])
        </div>
    </section>

    @include('reference._save_bar')
</form>

@endsection
