---
subtitle: Validation, dependent fields, triggers, presets, contexts and defaults.
demo: /reference/forms/behaviour
---
# Behaviour

Fields can react to each other in two places. In the browser, triggers show, hide or fill a field as
another one changes, and presets copy text from one field to another. On the server, the form refreshes
fields from the posted values, calls change handlers and lets the model adjust its fields. The model also
validates the saved values, and the form context and defaults decide what a new record starts with.

## Validation {#validation}

The model's `$rules` check the values on save, and a failed rule stops the save and shows its message.
The form passes each field's label to the model as the attribute name, so a message reads "The Name field
is required" rather than using the column name. A field the rules require gets the required marker
beside its label. The `required` property sets the marker yourself, without checking anything, and
`required: false` removes a marker the rules would add. `autoFocus` puts the cursor in the name field
when the page loads.

<<< @/../resources/ui/reference/forms/behaviour/validation.yaml

<<< @/../app/Models/Sample.php#docs-validation-rules{php}

The required marker is read from models that use the `Validation` trait. [Models](/guides/models) covers
validating other models.

## Dependent fields {#dependent-fields}

A field lists the fields it relies on in `dependsOn`. When one of them changes, the form posts its values
to its `onRefresh` handler and redraws the dependent field, so its options can follow the new values.
While the form refreshes, the model is filled with the posted values without being saved, so an options
method such as `getStateOptions` reads the picked country from `$this`. A redrawn field fires its own
change, so a chain of dependent fields updates in turn, as the city does here.

<<< @/../resources/ui/reference/forms/behaviour/dependent-fields.yaml

<<< @/../app/Models/Sample.php#docs-dependent-options{php}

The model's `filterFields` method runs on every render and refresh, with the form's fields and its
context (`refresh` during a refresh), and can change any property of any field. Here it hides the
postcode until a country is picked, and relabels it for the United States. A model can do the same from a
`model.form.filterFields` event listener.

<<< @/../app/Models/Sample.php#docs-filter-fields{php}

## Triggers on a checkbox {#trigger-checked}

`trigger` changes a field in the browser when another field meets a condition, with no request to the
server. It takes the `field` to watch, the `condition` and the `action`. The `checked` and `unchecked`
conditions watch a checkbox or switch. `show` and `enable` apply while the condition holds, and reverse
when it stops, so the discount code hides again when the box is unticked.

<<< @/../resources/ui/reference/forms/behaviour/trigger-checked.yaml

`show` and `hide` act on the element that wraps the field, while `enable`, `disable` and `empty` act on
the input. A field hidden by a trigger is still posted and saved. A field disabled by a trigger posts
nothing, so its saved value stays as it was.

## Triggers on a value {#trigger-value}

`value[a][b]` holds when the field's value is any of the listed values. A `*` matches any run of text, so
`value[courier*]` matches both courier options and `value[*]` holds for any value at all, while `value[]`
holds while the field is empty.

<<< @/../resources/ui/reference/forms/behaviour/trigger-value.yaml

## Trigger actions {#trigger-actions}

Besides `show` and `enable`, the actions are `hide`, `disable`, `empty`, which clears the field, and
`fill[value]`, which sets it (`fill` alone sets `1`, or ticks a checkbox). Join actions with a pipe, as in
`hide|empty`. `empty` and `fill` act once, when the condition starts to hold, and do not restore the old
value when it stops. Both fire a change on the field, so its own triggers and dependent fields follow.

<<< @/../resources/ui/reference/forms/behaviour/trigger-actions.yaml

## Triggers on a list {#trigger-multi-value}

A field that posts several values, such as a checkbox list, is named with `[]` in the trigger, as in
`features[]`, so the condition reads every ticked option and holds when any of them matches.

<<< @/../resources/ui/reference/forms/behaviour/trigger-multi-value.yaml

## Triggers on a parent field {#trigger-parent}

A trigger names a field of its own form, so inside a [nested form](/form-widgets/nested-form) or a
[repeater](/form-widgets/repeater) item it names fields of the same level. A leading `^` climbs one
level up to the parent form, and `^^` climbs two.

<<< @/../resources/ui/reference/forms/behaviour/trigger-parent.yaml

## Change handler {#change-handler}

`changeHandler` names an AJAX handler on the controller, called with the form's values whenever the
field changes. The handler decides what to send back. This one fills the form from the posted values with
`setFormValues()`, without saving them, and redraws the stats partial by replacing its field with
`renderField()`.

<<< @/../resources/ui/reference/forms/behaviour/change-handler.yaml

<<< @/../app/Controllers/Reference/FormsController/BehaviourHandlers.php

<<< @/../resources/views/reference/forms/_field_headline_stats.php{php}

## Refreshing the form {#refresh}

Every form has an `onRefresh` handler, called through the form's alias as `{alias}::onRefresh`, that
redraws the form from the posted values without saving them. Posting `fields`, a list of field names,
redraws just those fields, as the Recalculate button in this partial does for its own field. With no
`fields`, it redraws the whole form. Dependent fields use the same handler.

<<< @/../resources/ui/reference/forms/behaviour/refresh.yaml

<<< @/../resources/views/reference/forms/_field_stock_value.php{php}

## Nested names {#nested-names}

A name like `seo[title]` reads and saves a key inside an array attribute, here the `seo` column, which
the model lists in `$jsonable` so it is stored as JSON. When the first part names a singular relation,
such as `user[name]`, the value is saved on the related record instead.

<<< @/../resources/ui/reference/forms/behaviour/nested-names.yaml

## Contexts {#contexts}

A form has a context, usually `create` for a new record and `update` for a saved one, passed as
`context` to `Form::make()`. A field with a `context`, or with a name ending in `@context`, only appears
in that context, and `context` can also list several. Fields with neither appear in every context. Two
definitions such as `reference@create` and `reference@update` are the same field set up two ways, so
one YAML file serves both forms.

<<< @/../resources/ui/reference/forms/behaviour/contexts.yaml

The first example on the demo page edits the saved record, so its context is `update`. The second
renders the same YAML on a new record in the `create` context. That form, and the default and preset
forms below it, have an `arrayName` of `NewSample`, so their fields post under `NewSample[...]` and the
page's Save leaves them out:

<<< @/../app/Controllers/Reference/FormsController.php#docs-behaviour-action{php}

## Default values {#defaults}

`default` fills a field on a new record, and for option fields it names the option to select.
`defaultFrom` copies another attribute of the record instead. Defaults apply when the form is in the
`create` context, the record is not saved yet, or the form is nested in another, and only to attributes
that have no value, so they never replace saved values.

<<< @/../resources/ui/reference/forms/behaviour/defaults.yaml

## Input presets {#presets}

`preset` fills a field from another one as you type, converted by its `type`. `slug`, the type used when
`preset` is just a field name, and `file` turn the text into lowercase words joined by dashes, `url` does
the same with a leading slash, `camel` writes camelCase, and `exact` copies the text as typed. Accented
letters are transliterated, and short English words such as "a" and "the" are dropped.

A preset only fills an empty field, and stops once you edit the field yourself, so it suits create forms.
On a saved record the field already has a value, and the preset leaves it alone.

<<< @/../resources/ui/reference/forms/behaviour/presets.yaml

[Preset options](#preset-options) lists what `preset` takes as an array.

## The input preset control {#input-preset}

Presets work on any input through `data-input-preset`, a selector for the source input. The control
also has `snake` and `namespace` types, which form fields can use too, and
`data-input-preset-remove-words="false"` keeps the short words that are dropped by default.

<<< @/../resources/views/reference/forms/examples/input-preset.blade.php{blade}

Attribute | Description
------------- | -------------
**data-input-preset** | a selector for the input to copy from.
**data-input-preset-type** | the conversion: `slug`, `url`, `file`, `camel`, `snake`, `namespace` or `exact`. Default: `slug`.
**data-input-preset-closest-parent** | a selector for a parent of both inputs, which limits where the source is looked for.
**data-input-preset-prefix-input** | a selector for an input whose value starts the result.
**data-input-preset-remove-words** | `false` keeps short English words. Default: `true`.

The control dispatches `input-preset:before-update` and `input-preset:after-update` on the input it
fills.

## The input trigger control {#input-trigger}

Triggers work on any element through `data-trigger`, a selector for the inputs to watch, with
`data-trigger-action` and `data-trigger-condition`. They take the same actions and conditions as the
`trigger` property. Here the hint hides and the button enables once the box is ticked.

<<< @/../resources/views/reference/forms/examples/input-trigger.blade.php{blade}

Attribute | Description
------------- | -------------
**data-trigger** | a selector for the inputs to watch.
**data-trigger-action** | the actions, such as `show` or `hide\|empty`.
**data-trigger-condition** | `checked`, `unchecked` or `value[...]`.
**data-trigger-closest-parent** | selectors, separated by commas, for the parent to look for the inputs in. The first that matches an ancestor wins. Default: the whole page.

Dispatch `trigger:update` on the element to check the condition again, for example after changing the
watched input from a script. The control dispatches `trigger:after-update` with the new state.

## Trigger options {#trigger-options}

The `trigger` property takes these options.

Option | Description
------------- | -------------
**field** | the field to watch, by its name in the same form. `^` climbs to a parent form, and `[]` reads every value of a multi-value field.
**condition** | `checked`, `unchecked`, or `value[a][b]`, where `*` matches any text and `value[]` an empty field.
**action** | `show`, `hide`, `enable`, `disable`, `empty` or `fill[value]`, joined with `\|` to run several.

## Preset options {#preset-options}

`preset` takes a field name, which uses the `slug` type, or an array of these options.

Option | Description
------------- | -------------
**field** | the field to copy from, by its name in the same form.
**type** | the conversion: `slug`, `url`, `file`, `camel`, `snake`, `namespace` or `exact`. Default: `slug`.
**prefixInput** | a selector for an input whose value starts the result.
