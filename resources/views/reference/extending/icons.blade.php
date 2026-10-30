@extends('layouts.app')
@section('title', 'Icons')
@section('content')

@include('reference._header', [
    'intro' => 'Widgets name their icons with semantic keys, such as <code>action.delete</code>, and an icon pack maps each key to icon classes. Swap the pack, or replace a few keys, and every widget follows.',
])

<section class="ui-section" id="keys">
    <h2>
        Semantic keys
    </h2>
    <p class="fg-3">
        <code>Ui::icon()</code> renders the <code>&lt;i&gt;</code> element for a key from the default pack, hidden from screen readers. It returns a string, so print it with <code>&lbrace;!! !!&rbrace;</code>.
    </p>
    @include('reference._example', ['view' => 'reference.extending.examples.icon-keys'])
</section>

<section class="ui-section" id="attributes">
    <h2>
        Attributes and plain classes
    </h2>
    <p class="fg-3">
        The second argument adds attributes, and its <code>class</code> merges with the icon's own. A name that is not a key passes through as icon classes, so <code>ph ph-rocket-launch</code> works wherever a key does.
    </p>
    @include('reference._example', ['view' => 'reference.extending.examples.icon-attributes'])
</section>

<section class="ui-section" id="icon-class">
    <h2>
        Classes for your own markup
    </h2>
    <p class="fg-3">
        <code>Ui::iconClass()</code> returns only the classes, for markup you write yourself. Both methods take a pack name as their last argument.
    </p>
    @include('reference._example', ['view' => 'reference.extending.examples.icon-class'])
</section>

<section class="ui-section" id="packs">
    <h2>
        Icon packs
    </h2>
    <p class="fg-3">
        The library ships the <code>phosphor</code>, <code>bootstrap</code> and <code>october</code> packs. <code>Ui::registerIconPack()</code> adds another from an array, an <code>IconPack</code> class or a closure, here the phosphor pack with three keys swapped. Set <code>LARAJAX_ICONS</code> to make a pack the default, or list keys under <code>overrides</code> in <code>config/larajax-icons.php</code> to replace them in every pack.
    </p>
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
    <h2>
        Adding keys to every pack
    </h2>
    <p class="fg-3">
        <code>Ui::extendIcons()</code> adds or replaces keys in every pack, from an array or from a closure that receives each pack's icons and name. The method above adds two status keys this way, with bootstrap glyphs for the bootstrap pack.
    </p>
    @include('reference._example', ['view' => 'reference.extending.examples.icon-extend'])
</section>

<section class="ui-section" id="all-keys">
    <h2>
        Every key in the default pack
    </h2>
    <p class="fg-3">
        The keys the widgets use, with the two keys this page adds.
    </p>
    @include('reference._example', ['view' => 'reference.extending.examples.icon-pack-keys', 'stacked' => true])
</section>

@endsection
