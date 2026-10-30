@extends('layouts.app')
@section('title', 'Icons')
@section('content')

@include('reference._header', [
    'intro' => 'Semantic icon keys, such as <code>action.delete</code>, mapped to icon classes by a swappable icon pack.',
])

<section class="ui-section" id="keys">
    @include('reference._heading', ['id' => 'keys', 'title' => 'Semantic keys', 'caption' => '<code>Ui::icon()</code> with four keys from the default pack.'])
    @include('reference._example', ['view' => 'reference.extending.examples.icon-keys'])
</section>

<section class="ui-section" id="attributes">
    @include('reference._heading', ['id' => 'attributes', 'title' => 'Attributes and plain classes', 'caption' => 'Attributes as the second argument, and icon classes with no key.'])
    @include('reference._example', ['view' => 'reference.extending.examples.icon-attributes'])
</section>

<section class="ui-section" id="icon-class">
    @include('reference._heading', ['id' => 'icon-class', 'title' => 'Classes for your own markup', 'caption' => '<code>Ui::iconClass()</code> from the default pack and the bootstrap pack.'])
    @include('reference._example', ['view' => 'reference.extending.examples.icon-class'])
</section>

<section class="ui-section" id="packs">
    @include('reference._heading', ['id' => 'packs', 'title' => 'Icon packs', 'caption' => 'Shipped packs, and an <code>amber</code> pack from <code>Ui::registerIconPack()</code>.'])
    <div class="ui-example">
        <div class="ui-example-preview">
            @include('reference.extending.examples.icon-packs')
        </div>
        @include('reference._source', ['path' => 'resources/views/reference/extending/examples/icon-packs.blade.php'])
        @include('reference._source', [
            'path' => 'app/Controllers/Reference/ExtendingController/IconRegistrations.php',
            'caption' => 'The page action calls registerExampleIcons()',
        ])
    </div>
</section>

<section class="ui-section" id="extend">
    @include('reference._heading', ['id' => 'extend', 'title' => 'Adding keys to every pack', 'caption' => 'Two status keys from <code>Ui::extendIcons()</code>, in two packs.'])
    @include('reference._example', ['view' => 'reference.extending.examples.icon-extend'])
</section>

<section class="ui-section" id="all-keys">
    @include('reference._heading', ['id' => 'all-keys', 'title' => 'Every key in the default pack', 'caption' => 'The keys the widgets use, plus the two this page adds.'])
    @include('reference._example', ['view' => 'reference.extending.examples.icon-pack-keys', 'stacked' => true])
</section>

@endsection
