@extends('layouts.app')
@section('title', 'URLs and Timezones')
@section('content')

@include('reference._header', [
    'intro' => 'Resolvers for widget paths, links with your own scheme, and the viewer\'s timezone.',
])

<section class="ui-section" id="resolvers">
    @include('reference._heading', ['id' => 'resolvers', 'title' => 'Setting the resolvers', 'caption' => 'The three resolvers this page sets for its own requests.'])
    <div class="ui-example">
        @include('reference._source', ['path' => 'app/Controllers/Reference/ExtendingController/SeamResolvers.php'])
    </div>
</section>

<section class="ui-section" id="ui-url">
    @include('reference._heading', ['id' => 'ui-url', 'title' => 'Ui::url()', 'caption' => 'A widget path, a resolved scheme link and one that points nowhere.'])
    @include('reference._example', ['view' => 'reference.extending.examples.seams-url', 'stacked' => true])
</section>

<section class="ui-section" id="widget-paths">
    @include('reference._heading', ['id' => 'widget-paths', 'title' => 'Widget paths', 'caption' => '<code>recordUrl</code>, <code>linkUrl</code> and a toolbar <code>url</code> through <code>Ui::resolveUrlUsing()</code>.'])
    @if (request()->filled('path'))
        {{ Ui::callout(label: 'The resolver received '.request()->query('path'), comment: 'That is the widget path, before the resolver turned it into this address.', success: true) }}
    @endif
    <div class="ui-example is-stacked">
        <div class="ui-example-preview">
            {{ $pathToolbar }}
            {{ $pathList }}
        </div>
        @include('reference._source', ['path' => 'resources/ui/reference/extending/seams/path-buttons.yaml'])
        @include('reference._source', ['path' => 'resources/ui/reference/extending/seams/path-columns.yaml', 'caption' => 'The list sets recordUrl: samples/:id/edit'])
    </div>
</section>

<section class="ui-section" id="scheme-links">
    @include('reference._heading', ['id' => 'scheme-links', 'title' => 'Scheme links', 'caption' => '<code>reference://</code> links through <code>Ui::resolveLinkUsing()</code>, one of them retired.'])
    <div class="ui-example is-stacked">
        <div class="ui-example-preview">
            {{ $linkToolbar }}
            {{ $linkList }}
        </div>
        @include('reference._source', ['path' => 'resources/ui/reference/extending/seams/link-buttons.yaml'])
        @include('reference._source', ['path' => 'resources/ui/reference/extending/seams/link-columns.yaml'])
    </div>
</section>

<section class="ui-section" id="timezone">
    @include('reference._heading', ['id' => 'timezone', 'title' => 'Viewer timezone', 'caption' => 'Pick a timezone for <code>Ui::resolveTimezoneUsing()</code> to return.'])
    @include('reference._example', ['view' => 'reference.extending.examples.seams-timezone', 'stacked' => true])
</section>

<section class="ui-section" id="timezone-lists">
    @include('reference._heading', ['id' => 'timezone-lists', 'title' => 'Lists and filters in the viewer timezone', 'caption' => 'A <code>datetime</code> and a <code>date</code> column, with a <code>date</code> filter.'])
    <div class="ui-example is-stacked">
        <div class="ui-example-preview">
            {{ $timezoneFilter }}
            {{ $timezoneList }}
        </div>
        @include('reference._source', ['path' => 'resources/ui/reference/extending/seams/timezone-scopes.yaml'])
        @include('reference._source', ['path' => 'resources/ui/reference/extending/seams/timezone-columns.yaml'])
    </div>
</section>

<form data-request="onSaveExamples" data-request-flash>
    <section class="ui-section" id="page-link">
        @include('reference._heading', ['id' => 'page-link', 'title' => 'Storing scheme links', 'caption' => 'A dropdown that stores <code>reference://</code> links.'])
        @include('reference._form_example', ['name' => 'page-link'])
    </section>

    <section class="ui-section" id="published-at">
        @include('reference._heading', ['id' => 'published-at', 'title' => 'Date picker in the viewer timezone', 'caption' => 'A <code>datetime</code> date picker in the picked timezone.'])
        @include('reference._form_example', ['name' => 'published-at'])
    </section>

    @include('reference._save_bar')
</form>

@endsection
