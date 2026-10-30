---
subtitle: Show model records as a table, one column per definition.
demo: /reference/lists/columns
---
# Columns

The `Lists` widget shows the records of a model as a table, with a column for each entry in its
`columns` definition, given as a YAML file path or an array. The column `type` decides how a value
displays, and each type takes options of its own. Adding a list to a page, with the routes and
handlers it needs, is covered in [Building a List Page](/guides/lists).

## The lists on this page {#building}

Each example on this page is a small list of the seeded posts or samples, built in a loop from its own
YAML file. `customPageName: false` keeps the page number out of the address, so every list on the page
pages on its own.

<<< @/../app/Controllers/Reference/ListsController/ColumnsExamples.php

## Text {#text}

`text` is the default type, used when a column sets no `type`. It escapes the value, and an array of
plain values shows its items separated by commas, like the keywords here. `format` is a `sprintf`
pattern for the value, so `/blog/%s` puts the slug inside an address, and `default` shows in place of
an empty value.

<<< @/../resources/ui/reference/lists/columns/text.yaml

## Number {#number}

`number` takes `format` like a text column and aligns its cells to the right. Given a `relation` and
`relationCount: true`, it shows the number of related records, counted in the list query, so the
column still sorts.

<<< @/../resources/ui/reference/lists/columns/number.yaml

## Switch {#switch}

`switch` shows a boolean as a tick or a cross. Hovering the icon shows a label for the value: `options`
gives the labels for false and true, in that order, and without it they are Yes and No.

<<< @/../resources/ui/reference/lists/columns/switch.yaml

## Color picker {#colorpicker}

`colorpicker` shows a hex color as a swatch, such as a color saved by the
[color picker](/form-widgets/color-picker) form widget.

<<< @/../resources/ui/reference/lists/columns/colorpicker.yaml

## Selectable {#selectable}

`selectable` shows the label of the option a value picked, and takes its options the same ways a
[dropdown field](/forms/fields#dropdown-model-options) does:

- `options` lists them in the YAML. An option written as `[label, color]` shows a colored dot before
  the label, and `[label, icon]` shows the icon, or an image when the second item is a path.
- With no `options`, the column calls the model method named after it, `get{Column}Options()`, such as
  `getCountryOptions()`. The method is called on each record, so the states follow the country of
  their own row.
- `optionsMethod` names another model method, or a static method as `Class::method`.
- `optionsPreset` uses a built-in set, such as `timezones` (see
  [option presets](/forms/fields#dropdown-preset)).

An array value, like the features of a sample, shows the label of every picked option.

<<< @/../resources/ui/reference/lists/columns/selectable.yaml

<<< @/../app/Models/Sample.php#docs-dependent-options{php}

## Date and time {#dates}

`datetime`, `date` and `time` show a date value. Without a `format`, a `datetime` reads like
*Sat, Mar 14, 2026 9:30 AM*, a `date` like *Mar 14, 2026* and a `time` like *9:30 AM*. `format` takes a
[PHP date format](https://www.php.net/manual/en/datetime.format.php) in place of the default.

A `datetime` converts the value to the viewer's timezone, set with
[`Ui::resolveTimezoneUsing()`](/extending/seams#timezone), while `date` and `time` show the value as
stored. `useTimezone` changes either one: `true` converts a `date` or `time`, and `false` keeps a
`datetime` as stored.

<<< @/../resources/ui/reference/lists/columns/dates.yaml

## Relative time {#relative}

`timetense` names the day before the time, such as *Today at 12:49*, *Yesterday at 18:46* or
*14 Mar 2026 at 09:30*, and `timesince` counts the time passed, such as *3 days ago*. Both columns here
show the publish date: the second has a name of its own and reads the date through `valueFrom`. A
`timetense` column converts to the viewer's timezone like a `datetime`, and `useTimezone` changes
either type.

<<< @/../resources/ui/reference/lists/columns/relative.yaml

## Summary {#summary}

`summary` strips the HTML from a value and shortens it to 40 characters, or to `limitChars`.
`limitWords` keeps a number of whole words instead. `endChars` replaces the `...` added to a shortened
value, and a value short enough to show in full gets nothing added.

<<< @/../resources/ui/reference/lists/columns/summary.yaml

## Image {#image}

`image` shows a thumbnail of an attached image, from an `attachOne` or `attachMany` relation, or of an
image path or address held in the column. `width` and `height` size the thumbnail, 68 by 68 pixels by
default, and `options` passes resize options to it, such as `mode: crop`. An attachment has no
database column to sort by, so the column sets `sortable: false`.

<<< @/../resources/ui/reference/lists/columns/image.yaml

## Files {#files}

On an `attachMany` relation, an `image` column shows up to `limit` thumbnails, 3 by default, as small
squares when no size is set. `file` shows an icon for the type of each attached file, with the file
name as its tip, also up to `limit`.

A `file` column with `clickable: false` links each icon to its file. In a clickable cell, a click
belongs to the row link, so the icons are not links.

<<< @/../resources/ui/reference/lists/columns/files.yaml

## Linkage {#linkage}

`linkage` shows the value as a link to `linkUrl`, where `:name` takes that attribute of the record,
such as `:category_id`. A path becomes an address through
[`Ui::url()`](/extending/seams#widget-paths), while a full address is left alone, and a
[scheme link](/extending/seams#scheme-links) that points nowhere shows as plain text. `linkText`
replaces the link text, so a
column with no value of its own, like Edit here, still shows a link. `attributes` adds HTML attributes
to the link, such as `target: _blank`.

Without `linkUrl`, the value is the address, and a value that is an array of `[url, text]`, such as one
returned by an accessor, sets both. Only relative addresses and the `http`, `https`, `ftp`, `ftps`,
`mailto`, `tel` and `sms` schemes become links, and any other value shows as text. A linkage cell keeps
its own link, so it is never part of the row link.

<<< @/../resources/ui/reference/lists/columns/linkage.yaml

## Partial {#partial}

`partial` renders a file of your own in each cell, which receives the `$record`, its `$value` and the
`$column`. `path` is a file path, where `~` stands for the app root, or a namespaced view such as
`admin::lists.tags`. `relationWith` eager loads a relation with the list query, here the tags, so the
partial does not run a query for every row. Give a partial with links of its own `clickable: false`.

<<< @/../resources/ui/reference/lists/columns/partial.yaml

<<< @/../resources/views/reference/lists/columns/_column_tags.php

## Values from relations and SQL {#relations}

A column with a `relation` and a `select` reads a column of the related record inside the list query,
so it can be searched like any other. On a relation with many records, such as the tags, the values
are joined with commas. A column name in brackets, such as `category[slug]`, reads an attribute of the
related record, or a key of an array value, after the query has run, so it can be neither searched nor
sorted.

`select` without a relation is raw SQL for the value, where `@` stands for the table name, as in
`length(@title)`.

Two more options pick the value:

- `valueFrom` reads another attribute in place of the column name. With a `relation`, it reads that
  attribute of the related record and eager loads the relation.
- `displayFrom` shows another attribute, such as an accessor, while searching and sorting still use
  the column's own value.

<<< @/../resources/ui/reference/lists/columns/relations.yaml

## Headers and cells {#headers}

`shortLabel` replaces the `label` in the header, while the list setup keeps showing the full label.
`tooltip` adds an icon after the label with a tip, given as text or as an array with a `title`, an
`icon` (an info icon by default), a `placement` (`top`, `right`, `bottom` or `left`, default `top`) and
`isHtml` to show the title as HTML.

`width` sets the column width, as a percentage or in pixels, and a single column without one takes the
space left over. `align` aligns the cells to the `left`, `right` or `center`. `cssClass` adds a class
to the cells and `headCssClass` adds one to the header.

<<< @/../resources/ui/reference/lists/columns/headers.yaml

## Column properties {#column-properties}

Every column takes these properties. A column given as a string, such as `title: Title`, is a text
column with that label.

Property | Description
------------- | -------------
**label** | the column header. Default: the column name in StudlyCase.
**shortLabel** | a shorter label for the header, in place of `label`.
**type** | how the value displays, one of the [column types](#column-types). Default: `text`.
**default** | shown in place of an empty value.
**searchable** | includes the column in the list search. Default: `false`.
**sortable** | lets a click on the header sort the list. Default: `true`.
**sortableDefault** | sorts the list by this column until the visitor picks another, as `asc` or `desc`, when the list sets no `defaultSort`. Use it on one column only.
**invisible** | starts the column unchecked in the [list setup](/lists/options#setup), so it is hidden until the visitor shows it. Default: `false`.
**hidden** | defines the column without ever showing it, not even in the list setup. Default: `false`.
**clickable** | `false` leaves the cell out of the row link. Default: `true`.
**select** | raw SQL for the value, or with `relation` a column of the related table. `@` stands for the table name.
**valueFrom** | reads the value from another attribute, or with `relation` from an attribute of the related record.
**displayFrom** | shows another attribute, while searching and sorting use the column's own value.
**relation** | a relation of the model to read the value from, with `select` or `valueFrom`.
**relationCount** | shows the number of related records. Needs `relation`. Default: `false`.
**relationWith** | a relation, or an array of them, eager loaded with the list query. Dot notation loads nested relations.
**format** | a format for the value, used by the [text, number and date types](#type-options).
**width** | the column width, such as `10%` or `50px`.
**align** | aligns the cells: `left`, `right` or `center`.
**cssClass** | a class added to the cells.
**headCssClass** | a class added to the header cell.
**tooltip** | an icon with a tip after the header label, as text or an array with `title`, `icon`, `placement` and `isHtml`.
**permissions** | gate abilities the visitor needs to see the column, as a string or an array where any one is enough, checked with Laravel's `Gate`.
**order** | a number for the column's position. Default: 100, 200 and so on, in the order of the definitions.
**after** | places the column after the named column.
**before** | places the column before the named column.

## Column types {#column-types}

Type | Shows | Options
------------- | ------------- | -------------
**text** | the value, escaped. An array of plain values is joined with commas. | `format`
**number** | the value like `text`, aligned to the right. | `format`
**switch** | a tick or a cross for a boolean, with a label on hover. | `options`
**colorpicker** | a swatch of a hex color. |
**selectable** | the label of the picked option, or of each picked option in an array. | `options`, `optionsMethod`, `optionsPreset`
**datetime** | a date and time, in the viewer's timezone. | `format`, `useTimezone`
**date** | a date, as stored. | `format`, `useTimezone`
**time** | a time, as stored. | `format`, `useTimezone`
**timetense** | the day and time, such as *Today at 12:49*, in the viewer's timezone. | `useTimezone`
**timesince** | the time passed, such as *3 days ago*. | `useTimezone`
**summary** | the text without HTML, shortened. | `limitChars`, `limitWords`, `endChars`
**image** | thumbnails of attached images, or of image paths and addresses. | `width`, `height`, `limit`, `options`
**file** | an icon for each attached file. | `limit`
**linkage** | the value as a link. | `linkUrl`, `linkText`, `attributes`
**partial** | a file of your own. | `path`

The types are fixed: any other `type` throws an error that names it. For a cell no type covers, use a
`partial` column.

### Type options {#type-options}

Option | Description
------------- | -------------
**format** | `text` and `number`: a `sprintf` pattern for the value, such as `'%d views'`. `datetime`, `date` and `time`: a PHP date format.
**useTimezone** | the date types: `true` converts the value to the viewer's timezone and `false` shows it as stored. Default: `true` for `datetime` and `timetense`, `false` for the others.
**options** | `switch`: the labels for false and true. Default: Yes and No. `selectable`: the options, as `value: label`, `value: [label, color]` or `value: [label, icon]`. `image`: resize options for the thumbnails, such as `mode: crop`.
**optionsMethod** | `selectable`: a model method that returns the options, or a static method as `Class::method`. Default: `get{Column}Options()`, called on each record.
**optionsPreset** | `selectable`: a built-in option set, such as `timezones`.
**limitChars** | `summary`: the number of characters to keep. Default: `40`.
**limitWords** | `summary`: the number of words to keep, in place of `limitChars`.
**endChars** | `summary`: added to a shortened value. Default: `...`.
**width** | `image`: the thumbnail width in pixels. Default: `68`.
**height** | `image`: the thumbnail height in pixels. Default: `68`.
**limit** | `image` and `file`: the most files shown in a cell. Default: `3`.
**linkUrl** | `linkage`: the address, where `:name` takes an attribute of the record.
**linkText** | `linkage`: the link text, in place of the value.
**attributes** | `linkage`: HTML attributes for the link, such as `target: _blank`.
**path** | `partial`: the file to render, as a `~/` path or a namespaced view.
