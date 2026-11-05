---
subtitle: Drive the widgets from plain Eloquent models.
---
# Models

The widgets read and save plain Eloquent models. Relations are methods, JSON attributes are casts, and
a few small contracts from `octoberrain/contracts` add trees, manual sorting and file uploads. Every
model in the demo app is a stock Eloquent model: the posts, categories, tags and groups of the Showcase,
Laravel's `User` model as the authors, and the `Sample` record the Reference pages edit.

## Relations {#relations}

Declare relations as methods, the usual Laravel way. The post belongs to a category and an author, has
tags, comments and a featured image:

<<< @/../app/Models/Post.php#docs-relations{php}

The author belongs to a primary group and to other groups:

<<< @/../app/Models/User.php#docs-eloquent-relations{php}

A relation field, column or scope finds its relation by name, so a field named `category` reads the
`category()` method. The widgets call a method that takes no arguments to see whether it returns a
relation, so keep relation methods free of required parameters.

## How the widgets read a model {#model-inspector}

The widgets never ask a model about its relations directly. They go through the model inspector service,
`app('model.inspector')`, which reads your relation methods and casts. It answers the questions the
widgets need:

Method | Returns
------------- | -------------
**hasRelation($model, $name)** | whether the model has a relation by that name.
**getRelationType($model, $name)** | the relation type, such as `belongsTo` or `belongsToMany`.
**isRelationTypeSingular($model, $name)** | whether the relation holds one record, such as `belongsTo` or `hasOne`.
**makeRelation($model, $name)** | an instance of the related model.
**getRelationSimpleValue($model, $name)** | the related key, or the list of related keys.
**setRelationValue($model, $name, $value)** | sets a relation from a key or keys. A `belongsTo` is associated, and other relations hold the related records in memory.
**isJsonable($model, $attribute)** | whether the attribute stores JSON, through an `array`, `json`, `object` or `collection` cast.

Your own extensions can ask the same questions, for example
`app('model.inspector')->getRelationType($user, 'groups')` returns `belongsToMany`.

## Saving relations {#saving-relations}

A relation field posts its value under the relation's name: a key for a `belongsTo`, a list of keys for
a `belongsToMany`. Eloquent does not take relation names as attributes, so your save code maps them: a
`belongsTo` posts onto its foreign key, and a `belongsToMany` syncs once the record exists. The Authors
form saves the user's groups this way:

<<< @/../app/Controllers/AuthorsController/AuthorForm.php#docs-eloquent-relation-save{php}

The same goes for a [tag list](/form-widgets/tag-list) bound to a relation, which posts the keys of the
picked tags and creates the new ones first. The Reference pages save every relation of the sample with
one loop, which asks the model inspector which values are relations:

<<< @/../app/Classes/ReferenceControllerBase.php#docs-save-example{php}

Some widgets save their relation themselves and post no value: a [file upload](#file-attachments), a
[repeater](/form-widgets/repeater#relation) and a [nested form](/form-widgets/nested-form#relation)
bound to a relation.

When a field that another field `dependsOn` changes, the form refreshes from the posted values, and it
sets a posted relation on the model through `setRelationValue()` for the refresh. That is how the post's
permalink follows a newly picked category before the post is saved, see
[extending the form](/guides/forms#extending-the-form).

## Validation {#validation}

A model has no rules of its own, so validate the values in the controller before saving them:

1. read the values with `getSaveData()`;
2. run Laravel's `Validator::make()` on them, with rules that can depend on the record, such as a
   unique email that ignores the record being edited;
3. when it fails, `validate()` throws a `ValidationException`, which returns its messages by field
   name and marks those fields as invalid.

The Authors and post forms do exactly this, see
[validation in the controller](/guides/forms#validation). The form cannot read the rules from your
controller, so mark required fields with `required: true` in the YAML when you want the marker.

## JSON attributes {#json-attributes}

Fields that save structured values, such as a [repeater](/form-widgets/repeater), a
[nested form](/form-widgets/nested-form) or a [tag list](/form-widgets/tag-list) in array mode, need an
attribute that stores JSON. Give it an `array` cast, as the post does for its keywords:

<<< @/../app/Models/Post.php#docs-casts{php}

The user's `links` attribute has one too:

<<< @/../app/Models/User.php#docs-casts{php}

The widgets read any `array`, `json`, `object` or `collection` cast as JSON, through the model
inspector's `isJsonable()`.

## File attachments {#file-attachments}

[File uploads](/form-widgets/file-upload) store each file as a record of a file model, related to the
record it belongs to. The file model is a plain Eloquent model that implements
`October\Contracts\Database\AttachmentInterface`:

- `fromPost($fileInput)` stores an uploaded file and fills in the model;
- `getPath()` returns the address of the file, and `getThumb($width, $height, $options)` the address of
  a thumbnail;
- the static `getMaxFilesize()` returns the largest file it accepts, in kilobytes.

The upload field also reads the `file_name`, `file_size`, `title` and `description` attributes of each
file. The demo's file model stores the uploads on the public disk, under `uploads/public`, or under
`uploads/protected` when the file is private. It has no image resizer, so its thumbnail is the image
itself:

<<< @/../app/Models/File.php#docs-attachment-interface{php}

When the file model also implements `SortableInterface`, dragging files into a new order stores it
through `setSortableOrder()`, and a `getSortOrderColumn()` method names the column the field lists the
files by.

The record holds its files through a `hasOne` or `morphOne` relation for one file, and a `hasMany` or
`morphMany` relation for a list. The demo's relations are morph relations, each narrowed to its own
field with `withAttributes()`, which both filters the files and sets the field on a new one, like the
post's featured image in its [relations](#relations). A relation can set other attributes the same way,
such as the private contract on the [file upload](/form-widgets/file-upload) page.

An upload is held until the record saves. The field stores the file with no record to belong to and
keeps its key in the session, against the form's session key, and a removal is held the same way. When
the record's Eloquent `saved` event fires, the field attaches the held uploads and deletes the files
held for removal, so saving the record is all it takes. Leaving the page without saving leaves the
record as it was, and the held uploads stay in the table with no record. With `deferredBinding: false`,
an upload to a saved record is attached straight away.

The files live in a `files` table, whose `attachment_id` column is nullable, since a held upload belongs
to no record yet:

<<< @/../database/migrations/2026_10_29_000004_create_files_tables.php

## Trees {#trees}

A tree model implements `October\Contracts\Database\TreeInterface`, which the
[list structure](/lists/list-structure), the [relation field](/form-widgets/relation#tree) and the
[record finder](/form-widgets/record-finder#tree) read:

- `getChildren()` returns the records directly below a record, and `getChildCount()` how many records
  sit below it;
- the `getNested()` scope runs the query and returns its top level records, with their children nested
  below them;
- the `listsNested()` scope returns the values of a column in tree order, indented by depth.

The post category stores a simple tree, where each category names its parent in a `parent_id` column:

<<< @/../app/Models/Category.php#docs-tree{php}

Dragging a row in a list structure saves the move in two steps: the list associates the model's
`parent` relation with the new parent, and when the model implements `SortableInterface` it passes
`setSortableOrder()` the keys of the rows in their new order. The category stores that order in
`sort_order`:

<<< @/../app/Models/Category.php#docs-sortable-order{php}

## Reorderable lists {#reorderable-lists}

A flat reorderable list works on any model. To store the new order, the model implements
`October\Contracts\Database\SortableInterface` with a `setSortableOrder()` method and a `sort_order`
column, as the `User` model does. See [reorderable list](/lists/list-structure#reorder).

## Next steps

- [Building a Form Page](/guides/forms): saving and validating a form
- [Relation](/form-widgets/relation): relation fields and the trees they show
- [Widgets and Controllers](/guides/widgets): how the widgets' handlers reach your controller
