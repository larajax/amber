@extends('layouts.app')
@section('title', 'Scope Types')
@section('content')

@include('reference._header', [
    'intro' => 'The <code>Filter</code> widget is a bar of scopes above a list, and <code>bindToListWidget()</code> applies the active scopes to the list query, refreshing the list when one changes. Each scope remembers its value for the session, and the menu at the start of the bar clears them all.',
])

<section class="ui-section" id="building">
    <h2>
        The filters on this page
    </h2>
    <p class="fg-3">
        Each example binds a filter, with the scopes from its own YAML file, to a list of the seeded posts. The lists share one columns file.
    </p>
    <div class="ui-example">
        @include('reference._method_source', ['method' => 'makeScopeExamples'])
        @include('reference._source', ['path' => 'resources/ui/reference/filters/posts.yaml'])
    </div>
</section>

<section class="ui-section" id="checkbox">
    <h2>
        Checkbox
    </h2>
    <p class="fg-3">
        <code>checkbox</code> is on or off. Ticked, it keeps the records whose column of the same name is true, or that match the SQL in <code>conditions</code>.
    </p>
    @include('reference._widget_example', [
        'widgets' => [$examples['checkbox']['filter'], $examples['checkbox']['list']],
        'sources' => ['resources/ui/reference/filters/scopes/checkbox.yaml'],
    ])
</section>

<section class="ui-section" id="switch">
    <h2>
        Switch
    </h2>
    <p class="fg-3">
        <code>switch</code> has three states: click once for the first of its two <code>conditions</code>, again for the second, and again to turn it off. Without conditions the states are false and true on the column of the same name.
    </p>
    @include('reference._widget_example', [
        'widgets' => [$examples['switch']['filter'], $examples['switch']['list']],
        'sources' => ['resources/ui/reference/filters/scopes/switch.yaml'],
    ])
</section>

<section class="ui-section" id="dropdown">
    <h2>
        Dropdown
    </h2>
    <p class="fg-3">
        <code>dropdown</code> picks one value for the column. The options come from <code>options</code>, or from <code>optionsMethod</code>, given here as a static method whose options carry icons. <code>emptyOption</code> is the option that applies no filter.
    </p>
    @include('reference._widget_example', [
        'widgets' => [$examples['dropdown']['filter'], $examples['dropdown']['list']],
        'sources' => ['resources/ui/reference/filters/scopes/dropdown.yaml'],
    ])
</section>

<section class="ui-section" id="group">
    <h2>
        Group
    </h2>
    <p class="fg-3">
        <code>group</code> opens a searchable list of options to pick several. When the scope is named after a relation, the options are its records, labelled by <code>nameFrom</code>, and the filter keeps the records related to any picked one. With <code>options</code> it matches the column against the picked values.
    </p>
    @include('reference._widget_example', [
        'widgets' => [$examples['group']['filter'], $examples['group']['list']],
        'sources' => ['resources/ui/reference/filters/scopes/group.yaml'],
    ])
</section>

<section class="ui-section" id="date">
    <h2>
        Date
    </h2>
    <p class="fg-3">
        <code>date</code> compares the column with a date picked from a calendar: on, not on, before, after or between two dates. <code>daterange</code> offers only between, and leaving one end empty turns it into before or after.
    </p>
    @include('reference._widget_example', [
        'widgets' => [$examples['date']['filter'], $examples['date']['list']],
        'sources' => ['resources/ui/reference/filters/scopes/date.yaml'],
    ])
</section>

<section class="ui-section" id="text">
    <h2>
        Text
    </h2>
    <p class="fg-3">
        <code>text</code> matches the column against typed text, either exactly or as a part of the value.
    </p>
    @include('reference._widget_example', [
        'widgets' => [$examples['text']['filter'], $examples['text']['list']],
        'sources' => ['resources/ui/reference/filters/scopes/text.yaml'],
    ])
</section>

<section class="ui-section" id="number">
    <h2>
        Number
    </h2>
    <p class="fg-3">
        <code>number</code> compares the column with a number: equal to, greater, less, or between two numbers. <code>numberrange</code> offers only between, and leaving one end empty turns it into greater or less.
    </p>
    @include('reference._widget_example', [
        'widgets' => [$examples['number']['filter'], $examples['number']['list']],
        'sources' => ['resources/ui/reference/filters/scopes/number.yaml'],
    ])
</section>

@endsection
