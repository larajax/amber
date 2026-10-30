{{-- Renders an example view, then shows its source, plus any views it relies on, so the snippet is always the code that produced the element --}}
@php
    $exampleSources = [$view => null] + ($related ?? []);
@endphp

<div @class(['ui-example', 'is-stacked' => $stacked ?? false])>
    <div class="ui-example-preview">
        @include($view)
    </div>

    @foreach ($exampleSources as $sourceView => $caption)
        @php
            $sourceFile = view()->getFinder()->find($sourceView);
        @endphp
        @include('reference._code', [
            'path' => str_replace(DIRECTORY_SEPARATOR, '/', substr($sourceFile, strlen(base_path()) + 1)),
            'caption' => $caption,
            'code' => trim(file_get_contents($sourceFile)),
        ])
    @endforeach
</div>
