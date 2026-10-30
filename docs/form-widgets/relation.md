---
subtitle: Pick related records from a dropdown or a checkbox list.
demo: /reference/form-widgets/relation
---
# Relation

The `relation` field picks records through a model relation, with the options read from the related
model. A relation to one record renders as a dropdown, and a relation to many as a checkbox list.

## Dropdown {#dropdown}

A `belongsTo`, `hasOne` or `morphOne` relation renders as a dropdown, and saving sets the relation to
the picked record. The field name is the relation name, here the `user` relation to the `User` model.

<<< @/../resources/ui/reference/form-widgets/relation/dropdown.yaml

<<< @/../app/Models/Sample.php#docs-belongs-to{php}

`nameFrom` names each option from a column of the related model, `name` by default. It can also name an
accessor, though the field then loads every record to read it. `emptyOption` adds a first option that
saves no record, so the relation can be cleared. `defaultSort` orders the options by a `column` and a
`direction`, which is `desc` when left out.

## Checkbox list {#checkbox-list}

A `belongsToMany`, `morphToMany`, `morphedByMany` or `hasMany` relation renders as a checkbox list, and
saving syncs the relation to the checked records. Unchecking every box clears it.

<<< @/../resources/ui/reference/form-widgets/relation/checkbox-list.yaml

<<< @/../app/Models/Sample.php#docs-belongs-to-many{php}

`scope` narrows the options with a model scope on the related model, here leaving out the guest group.
The scope is called with the query and the record being edited, and `scope` also takes a static
`Class::method` with the same arguments. On a checkbox list, `emptyOption` is the message shown when
there are no options.

<<< @/../app/Models/UserGroup.php#docs-without-guest{php}

## Shaping the options {#options-query}

`select` builds each option's label in SQL, in place of `nameFrom`. `conditions` filters the options
with a raw SQL where clause, and a `:column` placeholder in it reads that attribute of the record being
edited, quoted, such as `country = :country`. Both are adapted to the database in use, so `concat()`
also works on SQLite and PostgreSQL.

`excludeFrom` names an attribute of the record whose keys are left out of the options, here the saved
owner's `user_id`, so the reviewer cannot be the owner. Without it, a relation to the same model, such
as a category's parent, leaves out the record being edited.

<<< @/../resources/ui/reference/form-widgets/relation/options-query.yaml

`conditions` takes the place of `scope`, so when both are set only `conditions` applies. With neither,
the relation's own constraints filter the options.

## Tree dropdown {#tree}

When the related model is a tree, using the `NestedTree` or `SimpleTree` trait, the options follow the
tree, and each child is indented under its parent. When `scope` or `conditions` leaves out a parent, its
children still show.

<<< @/../resources/ui/reference/form-widgets/relation/tree.yaml

<<< @/../app/Models/Category.php#docs-nested-tree{php}

## Tree checkbox list {#tree-checkbox-list}

A `belongsToMany` relation to a tree nests the checkboxes the same way, with each child under its
parent.

<<< @/../resources/ui/reference/form-widgets/relation/tree-checkbox-list.yaml

## Quick create {#quick-create}

`quickCreate` adds a Create New option at the top of the dropdown. Picking it opens a popup with a form
for the related model, and saving the popup creates the record and selects it in the dropdown. The new
record is stored at once, while the field's own value waits for the form's Save like any other change.

<<< @/../resources/ui/reference/form-widgets/relation/quick-create.yaml

`quickCreate` takes the path to the popup's fields YAML, or these options:

Option | Description
------------- | -------------
**fields** | the popup form's fields, as the path to a fields YAML file.
**title** | the popup title. Default: `New :name`.
**optionText** | the dropdown option that opens the popup. Default: `- Create New :name -`.
**context** | the context the popup form renders in. Default: `quickcreate`.
**popupSize** | the popup width, such as `small` or `large`. Default: the popup's own size.

`:name` stands for the field label. The popup form renders in its own context, so a field limited to
other contexts stays out of it: here the members field sets `context: [create, update]`, and the popup
shows only the group's name, code and description.

<<< @/../resources/ui/reference/form-widgets/relation/quick-create-form.yaml

Quick create works on a dropdown only, and is not offered in preview or read-only mode. When the field
has no `emptyOption` or `placeholder`, it gains a Select placeholder, so Create New is never picked by
default, and saving while Create New is still picked clears the relation.

## Read-only {#read-only}

`readOnly` shows the picked option as text, and still posts its key from a hidden input. This copy
reads the owner through `valueFrom`, and its name starts with an underscore, which keeps it out of the
saved data.

<<< @/../resources/ui/reference/form-widgets/relation/read-only.yaml

## Disabled {#disabled}

`disabled` locks the options, and the field is left out of the save.

<<< @/../resources/ui/reference/form-widgets/relation/disabled.yaml

## Saving on a plain Eloquent model {#eloquent}

The field reads its options and the current selection from Eloquent relation methods as well as from
Rain's relation arrays, so the Showcase's `User` model, a stock Eloquent model, declares its relations
the usual Laravel way:

<<< @/../app/Models/User.php#docs-eloquent-relations{php}

Saving is where the two differ. An October Rain model takes the posted keys as attribute values and sets
its relations when it saves. A plain Eloquent model needs the relations set in your save handler, as the
Showcase author form does: it maps the dropdown onto the foreign key, and syncs the checkbox list once
the record is saved.

<<< @/../app/Controllers/AuthorsController/AuthorForm.php#docs-eloquent-relation-save{php}

An empty selection posts an empty string, while a missing key means the field was not on the form, so
sync only when the key is present. The same goes for a [tag list](/form-widgets/tag-list#relation) or a
[record finder](/form-widgets/record-finder) on a relation. [Models](/guides/models) covers the rest of
what plain Eloquent supports.

## Options

Option | Description
------------- | -------------
**nameFrom** | the related model's column, or accessor, that labels each option. Default: `name`.
**select** | an SQL expression that labels each option, in place of `nameFrom`.
**emptyOption** | on a dropdown, a first option that saves no record. On a checkbox list, the message shown when there are no options.
**scope** | a model scope on the related model, or a static `Class::method`, that narrows the options. Also accepted as `modelScope`.
**conditions** | a raw SQL where clause that narrows the options, with `:column` placeholders from the record, used in place of `scope`.
**defaultSort** | the option order, as a `column` and a `direction`, or a column name alone for descending order.
**excludeFrom** | an attribute of the record whose keys are left out of the options.
**quickCreate** | adds a Create New option to a dropdown, as a fields YAML path or the options above.
**readOnly** | shows the picked option as text and still posts its value. Default: `false`.

The field also takes the common [field properties](/forms/fields#field-properties), such as
`placeholder`, `disabled` and `valueFrom`.
