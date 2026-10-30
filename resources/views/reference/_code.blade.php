{{-- Renders source code as a collapsible block under its path, which js/code-blocks.js highlights the first time it opens --}}
@php
    $codeLang = match (true) {
        str_ends_with($path, '.blade.php') => 'blade',
        str_ends_with($path, '.php') => 'php',
        str_ends_with($path, '.yaml'), str_ends_with($path, '.yml') => 'yaml',
        str_ends_with($path, '.js') => 'javascript',
        str_ends_with($path, '.css') => 'css',
        default => 'text',
    };
@endphp

<details
    class="ui-example-source"
    data-code-block
    data-code-lang="{{ $codeLang }}"
    @if ($fragment ?? false) data-code-fragment @endif>
    <summary class="ui-example-caption">
        <i class="ph ph-caret-right ui-example-chevron" aria-hidden="true"></i>
        @isset($caption)
            <span>
                {{ $caption }}
            </span>
        @endisset
        <code>
            {{ $path }}
        </code>
    </summary>
    <pre><code>{{ $code }}</code></pre>
</details>
