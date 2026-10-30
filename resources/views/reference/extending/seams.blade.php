@extends('layouts.app')
@section('title', 'URLs and Timezones')
@section('content')

@include('reference._header', [
    'intro' => 'Three resolvers adapt the widgets to your application: one turns widget paths into addresses, one turns links with your own scheme into addresses, and one gives the viewer\'s timezone. Each is a closure you set once.',
])

<section class="ui-section" id="resolvers">
    <h2>
        Setting the resolvers
    </h2>
    <p class="fg-3">
        An app sets its resolvers in a service provider's <code>boot()</code> method. This page sets them in its action instead, so they only apply here, and removes them when the request ends.
    </p>
    <div class="ui-example">
        @include('reference._source', ['path' => 'app/Controllers/Reference/ExtendingController/SeamResolvers.php'])
    </div>
</section>

<section class="ui-section" id="ui-url">
    <h2>
        Ui::url()
    </h2>
    <p class="fg-3">
        <code>Ui::url()</code> is what the widgets call for every path they link to. Without a URL resolver it returns <code>url($path)</code>. A link with a registered scheme goes to its link resolver instead, and comes back empty when it points nowhere.
    </p>
    @include('reference._example', ['view' => 'reference.extending.examples.seams-url', 'stacked' => true])
</section>

<section class="ui-section" id="widget-paths">
    <h2>
        Widget paths
    </h2>
    <p class="fg-3">
        <code>Ui::resolveUrlUsing()</code> receives each widget path, such as a list's <code>recordUrl</code>, a linkage column's <code>linkUrl</code> or a toolbar button's <code>url</code>. A real resolver might add an admin prefix. This one sends every path back to this page, so click a row, a preview link or the button to see the path it produced. Full addresses in <code>linkUrl</code> are left alone.
    </p>
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
    <h2>
        Scheme links
    </h2>
    <p class="fg-3">
        <code>Ui::resolveLinkUsing()</code> registers a resolver for one scheme, such as <code>reference://</code>. A linkage column shows a resolved link with the address as its text, and a link that points nowhere as plain text. A toolbar button with a scheme link links to its address, or renders as a plain button when there is none. Other schemes, such as <code>https://</code>, pass through.
    </p>
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
    <h2>
        Viewer timezone
    </h2>
    <p class="fg-3">
        <code>Ui::resolveTimezoneUsing()</code> returns the timezone of the person viewing, such as one saved on their account. Here it comes from the address, so pick one. <code>Ui::timezone()</code> returns it, or the app timezone when the resolver returns nothing or a name that is not a timezone.
    </p>
    @include('reference._example', ['view' => 'reference.extending.examples.seams-timezone', 'stacked' => true])
</section>

<section class="ui-section" id="timezone-lists">
    <h2>
        Lists and filters in the viewer timezone
    </h2>
    <p class="fg-3">
        A <code>datetime</code> column shows the stored time in the viewer timezone, while a <code>date</code> column shows the date as stored. A date filter reads the picked day in the viewer timezone, so in Sydney the 21st of October includes a sample published at 13:30 on the 20th, app time. <code>useTimezone: false</code> turns the conversion off for a column or scope.
    </p>
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
        <h2>
            Storing scheme links
        </h2>
        <p class="fg-3">
            A field can store the scheme link itself, so the address follows the page if its URL changes. The first sample's link shows in the Scheme links list after saving.
        </p>
        @include('reference._form_example', ['name' => 'page-link'])
    </section>

    <section class="ui-section" id="published-at">
        <h2>
            Date picker in the viewer timezone
        </h2>
        <p class="fg-3">
            The date picker shows the stored time in the viewer timezone and converts what you pick back to the app timezone when it saves. The <code>date</code> and <code>time</code> modes, and <code>useTimezone: false</code>, leave the value as stored.
        </p>
        @include('reference._form_example', ['name' => 'published-at'])
    </section>

    @include('reference._save_bar')
</form>

@endsection
