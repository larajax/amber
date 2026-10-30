@extends('layouts.app')
@section('title', 'Columns')
@section('content')

@include('reference._header', [
    'intro' => 'The <code>Lists</code> widget shows model records as a table, with a column for each definition.',
])

<section class="ui-section" id="building">
    @include('reference._heading', ['id' => 'building', 'title' => 'The lists on this page', 'caption' => '<code>makeColumnLists()</code> builds each list from its own YAML file.'])
    <div class="ui-example">
        @include('reference._method_source', ['method' => 'makeColumnLists'])
    </div>
</section>

<section class="ui-section" id="text">
    @include('reference._heading', ['id' => 'text', 'title' => 'Text', 'caption' => '<code>text</code> with <code>format</code> and <code>default</code>.'])
    @include('reference._widget_example', ['widgets' => [$lists['text']], 'sources' => ['resources/ui/reference/lists/columns/text.yaml']])
</section>

<section class="ui-section" id="number">
    @include('reference._heading', ['id' => 'number', 'title' => 'Number', 'caption' => '<code>number</code> with <code>format</code> and <code>relationCount</code>.'])
    @include('reference._widget_example', ['widgets' => [$lists['number']], 'sources' => ['resources/ui/reference/lists/columns/number.yaml']])
</section>

<section class="ui-section" id="switch">
    @include('reference._heading', ['id' => 'switch', 'title' => 'Switch', 'caption' => '<code>switch</code> with <code>options</code> naming its two labels.'])
    @include('reference._widget_example', ['widgets' => [$lists['switch']], 'sources' => ['resources/ui/reference/lists/columns/switch.yaml']])
</section>

<section class="ui-section" id="colorpicker">
    @include('reference._heading', ['id' => 'colorpicker', 'title' => 'Color picker', 'caption' => '<code>colorpicker</code> showing a hex color as a swatch.'])
    @include('reference._widget_example', ['widgets' => [$lists['colorpicker']], 'sources' => ['resources/ui/reference/lists/columns/colorpicker.yaml']])
</section>

<section class="ui-section" id="selectable">
    @include('reference._heading', ['id' => 'selectable', 'title' => 'Selectable', 'caption' => '<code>selectable</code> with colors, icons, model options and <code>optionsPreset</code>.'])
    @include('reference._widget_example', ['widgets' => [$lists['selectable']], 'sources' => ['resources/ui/reference/lists/columns/selectable.yaml']])
</section>

<section class="ui-section" id="dates">
    @include('reference._heading', ['id' => 'dates', 'title' => 'Date and time', 'caption' => '<code>datetime</code>, <code>date</code> and <code>time</code> with <code>format</code>.'])
    @include('reference._widget_example', ['widgets' => [$lists['dates']], 'sources' => ['resources/ui/reference/lists/columns/dates.yaml']])
</section>

<section class="ui-section" id="relative">
    @include('reference._heading', ['id' => 'relative', 'title' => 'Relative time', 'caption' => '<code>timetense</code> and <code>timesince</code>, the second through <code>valueFrom</code>.'])
    @include('reference._widget_example', ['widgets' => [$lists['relative']], 'sources' => ['resources/ui/reference/lists/columns/relative.yaml']])
</section>

<section class="ui-section" id="summary">
    @include('reference._heading', ['id' => 'summary', 'title' => 'Summary', 'caption' => '<code>summary</code> with <code>limitWords</code> and <code>endChars</code>.'])
    @include('reference._widget_example', ['widgets' => [$lists['summary']], 'sources' => ['resources/ui/reference/lists/columns/summary.yaml']])
</section>

<section class="ui-section" id="image">
    @include('reference._heading', ['id' => 'image', 'title' => 'Image', 'caption' => '<code>image</code> with <code>width</code>, <code>height</code> and resize <code>options</code>.'])
    @include('reference._widget_example', ['widgets' => [$lists['image']], 'sources' => ['resources/ui/reference/lists/columns/image.yaml']])
</section>

<section class="ui-section" id="files">
    @include('reference._heading', ['id' => 'files', 'title' => 'Files', 'caption' => '<code>image</code> and <code>file</code> on attachMany relations, with <code>limit</code>.'])
    @include('reference._widget_example', ['widgets' => [$lists['files']], 'sources' => ['resources/ui/reference/lists/columns/files.yaml']])
</section>

<section class="ui-section" id="linkage">
    @include('reference._heading', ['id' => 'linkage', 'title' => 'Linkage', 'caption' => '<code>linkage</code> with <code>linkUrl</code>, <code>linkText</code> and <code>attributes</code>.'])
    @include('reference._widget_example', ['widgets' => [$lists['linkage']], 'sources' => ['resources/ui/reference/lists/columns/linkage.yaml']])
</section>

<section class="ui-section" id="partial">
    @include('reference._heading', ['id' => 'partial', 'title' => 'Partial', 'caption' => '<code>partial</code> with <code>path</code> and <code>relationWith</code>.'])
    @include('reference._widget_example', ['widgets' => [$lists['partial']], 'sources' => [
        'resources/ui/reference/lists/columns/partial.yaml',
        'resources/views/reference/lists/columns/_column_tags.php',
    ]])
</section>

<section class="ui-section" id="relations">
    @include('reference._heading', ['id' => 'relations', 'title' => 'Values from relations and SQL', 'caption' => '<code>relation</code> with <code>select</code>, a bracket name and raw SQL.'])
    @include('reference._widget_example', ['widgets' => [$lists['relations']], 'sources' => ['resources/ui/reference/lists/columns/relations.yaml']])
</section>

<section class="ui-section" id="headers">
    @include('reference._heading', ['id' => 'headers', 'title' => 'Headers and cells', 'caption' => '<code>shortLabel</code>, <code>tooltip</code>, <code>width</code>, <code>align</code> and CSS classes.'])
    @include('reference._widget_example', ['widgets' => [$lists['headers']], 'sources' => ['resources/ui/reference/lists/columns/headers.yaml']])
</section>

@endsection
