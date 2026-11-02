@extends('layouts.app')
@section('title', 'Filter Options')
@section('content')

@include('reference._header', [
    'intro' => 'Scope options set starting values, replace the default queries and link scopes together.',
])

<section class="ui-section" id="building">
    @include('reference._heading', ['id' => 'building', 'title' => 'The filters on this page', 'caption' => '<code>makeOptionFilters()</code> and the list of posts each filter narrows.'])
    <div class="ui-example">
        @include('reference._method_source', ['method' => 'makeOptionFilters'])
        @include('reference._method_source', ['method' => 'makeFilteredPosts'])
    </div>
</section>

<section class="ui-section" id="defaults">
    @include('reference._heading', ['id' => 'defaults', 'title' => 'Default values', 'caption' => '<code>default</code> on a checkbox and a dropdown.'])
    @include('reference._widget_example', [
        'widgets' => [$examples['defaults']['filter'], $examples['defaults']['list']],
        'sources' => ['resources/ui/reference/filters/options/defaults.yaml'],
    ])
</section>

<section class="ui-section" id="conditions">
    @include('reference._heading', ['id' => 'conditions', 'title' => 'Conditions', 'caption' => 'SQL <code>conditions</code> on dropdown, number, date and text scopes.'])
    @include('reference._widget_example', [
        'widgets' => [$examples['conditions']['filter'], $examples['conditions']['list']],
        'sources' => ['resources/ui/reference/filters/options/conditions.yaml'],
    ])
</section>

<section class="ui-section" id="empty-option">
    @include('reference._heading', ['id' => 'empty-option', 'title' => 'Empty option', 'caption' => '<code>emptyOption</code> on a group, beside a switch and a search box.'])
    @include('reference._widget_example', [
        'widgets' => [$emptyOptionToolbar, $emptyOptionFilter, $emptyOptionList],
        'methods' => ['makeEmptyOptionFilter', 'makeEmptyOptionToolbar'],
        'sources' => ['resources/ui/reference/filters/options/empty-option.yaml'],
    ])
</section>

<section class="ui-section" id="group-options">
    @include('reference._heading', ['id' => 'group-options', 'title' => 'Group options', 'caption' => '<code>modelClass</code>, <code>optionsScope</code> and <code>matchMode: toggle</code>.'])
    @include('reference._widget_example', [
        'widgets' => [$examples['group-options']['filter'], $examples['group-options']['list']],
        'sources' => ['resources/ui/reference/filters/options/group-options.yaml'],
    ])
</section>

<section class="ui-section" id="date-options">
    @include('reference._heading', ['id' => 'date-options', 'title' => 'Date picker options', 'caption' => 'Date limits, week display and <code>useTimezone: false</code>.'])
    @include('reference._widget_example', [
        'widgets' => [$examples['date-options']['filter'], $examples['date-options']['list']],
        'sources' => ['resources/ui/reference/filters/options/date-options.yaml'],
    ])
</section>

<section class="ui-section" id="number-options">
    @include('reference._heading', ['id' => 'number-options', 'title' => 'Number options', 'caption' => 'Formatted numbers, with text or an icon inside the inputs.'])
    @include('reference._widget_example', [
        'widgets' => [$examples['number-options']['filter'], $examples['number-options']['list']],
        'sources' => ['resources/ui/reference/filters/options/number-options.yaml'],
    ])
</section>

<section class="ui-section" id="names">
    @include('reference._heading', ['id' => 'names', 'title' => 'Names, labels and order', 'caption' => '<code>valueFrom</code>, <code>shortLabel</code>, <code>before</code> and <code>cssClass</code>.'])
    @include('reference._widget_example', [
        'widgets' => [$examples['names']['filter'], $examples['names']['list']],
        'sources' => ['resources/ui/reference/filters/options/names.yaml'],
    ])
</section>

<section class="ui-section" id="depends">
    @include('reference._heading', ['id' => 'depends', 'title' => 'Model scopes and dependent scopes', 'caption' => '<code>modelScope</code> and a scope that <code>dependsOn</code> it.'])
    @include('reference._widget_example', [
        'widgets' => [$dependsFilter, $dependsList],
        'methods' => ['makeDependsFilter', 'makeFilteredUsers'],
        'sources' => [
            'resources/ui/reference/filters/options/depends.yaml',
            'resources/ui/reference/filters/users.yaml',
        ],
    ])
</section>

<section class="ui-section" id="search">
    @include('reference._heading', ['id' => 'search', 'title' => 'Search and list setup', 'caption' => 'The <code>search</code> and <code>setupHandler</code> options, after the scopes.'])
    @include('reference._widget_example', [
        'widgets' => [$searchFilter, $searchList],
        'methods' => ['makeSearchFilter'],
        'sources' => ['resources/ui/reference/filters/options/search.yaml'],
    ])
</section>

<section class="ui-section" id="primary-search">
    @include('reference._heading', ['id' => 'primary-search', 'title' => 'Search in its own row', 'caption' => '<code>primary: \'search\'</code> moves the search box and list setup above the scopes.'])
    @include('reference._widget_example', [
        'widgets' => [$primarySearchFilter, $primarySearchList],
        'methods' => ['makePrimarySearchFilter'],
        'sources' => ['resources/ui/reference/filters/options/search.yaml'],
    ])
</section>

<section class="ui-section" id="primary-tabs">
    @include('reference._heading', ['id' => 'primary-tabs', 'title' => 'A dropdown as tabs', 'caption' => '<code>primary</code> naming a dropdown scope shows its options as tabs above the other scopes.'])
    @include('reference._widget_example', [
        'widgets' => [$primaryTabsFilter, $primaryTabsList],
        'methods' => ['makePrimaryTabsFilter'],
        'sources' => ['resources/ui/reference/filters/options/primary-tabs.yaml'],
    ])
</section>

@endsection
