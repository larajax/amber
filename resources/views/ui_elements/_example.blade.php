{{-- Renders an example view, then shows its source, plus any views it relies on, so the snippet is always the code that produced the element --}}
@php
    $exampleView = 'ui_elements.examples.'.$name;
    $relatedViews = $related ?? [];
@endphp

<div @class(['ui-example', 'is-stacked' => $stacked ?? false])>
    <div class="ui-example-preview">
        @include($exampleView)
    </div>

    <div class="ui-example-source">
        <pre><code>{{ trim(file_get_contents(view()->getFinder()->find($exampleView))) }}</code></pre>
    </div>

    @foreach ($relatedViews as $relatedView => $caption)
        <div class="ui-example-source">
            <div class="ui-example-caption">
                {{ $caption }}
                <code>
                    resources/views/{{ str_replace('.', '/', $relatedView) }}.blade.php
                </code>
            </div>
            <pre><code>{{ trim(file_get_contents(view()->getFinder()->find($relatedView))) }}</code></pre>
        </div>
    @endforeach
</div>
