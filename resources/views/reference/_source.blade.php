{{-- Shows an app file under its path, so an example can print the YAML or PHP that configures it --}}
<div class="ui-example-source">
    <div class="ui-example-caption">
        @isset($caption)
            {{ $caption }}
        @endisset
        <code>
            {{ $path }}
        </code>
    </div>
    <pre><code>{{ trim(file_get_contents(base_path($path))) }}</code></pre>
</div>
