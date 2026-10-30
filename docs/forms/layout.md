---
subtitle: Tabs, spans, sections and the horizontal, survey and preview modes.
demo: /reference/forms/layout
---
# Layout

A form places its fields in up to three sections: the fields outside any tabs, the primary tabs and the
secondary tabs. Within a section, `span` puts each field on a side or across the width, and a few
properties shape how each field shows its label, comment and input. Form options switch the whole form
to a horizontal, survey or preview layout.

## Tabs {#tabs}

Fields under `tabs` are grouped into tabs by their `tab` property, in the order the tabs first appear.
Fields with no `tab` land in `defaultTab`, which is `Misc` unless you set it. `icons` puts an icon beside
a tab title, keyed by the tab name. Fields under the top level `fields` sit above the tabs and stay in
view whichever tab is open. One YAML file can hold `fields`, `tabs` and `secondaryTabs` together.

<<< @/../resources/ui/reference/forms/layout/tabs.yaml

The open tab is reflected in the URL hash, so a link or a reload opens the same tab. A tab whose fields
are all hidden, by `hidden` or by a [trigger](/forms/behaviour#trigger-checked), is hidden too.
[Tab options](#tab-options) lists everything a tab section takes.

## Lazy tabs {#lazy-tabs}

A tab named in `lazy` renders a loading indicator, then fetches its fields from the form's
`onLazyLoadTab` handler the first time it opens. It suits tabs holding heavy widgets that most visits
never look at. The tab that opens first is always rendered straight away. Until a lazy tab opens, its
fields are not on the page, so saving the form leaves their values as they were.

<<< @/../resources/ui/reference/forms/layout/lazy-tabs.yaml

## Secondary tabs {#secondary-tabs}

`secondaryTabs` renders a second tab set below the primary tabs, often for large content fields such as
a body or description. It takes the same options as `tabs`. `activeTab` picks the tab that opens first,
by name or by position counted from 1.

<<< @/../resources/ui/reference/forms/layout/secondary-tabs.yaml

## Spans {#spans}

`span` places a field on the `left` or `right` half, or across the `full` width, which is the default. A
left field followed by a right field share a row. `auto` takes the right half after a left field, and
the left half otherwise, so a run of `auto` fields fills the rows in pairs. On narrow screens the halves
stack.

<<< @/../resources/ui/reference/forms/layout/spans.yaml

`adaptive` makes a field fill its tab, for a large editor such as the
[rich editor](/form-widgets/rich-editor), and stretches the tab to the height of the page.

## Rows {#row-span}

Fields with `span: row` that follow one another share one grid row, each sized by the column classes in
its `spanClass`. Prefixed classes such as `md:col-6` apply from that screen width up, so the fields stack
on a phone.

<<< @/../resources/ui/reference/forms/layout/row-span.yaml

## Collapsible sections {#sections}

A [section](/forms/fields#section) field with the container attribute `data-field-collapsible` starts
collapsed. Clicking the section shows or hides every field after it, up to the next section. The fields
stay in the form while they are hidden, so they still save.

<<< @/../resources/ui/reference/forms/layout/sections.yaml

## Comments {#comments}

`comment` sits below a field and `commentAbove` between its label and the input. Both are escaped, unless
`commentHtml` is on, which renders them as HTML. Labels, comments and placeholders pass through
Laravel's `__()`, so they can be translation keys.

<<< @/../resources/ui/reference/forms/layout/comments.yaml

## Tooltips {#tooltips}

`tooltip` adds an icon beside the label that shows a hint on hover. As a string it is the hint text. As
an array it takes `title`, the text, `icon`, an icon class in place of the default info icon,
`placement`, which is `top` unless set to `right`, `bottom` or `left`, and `isHtml`, which renders the
title as HTML.

<<< @/../resources/ui/reference/forms/layout/tooltips.yaml

## Input attributes {#attributes}

`attributes` adds HTML attributes to the input itself, here a `pattern`, `maxlength` and `title` that the
browser checks before the form is sent. A `maxlength` or `pattern` you set replaces the one a text or
number field adds by default.

<<< @/../resources/ui/reference/forms/layout/attributes.yaml

## Container class and attributes {#css-class}

`cssClass` adds a class to the element that wraps the field, its label and its comment, which is the
place to style one field differently. `containerAttributes` adds HTML attributes to the same element,
such as a `data-` attribute for your own scripts.

<<< @/../resources/ui/reference/forms/layout/css-class.yaml

The demo app's stylesheet draws the highlight from the class:

<<< @/../public/css/amber.css#docs-forms-highlight{css}

## Horizontal mode {#horizontal}

The form option `horizontalMode` puts each label in a column beside its field, with the comment under
the field. It belongs to the widget rather than the YAML, so you set it on the form with
`$form->horizontalMode = true`, or pass `horizontalMode: true` to `Form::make()`.

<<< @/../resources/ui/reference/forms/layout/horizontal.yaml

The page action builds this example and the next, then switches their modes on:

<<< @/../app/Controllers/Reference/FormsController.php#docs-layout-action{php}

## Survey mode {#survey}

The form option `surveyMode` renders the tabs one after another, each under a heading, so the whole form
reads top to bottom with nothing hidden behind a tab. You set it like horizontal mode, with
`$form->surveyMode = true` or `surveyMode: true` passed to `Form::make()`.

<<< @/../resources/ui/reference/forms/layout/survey.yaml

## Read-only fields {#read-only}

`readOnly` stops a field from being changed while still posting its value, so saving the form keeps it.
Text inputs get the `readonly` attribute, checkboxes, switches and radio lists ignore clicks, Select All
and Select None leave a checkbox list alone, and a dropdown shows its value as text beside a hidden
input that posts it.

<<< @/../resources/ui/reference/forms/layout/read-only.yaml

## Disabled fields {#disabled}

`disabled` locks a field and leaves it out of the saved data, even if a value is posted for it, so the
saved value stays as it was. `hidden` does the same without rendering the field at all.

<<< @/../resources/ui/reference/forms/layout/disabled.yaml

## Preview mode {#preview}

The form option `previewMode` shows every field as plain, uneditable text, for a page that only displays
a record, and passes the mode on to the form widgets in it. This form is built with `Form::make(..., previewMode: true)`. On the demo page it also has an `arrayName`
of its own, so its fields never collide with the editable examples, and Save leaves it out.

<<< @/../resources/ui/reference/forms/layout/preview.yaml

<<< @/../app/Controllers/Reference/FormsController/StandaloneForms.php

## Tab options {#tab-options}

`tabs` and `secondaryTabs` take these options beside their `fields`.

Option | Description
------------- | -------------
**defaultTab** | the tab for fields with no `tab`. Default: `Misc`.
**activeTab** | the tab that opens first, by name or by position counted from 1. Default: the first tab.
**icons** | icon classes for the tabs, keyed by tab name.
**lazy** | the names of tabs to load the first time they open.
**stretch** | makes the tabs fill the height of the page. Default: the secondary tabs stretch when there are any, otherwise the primary tabs.
**linkable** | reflects the open tab in the URL hash, so a link can open it. Default: `true`.
**cssClass** | a class on the tab container.
**paneCssClass** | a class on the tab panes, as a string for every pane, or a list keyed by tab name or position.
**identifiers** | ids for the tabs, keyed by tab name. A tab given `notes` gets the id `tab-notes` and its pane `pane-notes`, for scripts that show or hide it.

## Form options {#form-options}

The layout options of the form widget, set with `Form::make()` or on the widget before it renders. The
widget's other options are covered in [Building a Form Page](/guides/forms).

Option | Description
------------- | -------------
**horizontalMode** | puts each label in a column beside its field. Default: `false`.
**surveyMode** | renders the tabs one after another under headings. Default: `false`.
**previewMode** | shows every field as uneditable text. Default: `false`.
