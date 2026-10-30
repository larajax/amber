---
subtitle: Repeat a small form as a list of items, saved as JSON or related records.
demo: /reference/form-widgets/repeater
---
# Repeater

The `repeater` field repeats a small form as a list of items that can be added, sorted, duplicated and
removed. On a JSON attribute it saves the items as an array, and on a relation each item is a related
record.

## Items {#default}

`form` defines the fields of each item, inline or as the path to a fields YAML file. `prompt` labels the
add button, and `titleFrom` names an item field whose value titles the item's header. Drag the handle to
sort the items, check several to drag them together, and use the item menu to duplicate, move or remove
one.

<<< @/../resources/ui/reference/form-widgets/repeater/default.yaml

Each item field is named inside the repeater with the item's index, such as `faqs[0][question]`. The
items save as a JSON array of objects, in their order on the page, and saving with no items stores
`null`. The fields of a new item render in the `create` context and those of a saved item in `update`,
so `context` works on item fields as it does on any form.

The attribute must hold JSON. On an October Rain model, list it in `$jsonable`, and on a plain Eloquent
model give it an `array` cast. See [Models](/guides/models) for both.

<<< @/../app/Models/Sample.php#docs-jsonable{php}

## Item limits {#limits}

`minItems` starts the field with that many empty items, and stops removal below that count. `maxItems`
hides the add button once the field is full. With `groups`, `minItems` adds no empty items, since the
field cannot tell which group they would be.

<<< @/../resources/ui/reference/form-widgets/repeater/limits.yaml

## Collapsed items {#collapsed}

`itemsExpanded: false` shows each item as its header only, titled by `titleFrom`. Opening one item
closes the others.

<<< @/../resources/ui/reference/form-widgets/repeater/collapsed.yaml

## Fixed order {#fixed-order}

`showReorder: false` removes the drag handle and the move options from the item menu, so the items stay
in the order they were added. `showDuplicate: false` removes Duplicate from the item menu.

<<< @/../resources/ui/reference/form-widgets/repeater/fixed-order.yaml

## Groups {#groups}

`groups` replaces `form` with several item types, and the add button opens a menu of them. Each group is
keyed by its code and has a `name`, a `description`, an `icon` and its `fields`, plus its own
`titleFrom`. `groups` also takes the path to a YAML file of groups, and a group can be the path to a
YAML file of its own.

<<< @/../resources/ui/reference/form-widgets/repeater/groups.yaml

Each saved item records its group's code under `groupKeyFrom`, which defaults to `_group`. Here it is
`type`, so a text item saves as `{"title": "...", "body": "...", "type": "text"}`.

## Builder {#builder}

`displayMode: builder` lists the items in a sidebar and edits one at a time beside it, which suits
longer items. A group can set `useTabs` for its own fields, and a group field can be another repeater.

<<< @/../resources/ui/reference/form-widgets/repeater/builder.yaml

## Tabs {#tabs}

`useTabs` moves the item fields into tabs, named by each field's `tab`, so every item has its own set
of tabs.

<<< @/../resources/ui/reference/form-widgets/repeater/tabs.yaml

## Nested repeaters {#nested}

An item field can be another repeater. Its items save as an array inside each item, such as
`[{"day": "...", "stops": [{"place": "..."}]}]`.

<<< @/../resources/ui/reference/form-widgets/repeater/nested.yaml

## Watching the parent form {#parent-fields}

Item fields are named inside the repeater, so a `trigger` that watches a field of the parent form starts
its name with `^`, one for each level up. Here every step's Details field shows while the parent form's
Step Style is Detailed.

<<< @/../resources/ui/reference/form-widgets/repeater/parent-fields.yaml

## Relation {#relation}

When the field name is a relation, each item is a related record, and the item fields are its
attributes. Here `links` is a `hasMany` relation to `SampleLink`.

<<< @/../resources/ui/reference/form-widgets/repeater/relation.yaml

<<< @/../app/Models/Sample.php#docs-has-many{php}

Adding an item creates an empty record at once, so its columns must accept empty values, and attaches
it against the form's session key. Removing an item detaches it the same way. Save commits both and
writes each item's fields, and when the related model uses the `Sortable` trait it also stores the item
order. Pass the session key when you save, as [Building a Form Page](/guides/forms) shows. With
`groups`, the group code is stored in the related model's `groupKeyFrom` column.

<<< @/../app/Models/SampleLink.php{php}

A plain Eloquent model has no deferred binding, so there adding and removing items writes to the
database straight away.

## Preview {#preview}

`disabled` renders the items in preview mode, where they can only be expanded and collapsed, and the
field is left out of the save. This copy reads the questions through `valueFrom`.

<<< @/../resources/ui/reference/form-widgets/repeater/preview.yaml

## Options

Option | Description
------------- | -------------
**form** | the fields of each item, inline or as the path to a fields YAML file.
**groups** | several item types in place of `form`, inline or as the path to a YAML file. See below.
**prompt** | the add button label. Default: `Add New Item`.
**titleFrom** | the item field whose value titles each item. With `groups`, each group's own `titleFrom` applies instead. Default: none.
**groupKeyFrom** | the key, or the related model's column, that stores each item's group code. Default: `_group`.
**minItems** | the fewest items, added empty when there are fewer. Default: none.
**maxItems** | the most items, after which the add button hides. Default: none.
**displayMode** | `accordion` lists the items in place, `builder` edits them one at a time beside a sidebar. Default: `accordion`.
**itemsExpanded** | shows every item open. `false` shows the headers and opens one at a time. Default: `true`.
**showReorder** | allows sorting the items. Default: `true`.
**showDuplicate** | adds Duplicate to the item menu. Default: `true`.
**useTabs** | moves the item fields into tabs, named by each field's `tab`. Default: `false`.

The field also takes the common [field properties](/forms/fields#field-properties), such as `disabled`
and `valueFrom`.

### Group options {#group-options}

Option | Description
------------- | -------------
**name** | the group name in the add menu.
**description** | a line under the name in the add menu.
**icon** | the group's icon class, such as `ph ph-image`. Default: the icon pack's default group icon.
**fields** | the group's item fields.
**titleFrom** | the item field whose value titles the group's items.
**useTabs** | moves the group's fields into tabs. Default: the field's `useTabs`.
