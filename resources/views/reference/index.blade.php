@extends('layouts.app')
@section('title', 'Reference')
@section('content')

<h1 class="mb-3">
    Reference
</h1>
<p class="fg-3 mb-7 reference-intro">
    One page for every part of larajax/ui, grouped the way the library is organised. Each example shows the YAML or Blade that renders it. The Showcase pages in the sidebar put the same pieces together as a small blog.
</p>

<div class="reference-groups">
    @foreach ($groups as $groupCode => $group)
        <section class="reference-group-card">
            <h2>
                <i class="ph {{ $group['icon'] }}" aria-hidden="true"></i>
                {{ $group['label'] }}
            </h2>
            <p class="fg-3">
                {{ $group['description'] }}
            </p>
            <ul class="reference-page-links">
                @foreach ($group['pages'] as $pageCode => $page)
                    <li>
                        <a href="{{ route("reference.{$groupCode}.{$pageCode}") }}">
                            <i class="ph {{ $page['icon'] }}" aria-hidden="true"></i>
                            {{ $page['label'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </section>
    @endforeach
</div>

@endsection
