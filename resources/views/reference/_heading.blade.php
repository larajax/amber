{{-- Renders an example section's heading and one-line caption, with a link to the docs section that has the same anchor --}}
@php
    [, $headingGroupCode, $headingPageCode] = explode('.', request()->route()->getName(), 3);
    $headingDocsUrl = config('reference.docs_url')."/{$headingGroupCode}/{$headingPageCode}#{$id}";
@endphp

<h2>
    {{ $title }}
    <a
        class="reference-docs-anchor"
        href="{{ $headingDocsUrl }}"
        target="_blank"
        rel="noopener"
        title="Read about this in the docs"
        aria-label="Read about {{ $title }} in the docs">
        <i class="ph ph-book-open" aria-hidden="true"></i>
    </a>
</h2>
<p class="fg-3">
    {!! $caption !!}
</p>
