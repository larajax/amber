{{-- Renders widgets above the controller methods that build them and the files that configure them --}}
<div class="ui-example is-stacked">
    <div @class(['ui-example-preview', $previewClass ?? null])>
        @foreach ($widgets as $widget)
            {{ $widget }}
        @endforeach
    </div>

    @foreach ($methods ?? [] as $method)
        @include('reference._method_source', ['method' => $method])
    @endforeach

    @foreach ($sources ?? [] as $path)
        @include('reference._source', ['path' => $path])
    @endforeach
</div>
