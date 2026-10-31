@extends('layouts.app')
@section('title', 'Icons')
@section('content')

@include('reference._header', [
    'intro' => 'The library draws every icon it shows from a semantic key, which an icon pack maps to icon classes, so you can change them without touching the widgets.',
])

<section class="ui-section" id="extend">
    @include('reference._heading', ['id' => 'extend', 'title' => 'Overriding keys', 'caption' => '<code>Ui::extendIcons()</code> swaps <code>callout.info</code> and <code>search.icon</code>, and the widgets follow.'])
    <div class="ui-example is-stacked">
        <div class="ui-example-preview">
            @include('reference.extending.examples.icon-extend')
        </div>
        @include('reference._source', ['path' => 'resources/views/reference/extending/examples/icon-extend.blade.php'])
        @include('reference._source', [
            'path' => 'app/Controllers/Reference/ExtendingController/IconRegistrations.php',
            'caption' => 'The page action calls registerExampleIcons()',
        ])
    </div>
</section>

<section class="ui-section" id="packs">
    @include('reference._heading', ['id' => 'packs', 'title' => 'Icon packs', 'caption' => 'The shipped packs, and an <code>amber</code> pack from <code>Ui::registerIconPack()</code>.'])
    @include('reference._example', ['view' => 'reference.extending.examples.icon-packs', 'stacked' => true])
</section>

<section class="ui-section" id="all-keys">
    @include('reference._heading', ['id' => 'all-keys', 'title' => 'The keys the library uses', 'caption' => 'Every key in the default pack, after this page\'s overrides.'])
    @include('reference._example', ['view' => 'reference.extending.examples.icon-pack-keys', 'stacked' => true])
</section>

@endsection
