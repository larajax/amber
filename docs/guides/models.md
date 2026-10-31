---
subtitle: Drive the widgets from October Rain models or plain Eloquent models.
---
# Models

The widgets read and save Eloquent models. They work with **October Rain models**
(`October\Rain\Database\Model`), which extend Eloquent with relations declared in arrays, validation
rules, file attachments, trees and deferred bindings, and with **plain Eloquent models**, which need no
trait and no changes. The demo app's Showcase uses both side by side: the posts, categories, tags and
groups are Rain models, and the authors are Laravel's stock `User` model.

## Rain and Eloquent side by side {#side-by-side}

A Rain model declares its relations in public arrays. The post belongs to a category and an author,
has tags, comments and a featured image:

<<< @/../app/Models/Post.php#docs-rain-relations{php}

A plain Eloquent model declares them as methods, the usual Laravel way. The author belongs to a
primary group and to other groups:

<<< @/../app/Models/User.php#docs-eloquent-relations{php}

The two kinds mix freely. The post's `author` relation points at the Eloquent `User`, and the user's
`posts()` method points back at the Rain `Post`, so the posts list shows each author's name and the
authors list counts each author's posts. A relation field, column or scope works the same way whichever
kind of model it reads.

## How the widgets read a model {#model-inspector}

The widgets never ask a model about its relations directly. They go through the model inspector service,
`app('model.inspector')`, which reads a Rain model's relation arrays when it has them and your relation
methods otherwise. It answers the questions the widgets need:

Method | Returns
------------- | -------------
**hasRelation($model, $name)** | whether the model has a relation by that name.
**getRelationType($model, $name)** | the relation type, such as `belongsTo` or `belongsToMany`.
**isRelationTypeSingular($model, $name)** | whether the relation holds one record, such as `belongsTo` or `hasOne`.
**makeRelation($model, $name)** | an instance of the related model.
**getRelationSimpleValue($model, $name)** | the related key, or the list of related keys.
**setRelationValue($model, $name, $value)** | sets a relation from a key or keys. A `belongsTo` is associated, and other relations hold the related records in memory.
**isJsonable($model, $attribute)** | whether the attribute stores JSON: a Rain model's `$jsonable`, or an `array`, `json`, `object` or `collection` cast.

Your own extensions can ask the same questions, for example
`app('model.inspector')->getRelationType($user, 'groups')` returns `belongsToMany`.

## What works on plain Eloquent {#what-works-on-eloquent}

With a stock Eloquent model, such as Laravel's default `User`:

- **Forms**: every basic field type, tabs, contexts and `getSaveData()`, and relation fields read from
  your relation methods.
- **Lists**: every column type, including relation columns and counts, with sorting, pagination and
  search. The library registers the `searchWhere` query methods as Eloquent builder macros, so a search
  works on any model.
- **Filters**: every scope type, including the group, date, text and number scopes.
- **Toolbars**: buttons and search.
- **Model hooks**: options methods for fields, columns and scopes, model query scopes through
  `modelScope`, and the `filterFields` and `filterScopes` methods.

The authors pages run on the Eloquent `User`: a filtered list that counts each author's posts, and a
form with a relation field for each kind of group.

## Saving relations {#saving-relations}

Saving relation values is where the two kinds differ.

- **Rain models** take relation values as attributes. The post lists `category`, `author` and `tags`
  as fillable, so `$post->fill($data)->save()` associates the category and author and syncs the tags.
  See [saving submitted data](/guides/forms#saving).
- **Plain Eloquent models** need the relations set in your save code: a `belongsTo` posts under its
  relation name and maps onto the foreign key, and a `belongsToMany` syncs once the record exists. The
  same goes for a [tag list](/form-widgets/tag-list) bound to a relation. The
  [relation field](/form-widgets/relation) shows how the Authors form saves each relation type.

## Validation {#validation}

A Rain model that uses the `Validation` trait lists its rules in `$rules` and checks them on every
save, as the post does. The form reads the same rules to mark a required field, so the YAML needs no
`required` property. See [validation on a Rain model](/guides/forms#validation-on-the-model).

A model without the trait, such as a plain Eloquent model, has nothing that checks it on save, so
validate the values in the controller before saving them:

1. read the values with `getSaveData()`;
2. run Laravel's `Validator::make()` on them, with rules that can depend on the record, such as a
   unique email that ignores the record being edited;
3. when it fails, throw a `ValidationException` with the validator, which returns its messages by field
   name, then save.

The Authors form does exactly this, see
[validation in the controller](/guides/forms#validation-in-the-controller) for its code. Without the
trait the form cannot read the rules, so mark required fields with `required: true` in the YAML when you
want the marker.

## JSON attributes {#json-attributes}

Fields that save structured values, such as a [repeater](/form-widgets/repeater), a
[nested form](/form-widgets/nested-form) or a [tag list](/form-widgets/tag-list) in array mode, need an
attribute that stores JSON. On a Rain model, list the attribute in `$jsonable`, as the post does for its
keywords:

<<< @/../app/Models/Post.php#docs-jsonable{php}

On a plain Eloquent model, give it an `array` cast, as the user's `links` attribute has:

<<< @/../app/Models/User.php#docs-casts{php}

The widgets treat both the same way, through the model inspector's `isJsonable()`, which also accepts
`json`, `object` and `collection` casts.

## File attachments {#file-attachments}

[File uploads](/form-widgets/file-upload) attach files to a record through a Rain model's `$attachOne`
and `$attachMany` relations, like the post's featured image in its
[relations](#side-by-side). The related model is a file model that extends
`October\Rain\Database\Attach\File`, which stores each upload on a disk and keeps its name, size, type
and the record it belongs to. The demo's own file model picks where the uploads live:

<<< @/../app/Models/File.php

The file records live in a `files` table, and uploads to a record that is not saved yet wait in a
`deferred_bindings` table until the record saves with the form's session key. The demo creates both in
one migration:

<<< @/../database/migrations/2026_10_29_000004_create_files_tables.php

## Reorderable lists {#reorderable-lists}

A flat reorderable list works on any model. To store the new order, a plain Eloquent model implements
`October\Contracts\Database\SortableInterface` with a `setSortableOrder()` method and a `sort_order`
column, as the `User` model does. See [reorderable list](/lists/list-structure#reorder).

## What needs October Rain {#what-needs-rain}

These features rely on parts of Rain's model and have no plain Eloquent version:

- **File uploads** (`type: fileupload`) attach files through Rain's `$attachOne` and `$attachMany`
  relations and its deferred bindings, like the post's featured image. See
  [file upload](/form-widgets/file-upload).
- **Trees** in the list structure need a model with Rain's `NestedTree` or `SimpleTree` trait, like the
  post categories. See [list structure](/lists/list-structure).
- **Deferred bindings** in general, which attach related records to a new record when it saves, are a
  Rain model feature.

Rain comes with Larajax UI and runs in a plain Laravel application, so a model that needs one of these
extends `October\Rain\Database\Model` in place of Eloquent's `Model`, and keeps working as an Eloquent
model everywhere else.

## Next steps

- [Building a Form Page](/guides/forms): saving and validating each kind of model
- [Relation](/form-widgets/relation): relation fields on both kinds of model
- [Widgets and Controllers](/guides/widgets): how the widgets' handlers reach your controller
