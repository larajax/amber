{{-- Renders one example form above the YAML file that defines it, so the snippet is always the configuration that produced the fields --}}
<div class="ui-example is-stacked reference-form-example">
    <div class="ui-example-preview">
        {{ $exampleForms[$name] }}
    </div>

    @include('reference._source', ['path' => "resources/ui/reference/{$exampleFolder}/{$name}.yaml"])
</div>
