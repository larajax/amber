---
subtitle: A form inside a field, saved as a JSON object or a related record.
demo: /reference/form-widgets/nested-form
---
# Nested Form

The `nestedform` field renders a whole form inside one field. On a JSON attribute it saves the inner
fields as one object, and on a relation it edits the related record.

## JSON object {#default}

The `form` option takes the same definition as any form, inline or as the path to a YAML file, and the
inner fields can be of any type. Each inner field is named inside the field, such as `address[line1]`,
and the field saves them together as one object, such as `{"recipient": "...", "line1": "..."}`.

<<< @/../resources/ui/reference/form-widgets/nested-form/default.yaml

The attribute must hold JSON. On an October Rain model, list it in `$jsonable`, as the `Sample` model
does for every structured field on these pages. On a plain Eloquent model, give it an `array` cast. See
[Models](/guides/models) for both.

<<< @/../app/Models/Sample.php#docs-jsonable{php}

## Tabs {#tabs}

The inner form can have `tabs` and `secondaryTabs` of its own, beside or instead of its `fields`. Its
tabs are styled as nested tabs, so they read as part of the field rather than the page.

<<< @/../resources/ui/reference/form-widgets/nested-form/tabs.yaml

## Without the panel {#no-panel}

The inner form sits in a panel by default, with a border and a background. `showPanel: false` drops the
panel, so the inner fields line up with the rest of the form while still saving as one object.

<<< @/../resources/ui/reference/form-widgets/nested-form/no-panel.yaml

## Relation {#relation}

When the field name is a relation on the model, the inner form edits the related record instead of a
JSON value. Here `primary_group` is a `belongsTo` relation, so the inner fields are the group's own
`name` and `code`. Saving the form saves the group as well, and a validation error on the group shows
on its inner field.

<<< @/../resources/ui/reference/form-widgets/nested-form/relation.yaml

<<< @/../app/Models/Sample.php#docs-belongs-to{php}

The field suits a singular relation, such as `belongsTo` or `hasOne`. While the relation is empty, the
inner fields start blank. `defaultCreate: true` instead creates a record and attaches it as the form
loads (against the session key on a Rain model), so the inner form always edits a record the relation
holds.

## Preview {#preview}

`disabled` renders the inner form in preview mode, and the field is left out of the save. This copy
reads the address through `valueFrom`.

<<< @/../resources/ui/reference/form-widgets/nested-form/preview.yaml

## Options

Option | Description
------------- | -------------
**form** | the inner form, with `fields`, `tabs` and `secondaryTabs`, inline or as the path to a YAML file.
**showPanel** | wraps the inner form in a panel. Default: `true`.
**defaultCreate** | on a relation, creates and attaches a related record when there is none. Default: `false`.

The field also takes the common [field properties](/forms/fields#field-properties), such as `disabled`
and `valueFrom`.
