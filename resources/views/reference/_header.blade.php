{{-- Renders the page title and intro from the reference registry, then the navigation between the pages of its group --}}
@php
    [, $referenceGroupCode, $referencePageCode] = explode('.', request()->route()->getName(), 3);
    $referenceGroup = config("reference.groups.{$referenceGroupCode}");
    $referencePage = $referenceGroup['pages'][$referencePageCode];
@endphp

<p class="reference-eyebrow">
    <a href="{{ route('reference.index') }}">
        Reference
    </a>
    <span aria-hidden="true">/</span>
    {{ $referenceGroup['label'] }}
</p>

<h1 class="mb-3">
    {{ $referencePage['label'] }}
</h1>

@isset($intro)
    <p class="fg-3 mb-5 reference-intro">
        {!! $intro !!}
    </p>
@endisset

<nav class="nav nav-pills reference-nav mb-7" aria-label="{{ $referenceGroup['label'] }} pages">
    @foreach ($referenceGroup['pages'] as $pageCode => $page)
        @php
            $isCurrentPage = $pageCode === $referencePageCode;
        @endphp
        <a
            @class(['nav-link', 'active' => $isCurrentPage])
            href="{{ route("reference.{$referenceGroupCode}.{$pageCode}") }}"
            @if ($isCurrentPage) aria-current="page" @endif>
            <i class="ph {{ $page['icon'] }}" aria-hidden="true"></i>
            {{ $page['label'] }}
        </a>
    @endforeach
</nav>
