@extends('layouts.app')
@section('title', 'Filter Options')
@section('content')

@include('reference._header', [
    'intro' => 'Scope options set the starting values, replace the default queries with SQL or model scopes, and link scopes together. Most examples below are built like those on Scope Types, a filter of posts from its own YAML file, and the last two show their methods.',
])

<section class="ui-section" id="building">
    <h2>
        The filters on this page
    </h2>
    <p class="fg-3">
        The method that builds the filters of posts, and the list each one filters.
    </p>
    <div class="ui-example">
        @include('reference._method_source', ['method' => 'makeOptionFilters'])
        @include('reference._method_source', ['method' => 'makeFilteredPosts'])
    </div>
</section>

<section class="ui-section" id="defaults">
    <h2>
        Default values
    </h2>
    <p class="fg-3">
        <code>default</code> sets the value a scope starts with, so these posts start without the archived ones and with only the approved status. A change to the scope lasts for the session, and Clear Filters goes back to the defaults.
    </p>
    @include('reference._widget_example', [
        'widgets' => [$examples['defaults']['filter'], $examples['defaults']['list']],
        'sources' => ['resources/ui/reference/filters/options/defaults.yaml'],
    ])
</section>

<section class="ui-section" id="conditions">
    <h2>
        Conditions
    </h2>
    <p class="fg-3">
        <code>conditions</code> replaces the default query with SQL. A dropdown takes one condition with <code>:value</code> for the picked option. Number, date and text scopes take one condition for each choice they offer, keyed by its name, where <code>true</code> keeps the default query. They offer only the named choices, and with one choice the popover shows it as a label. Date conditions use <code>:value</code> for the date and time, <code>:valueDate</code> for the date alone, and <code>:after</code> and <code>:before</code> for a range.
    </p>
    @include('reference._widget_example', [
        'widgets' => [$examples['conditions']['filter'], $examples['conditions']['list']],
        'sources' => ['resources/ui/reference/filters/options/conditions.yaml'],
    ])
</section>

<section class="ui-section" id="empty-option">
    <h2>
        Empty option
    </h2>
    <p class="fg-3">
        <code>emptyOption</code> on a group adds an option for the records with no value, here the posts without tags. It widens only its own scope, so the published switch and the search still apply.
    </p>
    @include('reference._widget_example', [
        'widgets' => [$emptyOptionToolbar, $emptyOptionFilter, $emptyOptionList],
        'methods' => ['makeEmptyOptionFilter', 'makeEmptyOptionToolbar'],
        'sources' => ['resources/ui/reference/filters/options/empty-option.yaml'],
    ])
</section>

<section class="ui-section" id="group-options">
    <h2>
        Group options
    </h2>
    <p class="fg-3">
        <code>modelClass</code> names the model that supplies a group's options, and <code>optionsScope</code> applies one of its scopes to them, here offering only the categories that hold posts. <code>matchMode: toggle</code> adds a choice between including and excluding the picked options.
    </p>
    @include('reference._widget_example', [
        'widgets' => [$examples['group-options']['filter'], $examples['group-options']['list']],
        'sources' => ['resources/ui/reference/filters/options/group-options.yaml'],
    ])
</section>

<section class="ui-section" id="date-options">
    <h2>
        Date picker options
    </h2>
    <p class="fg-3">
        <code>minDate</code> and <code>maxDate</code> limit the dates that can be picked, <code>yearRange</code> sets how many years either side the year view offers, <code>firstDay</code> starts the week on Monday in place of the locale's first day, and <code>showWeekNumber</code> numbers the weeks. <code>useTimezone: false</code> compares the dates as stored, in place of the viewer's timezone.
    </p>
    @include('reference._widget_example', [
        'widgets' => [$examples['date-options']['filter'], $examples['date-options']['list']],
        'sources' => ['resources/ui/reference/filters/options/date-options.yaml'],
    ])
</section>

<section class="ui-section" id="names">
    <h2>
        Names, labels and order
    </h2>
    <p class="fg-3">
        <code>valueFrom</code> filters a column with a name other than the scope's, and <code>shortLabel</code> replaces the label in the bar. Scopes show in the order of the file, which <code>before</code> and <code>after</code> change, and <code>cssClass</code> adds a class to the scope.
    </p>
    @include('reference._widget_example', [
        'widgets' => [$examples['names']['filter'], $examples['names']['list']],
        'sources' => ['resources/ui/reference/filters/options/names.yaml'],
    ])
</section>

<section class="ui-section" id="depends">
    <h2>
        Model scopes and dependent scopes
    </h2>
    <p class="fg-3">
        <code>modelScope</code> runs a model scope in place of the default query, passing it the scope with its value, here filtering the users by their email domain. <code>dependsOn</code> clears a scope and renders it again when the named scope changes, so a picked name resets when the domain does.
    </p>
    @include('reference._widget_example', [
        'widgets' => [$dependsFilter, $dependsList],
        'methods' => ['makeDependsFilter', 'makeFilteredUsers'],
        'sources' => [
            'resources/ui/reference/filters/options/depends.yaml',
            'resources/ui/reference/filters/users.yaml',
        ],
    ])
</section>

@endsection
