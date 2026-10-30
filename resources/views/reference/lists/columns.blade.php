@extends('layouts.app')
@section('title', 'Columns')
@section('content')

@include('reference._header', [
    'intro' => 'The <code>Lists</code> widget shows the records of a model as a table, with a column for each entry in its <code>columns</code> definition. The column <code>type</code> decides how the value displays, and each type takes options of its own.',
])

<section class="ui-section" id="building">
    <h2>
        The lists on this page
    </h2>
    <p class="fg-3">
        Each example is a small list of the seeded posts or samples, built in a loop from its own YAML file. <code>customPageName: false</code> keeps the page number out of the address, so every list pages on its own.
    </p>
    <div class="ui-example">
        @include('reference._method_source', ['method' => 'makeColumnLists'])
    </div>
</section>

<section class="ui-section" id="text">
    <h2>
        Text
    </h2>
    <p class="fg-3">
        <code>text</code> is the default type and escapes the value. <code>format</code> is a <code>sprintf</code> pattern for the value, an array value shows its items separated by commas, and <code>default</code> shows when the value is empty.
    </p>
    @include('reference._widget_example', ['widgets' => [$lists['text']], 'sources' => ['resources/ui/reference/lists/columns/text.yaml']])
</section>

<section class="ui-section" id="number">
    <h2>
        Number
    </h2>
    <p class="fg-3">
        <code>number</code> takes <code>format</code> like a text column and aligns right. With a <code>relation</code> and <code>relationCount: true</code> it counts the related records in the list query.
    </p>
    @include('reference._widget_example', ['widgets' => [$lists['number']], 'sources' => ['resources/ui/reference/lists/columns/number.yaml']])
</section>

<section class="ui-section" id="switch">
    <h2>
        Switch
    </h2>
    <p class="fg-3">
        <code>switch</code> shows a boolean as a tick or a cross. Hover it for its label, which <code>options</code> sets for the false and true values and which is Yes and No without them.
    </p>
    @include('reference._widget_example', ['widgets' => [$lists['switch']], 'sources' => ['resources/ui/reference/lists/columns/switch.yaml']])
</section>

<section class="ui-section" id="colorpicker">
    <h2>
        Color picker
    </h2>
    <p class="fg-3">
        <code>colorpicker</code> shows a hex color, such as one saved by the color picker form widget, as a swatch.
    </p>
    @include('reference._widget_example', ['widgets' => [$lists['colorpicker']], 'sources' => ['resources/ui/reference/lists/columns/colorpicker.yaml']])
</section>

<section class="ui-section" id="selectable">
    <h2>
        Selectable
    </h2>
    <p class="fg-3">
        <code>selectable</code> shows the label of the option a value picked, taking options the way dropdown fields do. An option given as a label and a color shows a dot, and a label and an icon shows the icon. Without <code>options</code> they come from the model method named after the column, such as <code>getCountryOptions()</code>, called on each record, so the states follow the country of their row. <code>optionsPreset</code> uses a built-in set, and an array value shows every label.
    </p>
    @include('reference._widget_example', ['widgets' => [$lists['selectable']], 'sources' => ['resources/ui/reference/lists/columns/selectable.yaml']])
</section>

<section class="ui-section" id="dates">
    <h2>
        Date and time
    </h2>
    <p class="fg-3">
        <code>datetime</code>, <code>date</code> and <code>time</code> show a date value, and <code>format</code> takes a PHP date format in place of the default. A <code>datetime</code> converts to the viewer's timezone, while <code>date</code> and <code>time</code> show the stored value, and <code>useTimezone</code> changes either.
    </p>
    @include('reference._widget_example', ['widgets' => [$lists['dates']], 'sources' => ['resources/ui/reference/lists/columns/dates.yaml']])
</section>

<section class="ui-section" id="relative">
    <h2>
        Relative time
    </h2>
    <p class="fg-3">
        <code>timetense</code> names the day, such as Yesterday at 18:46, and <code>timesince</code> counts the time passed. Both columns here show the publish date, the second through <code>valueFrom</code>.
    </p>
    @include('reference._widget_example', ['widgets' => [$lists['relative']], 'sources' => ['resources/ui/reference/lists/columns/relative.yaml']])
</section>

<section class="ui-section" id="summary">
    <h2>
        Summary
    </h2>
    <p class="fg-3">
        <code>summary</code> strips HTML and shortens the text to 40 characters, or to <code>limitChars</code>, or to a number of whole words with <code>limitWords</code>. <code>endChars</code> replaces the three dots added to a shortened value.
    </p>
    @include('reference._widget_example', ['widgets' => [$lists['summary']], 'sources' => ['resources/ui/reference/lists/columns/summary.yaml']])
</section>

<section class="ui-section" id="image">
    <h2>
        Image
    </h2>
    <p class="fg-3">
        <code>image</code> shows a thumbnail of an attached image, or of an image path or address. <code>width</code> and <code>height</code> size the thumbnail and <code>options</code> passes resize options such as <code>mode: crop</code>. An attachment has no database column to sort by, so the column sets <code>sortable: false</code>.
    </p>
    @include('reference._widget_example', ['widgets' => [$lists['image']], 'sources' => ['resources/ui/reference/lists/columns/image.yaml']])
</section>

<section class="ui-section" id="files">
    <h2>
        Files
    </h2>
    <p class="fg-3">
        An <code>image</code> column on an attachMany relation shows up to <code>limit</code> thumbnails, small squares when no size is set. <code>file</code> shows an icon for the type of each attached file. With <code>clickable: false</code> the icons link to the files, while a clickable cell leaves the click to the row link.
    </p>
    @include('reference._widget_example', ['widgets' => [$lists['files']], 'sources' => ['resources/ui/reference/lists/columns/files.yaml']])
</section>

<section class="ui-section" id="linkage">
    <h2>
        Linkage
    </h2>
    <p class="fg-3">
        <code>linkage</code> links the value to <code>linkUrl</code>, where <code>:name</code> takes an attribute of the record. <code>linkText</code> replaces the link text, so a column with no value of its own still shows a link, and <code>attributes</code> adds attributes such as <code>target</code>. Linkage cells are never part of the row link.
    </p>
    @include('reference._widget_example', ['widgets' => [$lists['linkage']], 'sources' => ['resources/ui/reference/lists/columns/linkage.yaml']])
</section>

<section class="ui-section" id="partial">
    <h2>
        Partial
    </h2>
    <p class="fg-3">
        <code>partial</code> renders the file in <code>path</code> with the <code>$record</code>, <code>$value</code> and <code>$column</code>. <code>relationWith</code> eager loads the tags with the list query, so the partial does not query once per row.
    </p>
    @include('reference._widget_example', ['widgets' => [$lists['partial']], 'sources' => [
        'resources/ui/reference/lists/columns/partial.yaml',
        'resources/views/reference/lists/columns/_column_tags.php',
    ]])
</section>

<section class="ui-section" id="relations">
    <h2>
        Values from relations and SQL
    </h2>
    <p class="fg-3">
        A <code>relation</code> with <code>select</code> reads a column of the related record in the list query, joining the values of a many relation with commas. A name such as <code>category[slug]</code> reads an attribute of a relation without joining it, so it cannot sort. <code>select</code> without a relation is raw SQL, where <code>@</code> stands for the table.
    </p>
    @include('reference._widget_example', ['widgets' => [$lists['relations']], 'sources' => ['resources/ui/reference/lists/columns/relations.yaml']])
</section>

<section class="ui-section" id="headers">
    <h2>
        Headers and cells
    </h2>
    <p class="fg-3">
        <code>shortLabel</code> replaces the label in the header, and <code>tooltip</code> adds an icon there with a tip, given as text or as a title with an <code>icon</code>, a <code>placement</code> and <code>isHtml</code>. <code>width</code> sets the column width, <code>align</code> aligns the cells, and <code>cssClass</code> and <code>headCssClass</code> add classes to the cells and the header.
    </p>
    @include('reference._widget_example', ['widgets' => [$lists['headers']], 'sources' => ['resources/ui/reference/lists/columns/headers.yaml']])
</section>

@endsection
