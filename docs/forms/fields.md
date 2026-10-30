---
subtitle: Every built-in field type, and the properties all fields share.
demo: /reference/forms/fields
---
# Field Types

The form widget renders one field for each definition in its YAML, and the field's `type` picks how it
renders. This page covers the built-in types and the options each one takes, followed by the
[properties every field shares](#field-properties). Form widgets, such as the
[color picker](/form-widgets/color-picker), have pages of their own, and
[Building a Form Page](/guides/forms) shows how to put a form on a page and save it.

Each definition is keyed by the field name, which is the model attribute the field reads and saves.
Where the fields sit, in tabs or side by side, is covered in [Layout](/forms/layout).

## Text {#text}

`text` is the default type, so a field with no `type` renders a single line input. `placeholder` shows
while the field is empty. The input is limited to 255 characters unless `attributes` sets its own
`maxlength`.

<<< @/../resources/ui/reference/forms/fields/text.yaml

## Email {#email}

`email` renders an email input, which brings up the email keyboard on phones and lets the browser check
the format. The browser check is a convenience, so validate the value on the model as well, here with
the `email` rule (see [Validation](/forms/behaviour#validation)).

<<< @/../resources/ui/reference/forms/fields/email.yaml

## Number {#number}

`number` passes `min`, `max` and `step` to the input. With no `step`, any decimal is accepted and the
spinner is hidden. The value is saved as a number, and an empty field saves as `null`, so a nullable
column is cleared rather than set to zero.

<<< @/../resources/ui/reference/forms/fields/number.yaml

## Password {#password}

`password` always renders empty, so a stored password is never sent back to the browser. When the form
saves through the `FormModelSaver` trait (see [Building a Form Page](/guides/forms)), a field name that
starts with an underscore is set on the model but removed before the record is written. This example
uses one, so its value is never stored.

<<< @/../resources/ui/reference/forms/fields/password.yaml

## Textarea {#textarea}

`textarea` renders a multi-line input. In preview mode it shows the text with its line breaks.

<<< @/../resources/ui/reference/forms/fields/textarea.yaml

## Textarea sizes {#textarea-sizes}

`size` sets the height of a textarea: `tiny`, `small`, `large` (the default), `huge` or `giant`. The
same sizes apply to the editor widgets, such as the [code editor](/form-widgets/code-editor).

<<< @/../resources/ui/reference/forms/fields/textarea-sizes.yaml

## Dropdown {#dropdown}

`dropdown` renders a searchable select from its `options`, keyed by the value that is saved.
`emptyOption` adds a first option with an empty value, so picking it clears the field. `placeholder`
prompts while the field is empty and adds a clear button in place of an empty option. `showSearch: false`
removes the search box, which suits a short list.

<<< @/../resources/ui/reference/forms/fields/dropdown.yaml

A read-only dropdown, or one in preview mode, shows the label of its value as text instead of a select.

## Options from the model {#dropdown-model-options}

With no `options`, the field calls `get{Field}Options` on the model, where `{Field}` is the field name in
studly case, so the `country` field calls `getCountryOptions`. `optionsMethod` names any other model
method, or a static method written as `Class::method`. An option with `optgroup: true` and `children`
becomes an option group, as `getCategoryOptions` returns here. Radio lists, checkbox lists and balloon
selectors take their options the same ways; [Option lists](#option-lists) has the full order and the
arguments each method receives.

<<< @/../resources/ui/reference/forms/fields/dropdown-model-options.yaml

<<< @/../app/Models/Sample.php#docs-country-options{php}

<<< @/../app/Models/Sample.php#docs-category-options{php}

<<< @/../app/Models/Sample.php#docs-list-currencies{php}

## Option presets {#dropdown-preset}

`optionsPreset` fills the options from a built-in list: `icons@phosphor` and `icons@bootstrap` list
every Phosphor and Bootstrap icon with its preview, `locales` lists language codes with their native
names, `flags` and `flags@short` list the same codes with a flag, and `timezones` lists every timezone
with its UTC offset. `icons` lists the classes of the `oc-icon-*` icon font. You can register lists of
your own, see [Presets](#option-presets).

<<< @/../resources/ui/reference/forms/fields/dropdown-preset.yaml

## Option colors and icons {#option-styles}

An option written as `[label, value]` shows a status dot when the second value is a hex color, an image
when it is a path (any value with a dot in it), and an icon otherwise. An option can also be detailed, an
array with `label` and any of `icon`, `color`, `image`, `comment`, `disabled` and `children`, written in
the YAML or returned by a model method. Children are indented below their parent. A disabled option cannot be
picked, unless it is the saved value, which stays selectable so that saving the form keeps it.

<<< @/../resources/ui/reference/forms/fields/option-styles.yaml

The `level` field has no options in the YAML, so they come from `getLevelOptions`:

<<< @/../app/Models/Sample.php#docs-level-options{php}

## Radio list {#radio}

`radio` picks one option from a list. `inlineOptions` puts the options side by side. An option written
as `[label, description]` shows the description under its label, and a detailed option can be
`disabled`. A radio list with no options shows its `placeholder` instead.

<<< @/../resources/ui/reference/forms/fields/radio.yaml

## Checkbox {#checkbox}

`checkbox` saves `1` when ticked. A hidden input posts `0` when it is not, so unticking the box is saved
too. The label and comment sit beside the box rather than above it. Cast the attribute to `boolean` on
the model to read it as `true` or `false`.

<<< @/../resources/ui/reference/forms/fields/checkbox.yaml

## Switch {#switch}

`switch` saves like a checkbox, drawn as a switch.

<<< @/../resources/ui/reference/forms/fields/switch.yaml

## Checkbox list {#checkboxlist}

`checkboxlist` ticks any number of options and saves the ticked keys as an array, so the attribute needs
a JSON column with an `array` cast, or a place in `$jsonable`. An empty value is posted when nothing is
ticked, so clearing the list is saved too. `quickselect` adds Select All and Select None buttons, and a
click with Shift held sets every box between it and the previous click to the same state.
`inlineOptions` puts the options on one line.

<<< @/../resources/ui/reference/forms/fields/checkboxlist.yaml

## Nested options {#checkboxlist-nested}

Options with `children` nest below their parent, and each option can carry a `comment`. With
`cumulative`, ticking a parent ticks all of its children too. Unticking the parent leaves them as they
are. Every option, parent or child, saves its own key.

<<< @/../resources/ui/reference/forms/fields/checkboxlist-nested.yaml

## Long lists {#checkboxlist-scrollable}

A list of more than ten options scrolls inside a box, shows the Select All and Select None buttons
unless `quickselect: false` turns them off, and stacks its options even with `inlineOptions`.

<<< @/../resources/ui/reference/forms/fields/checkboxlist-scrollable.yaml

## Balloon selector {#balloon-selector}

`balloon-selector` picks one option from a row of pills and stores it in a hidden input, which suits a
short list of states. Each option is focusable, and Enter or Space picks the focused one.

<<< @/../resources/ui/reference/forms/fields/balloon-selector.yaml

## Clearing a balloon selector {#balloon-allow-empty}

By default a balloon selector always holds one of its options once one is picked. `allowEmpty` lets the
active option be clicked again to clear the value.

<<< @/../resources/ui/reference/forms/fields/balloon-allow-empty.yaml

## Read-only balloon selector {#balloon-read-only}

With `readOnly`, the options leave the tab order and ignore clicks and keys, while the hidden input still
posts the value, so saving the form keeps it. A `disabled` balloon selector looks the same but its value
is left out of the saved data.

<<< @/../resources/ui/reference/forms/fields/balloon-read-only.yaml

## Section {#section}

`section` renders a heading from `label` and a line of text from `comment`, to open a group of fields.
`displayMode: simple` shows the label as plain text instead of a heading. Sections, hints and rulers post
nothing, so their names only need to be unique; starting them with an underscore keeps them clear of
your attribute names. A section can also collapse the fields below it, see
[Collapsible sections](/forms/layout#sections).

<<< @/../resources/ui/reference/forms/fields/section.yaml

## Hint {#hint}

`hint` renders a callout from `label` and `comment`. `mode` sets its style: `info` (the default),
`tip`, which looks like info, `success`, `warning` or `danger`. The comment is read as Markdown, or as
HTML with `commentHtml`.

<<< @/../resources/ui/reference/forms/fields/hint.yaml

## Hint from a partial {#hint-partial}

With `path`, the hint renders a partial inside the callout, so it can show values from the record. The
partial has `$formModel`, `$field` and `$value` in scope. The label and mode still apply, and a comment
renders above the partial. `path` takes a PHP partial, where `~` is the application root, or a namespaced
view such as `shop::hints.stock`.

<<< @/../resources/ui/reference/forms/fields/hint-partial.yaml

<<< @/../resources/views/reference/forms/_hint_record_checklist.php{php}

## Ruler {#ruler}

`ruler` draws a horizontal line between fields, with an optional label above it and a comment below.

<<< @/../resources/ui/reference/forms/fields/ruler.yaml

## Partial {#partial}

`partial` renders a partial from `path`, with `$formModel`, `$field` and `$value` in scope, for anything
the built-in types do not cover. The label and comment still render around it. This one reads the form
model to show which record the form edits.

<<< @/../resources/ui/reference/forms/fields/partial.yaml

<<< @/../resources/views/reference/forms/_field_record_card.php{php}

## A partial with its own input {#partial-ajax}

A partial can render its own input, named with `$field->getName()` and given `$field->getId()`, and the
form then saves it like any other field. This one renders a [custom select](/ui/inputs) with
`data-handler`, so the dropdown loads its options from a handler on the controller as you type. The
handler receives the typed text as `term` and returns a list of `id` and `text` pairs under `results`.

<<< @/../resources/ui/reference/forms/fields/partial-ajax.yaml

<<< @/../resources/views/reference/forms/_field_timezone.php{php}

<<< @/../app/Controllers/Reference/FormsController/FieldsHandlers.php

## Field properties {#field-properties}

Every field takes these properties, where they apply to its type.

Property | Description
------------- | -------------
**label** | the text shown above the field, or beside a checkbox or switch.
**type** | the field type: one of the types on this page, or a form widget such as `colorpicker`. Default: `text`.
**span** | where the field sits: `left`, `right`, `full`, `auto`, `row` or `adaptive`. See [Spans](/forms/layout#spans). Default: `full`.
**spanClass** | the grid column classes of a `row` field, such as `md:col-6`. See [Rows](/forms/layout#row-span).
**size** | the height of a field that has one, such as a textarea: `tiny`, `small`, `large`, `huge` or `giant`. Default: `large`.
**placeholder** | text shown while the field is empty.
**comment** | text below the field. See [Comments](/forms/layout#comments).
**commentAbove** | text between the label and the field.
**commentHtml** | renders the comments as HTML instead of escaping them. Default: `false`.
**tooltip** | an icon beside the label that shows a hint, as text or as an array of `title`, `icon`, `placement` and `isHtml`. See [Tooltips](/forms/layout#tooltips).
**default** | the value of the field on a new record. For option fields, the key of the option to select. See [Default values](/forms/behaviour#defaults).
**defaultFrom** | another attribute of the record to take the default value from.
**valueFrom** | another attribute to read the value from, while the field still posts under its own name.
**tab** | the tab the field belongs to, for fields under `tabs` or `secondaryTabs`. See [Tabs](/forms/layout#tabs).
**order** | a number that sets the position of the field. Default: 100, 200 and so on, in the order the fields are defined.
**cssClass** | a class on the element that wraps the field. See [Container class and attributes](/forms/layout#css-class).
**attributes** | HTML attributes for the input itself. See [Input attributes](/forms/layout#attributes).
**containerAttributes** | HTML attributes for the element that wraps the field.
**stretch** | makes the field fill the height of its container, for a large editor. Default: `false`.
**autoFocus** | puts the cursor in the field when the page loads. Default: `false`.
**readOnly** | stops the field from being changed while still posting its value. See [Read-only fields](/forms/layout#read-only). Default: `false`.
**disabled** | locks the field and leaves it out of the saved data. See [Disabled fields](/forms/layout#disabled). Default: `false`.
**hidden** | does not render the field, and leaves it out of the saved data. Default: `false`.
**required** | shows the required marker beside the label, without checking anything. The model's validation rules set it for you. See [Validation](/forms/behaviour#validation).
**context** | the form contexts the field appears in, such as `create` or `update`. A name such as `reference@create` does the same. See [Contexts](/forms/behaviour#contexts).
**dependsOn** | the fields that redraw this field when they change. See [Dependent fields](/forms/behaviour#dependent-fields).
**changeHandler** | an AJAX handler called when the field changes. See [Change handler](/forms/behaviour#change-handler).
**trigger** | a condition on another field that shows, hides, enables, disables, empties or fills this one in the browser. See [Triggers](/forms/behaviour#trigger-options).
**preset** | fills the field from another one as you type. See [Input presets](/forms/behaviour#presets).
**permissions** | the gate abilities the user needs for the field to appear. With a list, any one of them is enough. When the user has none of them, the field is removed from the form.
**options** | the choices of an option field. See [Option lists](#option-lists).
**path** | the partial of a `partial` or `hint` field.

## Type options {#type-options}

The options each type takes besides the common properties. `text`, `email`, `password`, `checkbox`,
`switch` and `ruler` take only the common properties.

### Number {#number-options}

Option | Description
------------- | -------------
**min** | the lowest value the input accepts.
**max** | the highest value the input accepts.
**step** | the interval between values, and shows the spinner. Default: any decimal, with no spinner.

### Textarea {#textarea-options}

Option | Description
------------- | -------------
**size** | the height: `tiny`, `small`, `large`, `huge` or `giant`. Default: `large`.

### Dropdown {#dropdown-options}

Option | Description
------------- | -------------
**options** | the options, as a list or the name of a model method. See [Option lists](#option-lists).
**optionsMethod** | a model method, or a static `Class::method`, that returns the options.
**optionsPreset** | a built-in list of options, such as `timezones`. See [Presets](#option-presets).
**emptyOption** | the label of a first option with an empty value, which clears the field.
**placeholder** | text shown while the field is empty, with a clear button. It also adds an empty option when there is no `emptyOption`.
**showSearch** | shows a search box above the options. Default: `true`.

### Radio list {#radio-options}

Option | Description
------------- | -------------
**options**, **optionsMethod**, **optionsPreset** | the options, as for a dropdown.
**inlineOptions** | puts the options side by side. Default: `false`.
**placeholder** | text shown when there are no options.

### Checkbox list {#checkboxlist-options}

Option | Description
------------- | -------------
**options**, **optionsMethod**, **optionsPreset** | the options, as for a dropdown.
**inlineOptions** | puts the options side by side, for lists of up to ten. Default: `false`.
**quickselect** | adds Select All and Select None buttons. Default: `false`, or `true` with more than ten options.
**cumulative** | ticking a parent option ticks its children. Default: `false`.
**emptyOption** | text shown when there are no options.
**placeholder** | text shown in preview mode when nothing is ticked.

### Balloon selector {#balloon-selector-options}

Option | Description
------------- | -------------
**options**, **optionsMethod**, **optionsPreset** | the options, as for a dropdown.
**allowEmpty** | clicking the active option again clears the value. Default: `false`.

### Section {#section-options}

Option | Description
------------- | -------------
**displayMode** | `simple` shows the label as plain text instead of a heading.

### Hint {#hint-options}

Option | Description
------------- | -------------
**mode** | the style of the callout: `info`, `tip`, `success`, `warning` or `danger`. Default: `info`.
**path** | a partial to render inside the callout.

### Partial {#partial-options}

Option | Description
------------- | -------------
**path** | the partial to render, a PHP file under `~/` (the application root) or a namespaced view such as `shop::fields.card`.

## Option lists {#option-lists}

The `dropdown`, `radio`, `checkboxlist` and `balloon-selector` types need a list of options, each keyed
by the value that is saved. The field takes the first of these that it finds:

1. `options` as a list in the YAML.
2. `optionsPreset`, a [preset](#option-presets) name.
3. `optionsMethod`, the name of a model method, or a static method written as `Class::method`.
4. `options` as a string, read like `optionsMethod`. `options: preset:timezones` reads a preset.
5. Otherwise, `get{Field}Options` on the model, and failing that `getDropdownOptions`.

The methods receive these arguments, and all of them return the options as an array:

Method | Arguments
------------- | -------------
**a model method named by `optionsMethod` or `options`** | `($value, $fieldName, $data)`
**a static `Class::method`** | `($model, $field)`, the model and the `FormField`
**`get{Field}Options`** | `($value, $data)`
**`getDropdownOptions`** | `($attribute, $value, $data)`, where `$attribute` is the field name

While a form refreshes, such as for a [dependent field](/forms/behaviour#dependent-fields), the model
holds the posted values, so a method can read the other fields from `$this`.

### Option formats {#option-formats}

Each option can be written in any of these ways:

Format | Result
------------- | -------------
**`key: Label`** | a plain option.
**`key: [Label, Second]`** | in a dropdown, a status dot when `Second` is a hex color, an image when it is a path, and an icon class otherwise. Radio and checkbox lists show it as a description under the label.
**`key: { label: ..., ... }`** | a detailed option, taking `label`, `comment`, `icon`, `color`, `image`, `disabled` and `children`.
**`key: { optgroup: true, children: ... }`** | an option group in a dropdown, with the key as its heading.

### Presets {#option-presets}

`optionsPreset` takes one of the built-in lists: `icons`, `icons@phosphor`, `icons@bootstrap`, `locales`,
`flags`, `flags@short` or `timezones`. To add your own list, register it in a service provider's `boot`
method with `PresetManager::instance()->registerPreset('priorities', fn () => [...])`, using
`Larajax\Ui\Classes\PresetManager`. `extendPreset` takes a callback that receives a list's items and
returns them changed, and `replacePreset` swaps a list for another.
