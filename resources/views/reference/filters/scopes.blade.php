@extends('layouts.app')
@section('title', 'Scope Types')
@section('content')

@include('reference._header', [
    'intro' => 'The <code>Filter</code> widget is a bar of scopes that narrows the list it is bound to.',
])

<section class="ui-section" id="building">
    @include('reference._heading', ['id' => 'building', 'title' => 'The filters on this page', 'caption' => '<code>makeScopeExamples()</code> binds each filter to a list of posts.'])
    <div class="ui-example">
        @include('reference._method_source', ['method' => 'makeScopeExamples'])
        @include('reference._source', ['path' => 'resources/ui/reference/filters/posts.yaml'])
    </div>
</section>

<section class="ui-section" id="checkbox">
    @include('reference._heading', ['id' => 'checkbox', 'title' => 'Checkbox', 'caption' => '<code>checkbox</code> on a column and with SQL <code>conditions</code>.'])
    @include('reference._widget_example', [
        'widgets' => [$examples['checkbox']['filter'], $examples['checkbox']['list']],
        'sources' => ['resources/ui/reference/filters/scopes/checkbox.yaml'],
    ])
</section>

<section class="ui-section" id="switch">
    @include('reference._heading', ['id' => 'switch', 'title' => 'Switch', 'caption' => '<code>switch</code> with two <code>conditions</code>.'])
    @include('reference._widget_example', [
        'widgets' => [$examples['switch']['filter'], $examples['switch']['list']],
        'sources' => ['resources/ui/reference/filters/scopes/switch.yaml'],
    ])
</section>

<section class="ui-section" id="dropdown">
    @include('reference._heading', ['id' => 'dropdown', 'title' => 'Dropdown', 'caption' => '<code>dropdown</code> with <code>optionsMethod</code> and <code>emptyOption</code>.'])
    @include('reference._widget_example', [
        'widgets' => [$examples['dropdown']['filter'], $examples['dropdown']['list']],
        'sources' => ['resources/ui/reference/filters/scopes/dropdown.yaml'],
    ])
</section>

<section class="ui-section" id="group">
    @include('reference._heading', ['id' => 'group', 'title' => 'Group', 'caption' => '<code>group</code> on a relation and with <code>options</code>.'])
    @include('reference._widget_example', [
        'widgets' => [$examples['group']['filter'], $examples['group']['list']],
        'sources' => ['resources/ui/reference/filters/scopes/group.yaml'],
    ])
</section>

<section class="ui-section" id="date">
    @include('reference._heading', ['id' => 'date', 'title' => 'Date', 'caption' => '<code>date</code> and <code>daterange</code>.'])
    @include('reference._widget_example', [
        'widgets' => [$examples['date']['filter'], $examples['date']['list']],
        'sources' => ['resources/ui/reference/filters/scopes/date.yaml'],
    ])
</section>

<section class="ui-section" id="text">
    @include('reference._heading', ['id' => 'text', 'title' => 'Text', 'caption' => '<code>text</code> matching the whole value or a part of it.'])
    @include('reference._widget_example', [
        'widgets' => [$examples['text']['filter'], $examples['text']['list']],
        'sources' => ['resources/ui/reference/filters/scopes/text.yaml'],
    ])
</section>

<section class="ui-section" id="number">
    @include('reference._heading', ['id' => 'number', 'title' => 'Number', 'caption' => '<code>number</code> and <code>numberrange</code>.'])
    @include('reference._widget_example', [
        'widgets' => [$examples['number']['filter'], $examples['number']['list']],
        'sources' => ['resources/ui/reference/filters/scopes/number.yaml'],
    ])
</section>

@endsection
