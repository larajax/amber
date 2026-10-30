---
subtitle: Pick several values as tags, saved as an array, a string or a relation.
demo: /reference/form-widgets/tag-list
---
# Tag List

The `taglist` field picks several values as tags, typed in or chosen from a list of options. It saves
them as a JSON array, as one joined string, or as related records.

## Modes {#modes}

`mode` sets how the tags are saved, and when it is not set it follows the attribute:

- a relation uses `relation` mode, which saves the related records' keys;
- a JSON attribute uses `array` mode, which saves a JSON array;
- anything else uses `string` mode, which saves one string with the tags joined by `separator`.

A JSON attribute is listed in `$jsonable` on an October Rain model, or has an `array` cast on a plain
Eloquent model. See [Models](/guides/models) for both.

<<< @/../app/Models/Sample.php#docs-jsonable{php}

## Array mode {#array}

`mode: array` saves the tags as a JSON array, such as `["red", "blue"]`. `tags` is already in
`$jsonable`, so the mode would follow anyway, and setting it makes the choice explicit. `customTags`
lets the user type new tags, each ended with Enter or the separator, a comma by default. `placeholder`
fills the field while it is empty.

<<< @/../resources/ui/reference/form-widgets/tag-list/array.yaml

## String mode {#string}

String mode joins the tags into one value with `separator`, either `comma` (the default) or `space`,
and splits the saved value the same way when the form loads. The separator also ends a typed tag, so
here a space finishes each keyword.

<<< @/../resources/ui/reference/form-widgets/tag-list/string.yaml

## Fixed options {#options}

`options` limits the tags to a fixed list, saved by their keys, such as `["design", "research"]`. Like a
dropdown's options, it takes an inline list, the name of a model method that returns them, or `true` to
call the model's `get{Field}Options()` method. `maxItems` caps how many tags can be picked.

<<< @/../resources/ui/reference/form-widgets/tag-list/options.yaml

## Saving labels {#use-key}

`useKey: false` saves the option labels instead of their keys, here `["English", "Japanese"]`. Custom
tags always save the text that was typed, so `customTags` turns `useKey` off.

<<< @/../resources/ui/reference/form-widgets/tag-list/use-key.yaml

## Relation mode {#relation}

On a relation, such as this `belongsToMany` relation to `UserGroup`, the options are every record of
the related model, named by its `nameFrom` column, and saving syncs the relation to the picked records.

<<< @/../resources/ui/reference/form-widgets/tag-list/relation.yaml

<<< @/../app/Models/Sample.php#docs-belongs-to-many{php}

On a plain Eloquent model, sync the relation in your save handler, as the
[relation field](/form-widgets/relation#eloquent) shows.

## Creating related records {#relation-custom}

With `customTags` in relation mode, the field matches tags to records by their `nameFrom` column, and a
typed tag that matches no record creates one, setting only that column. The new record is saved
without validation, so the related model's other columns must accept empty values. When the field also
sets `options`, it cannot create records, and saving a tag that matches none of the options throws an
error.

<<< @/../resources/ui/reference/form-widgets/tag-list/relation-custom.yaml

## Read-only {#read-only}

`readOnly` lists the tags without an input, and still posts them from hidden inputs. This copy reads the
tags through `valueFrom`, and its name starts with an underscore, which keeps it out of the saved data.

<<< @/../resources/ui/reference/form-widgets/tag-list/read-only.yaml

## Preview {#preview}

`disabled` lists the picked labels in preview mode, and the field is left out of the save.

<<< @/../resources/ui/reference/form-widgets/tag-list/preview.yaml

## Options

Option | Description
------------- | -------------
**mode** | how the tags are saved: `array`, `string` or `relation`. Default: follows the attribute.
**separator** | the character that joins the tags in string mode and ends a typed tag: `comma` or `space`. Default: `comma`.
**customTags** | lets the user type new tags. In relation mode a new tag creates a record. Default: `false`.
**options** | a fixed list of tags, the name of a model method that returns them, or `true` for `get{Field}Options()`. Default: the related records in relation mode, otherwise none.
**useKey** | saves the option keys rather than their labels. Turned off by `customTags`. Default: `true`.
**nameFrom** | in relation mode, the related model's column that names each tag. Default: `name`.
**placeholder** | the text shown while the field is empty. Default: none.
**maxItems** | the most tags that can be picked. Default: no limit.

The field also takes the common [field properties](/forms/fields#field-properties), such as `readOnly`,
`disabled` and `valueFrom`.
