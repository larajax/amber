---
subtitle: Render a form for a model, save it, validate it and extend it.
---
# Building a Form Page

This guide builds the post editor of the demo app's Showcase, the pages at `/posts/create` and
`/posts/{id}/edit`. It covers rendering a form from a controller, saving and validating what comes
back, serving the create and edit pages from one set of field definitions, and extending a form. Every
snippet is the demo's own code, so you can [run the demo app](/#run-the-demo-app) and
watch it work.

## Describe the fields {#describe-the-fields}

A form starts with a YAML file of field definitions. The post form puts the title and slug above the
tabs, the details and publishing fields in primary tabs, and the content and the editor notes in
secondary tabs:

<<< @/../resources/ui/post/fields.yaml

Each key is a model attribute or relation name, and its definition picks the field type and how it
looks. `fields` holds the fields outside any tabs, `tabs` the primary tabs and `secondaryTabs` a second
row of tabs below them. The [field types](/forms/fields), [layout](/forms/layout) and
[behaviour](/forms/behaviour) pages cover every option, and each form widget used here, such as the
[relation](/form-widgets/relation), [tag list](/form-widgets/tag-list) and
[file upload](/form-widgets/file-upload), has a page of its own.

## Rendering a form {#rendering-a-form}

The controller keeps to its page actions. `create` and `edit` build the form for their record and pass
it to the view:

<<< @/../app/Controllers/PostsController.php

The form and the handlers that save it live in the `PostForm` trait, in a folder named after the
controller (see [Organising a controller](/installation#organising-a-controller)):

<<< @/../app/Controllers/PostsController/PostForm.php

`makeFormWidget()` calls `Form::make()` with three named arguments: `model` is the record the fields
read their values from, `fields` is the YAML file (a path starting with `~/` resolves from the
application root, and an array of definitions works too), and `context` picks the fields for the page,
see [Contexts](#contexts). The full list of options is under
[Widget configuration](#widget-configuration).

The trait keeps the form in the public `$formWidget` property. A widget built inside a controller
action is bound to that controller, which is what routes the widget's own AJAX handlers, such as the
file upload and the refresh of a field that `dependsOn` another, back to it, see
[Widgets and Controllers](/guides/widgets#built-in-the-action).

The view wraps the widget in a `<form>` element:

<<< @/../resources/views/posts/create.blade.php{blade}

- `data-request="onStore"` names the controller handler that runs when the form is submitted, and
  `data-request-flash` shows the message the handler returns.
- `{{ $formWidget }}` renders the fields. Widgets are `Htmlable`, so Blade prints their markup as it is.
- The hidden `_session_key` input posts the form's session key back with the save, which attaches the
  uploaded image, see [Held uploads](#held-uploads).
- [`Ui::ajaxButton()`](/ui/buttons#ajax-button) renders the submit button, with Ctrl+S (or Cmd+S) as
  its [hotkey](/ui/buttons#hotkeys).

The edit view is the same form with `onUpdate` as its handler, plus a Delete button that calls
`onDestroy` after a confirmation:

<<< @/../resources/views/posts/edit.blade.php{blade}

The page and its handlers share one address, so each route accepts both GET and POST, as shown in
[Routes](/installation#routes).

## Saving submitted data {#saving}

When the form is submitted, Larajax runs the page action again and then the handler named in
`data-request`: `onStore` on the create page, `onUpdate` on the edit page. Each handler builds the form
the way the page did and calls `saveForm()`.

`getSaveData()` returns the posted values, prepared by the field definitions:

- only fields of the current form and context are included, and a field missing from the post is left
  out, so a partial post never empties the others;
- `disabled` and `hidden` fields are removed, including fields the model hides in `filterFields`;
- form widgets turn their input into the value to save, such as a tag list into an array, and a number
  field returns a number or `null`.

`saveForm()` then checks the values, see [Validation](#validation), and saves them onto the post. The
relation fields post under their relation names, which Eloquent does not take as attributes, so the
handler maps them first: `category` and `author` onto their foreign keys, and `tags` into a list of keys
it syncs once the post exists. `fill($data)` sets the rest, and the post's fillable attributes keep
anything else out, such as the read-only `_permalink`. The [models guide](/guides/models#saving-relations)
covers each relation type.

### Held uploads and the session key {#held-uploads}

The featured image is a [file upload](/form-widgets/file-upload) field. An upload is held until the post
saves: the field stores the image with no post to belong to, and keeps it in the session against the
form's session key. The view posts that key back in `_session_key`, so the form built to save has the
same key, and when the post's Eloquent `saved` event fires the field attaches the image. Without the
key, the image stays unattached and the post saves without it.

The field listens for the record the form was built with, so build the form and save the record with
the same instance, as `onStore()` does with its new `Post`.

To share one session key between several forms on a page, pass it to each with the `sessionKey`
option.

### Saving related records {#perform-save-on-model}

A field can edit a related record in place, through a name such as `primary_group[name]` that reaches
into a singular relation. The `Larajax\Ui\Traits\FormModelSaver` trait saves such data:
`performSaveOnModel($model, $data)` fills the model, associates each `belongsTo` posted as a key, fills
the related record of every singular relation the data holds an array for, then saves them all in one
database transaction. Other relations are only held in memory, so sync a `belongsToMany` yourself once
it returns. The method is protected: add `use \Larajax\Ui\Traits\FormModelSaver;` to your controller, or
to its base class, and call `$this->performSaveOnModel(...)`.

Fields whose names start with an underscore, like the post form's `_permalink`, hold values for the
form only. `getSaveData()` keeps them, and `fill()` leaves them out only while they are not fillable
attributes, so keep such fields out of the fillable list or remove them from the data first.

## Validation {#validation}

Fields do not validate anything themselves: the `required` property only marks the label. Check the
values in the controller before saving them, and throw a `ValidationException` when they fail. Larajax
returns its messages to the browser by field name and marks those fields as invalid.

The post form validates in `saveForm()` with Laravel's validator, where the slug must be unique apart
from the post being edited:

<<< @/../app/Controllers/PostsController/PostForm.php#docs-rules{php}

`validate()` throws the `ValidationException` when a rule fails, which stops the save. The form cannot
read these rules, so the YAML sets `required: true` on the title, slug and category to show the marker.
The [validation example](/forms/behaviour#validation) shows it live.

The Authors form validates the same way, with a unique email that ignores the author being edited and a
password only required for a new author:

<<< @/../app/Controllers/AuthorsController/AuthorForm.php#docs-save-form{php}

Passing the failed validator to `ValidationException` returns its messages by field name. For a single
check, throw it with an array of messages instead, as the group form does with
`ValidationException::withMessages(['name' => 'The name field is required.'])`.

## Contexts {#contexts}

A form has a context, usually `create` or `update`, and a field can belong to one context only. Pass
the context to `Form::make()`, as `makeFormWidget()` does above, then limit a field in either of two
ways:

- add `context: create` (or a list of contexts) to its definition;
- end its name with the context, as the post form does with `slug@create` and `slug@update`: the slug
  is filled in from the title on a new post and read-only once the post exists.

Both forms of the name save to the same `slug` attribute. The Authors form uses the same pattern for
its password field, which reads "Password" when creating an author and "Reset Password" when editing
one. See [contexts](/forms/behaviour#contexts) for the live example.

## Widget configuration {#widget-configuration}

`Form::make()` takes these options as named arguments:

Option | Description
------------- | -------------
**model** | the model the form binds to. Field values are read from it, and relation fields and options methods use it.
**fields** | the fields outside any tabs, as a YAML file path or an array of definitions. A YAML file may also hold the `tabs` and `secondaryTabs` keys.
**tabs** | the primary tabs and their fields, when the `fields` file does not hold them.
**secondaryTabs** | the secondary tabs and their fields.
**context** | the form context, such as `create` or `update`. Fields that belong to another context are left out.
**data** | an object or array to read the field values from instead of the model.
**sessionKey** | the session key that uploads are held against until the record saves. Default: the posted `_session_key`, or a new key.
**arrayName** | wraps the input names in an array, such as `Post[title]`, and reads the posted values from it.
**isNested** | marks a form rendered inside a field of another form. Default: `false`.
**previewMode** | renders the values as read-only text, with no inputs. Default: `false`.
**horizontalMode** | puts each label beside its field instead of above it. Default: `false`.
**surveyMode** | renders the form as one column of questions, without tabs. Default: `false`.
**useFilterFields** | calls the model's `filterFields` method when the form renders, refreshes and saves. Default: `true`.
**alias** | the name the form's AJAX handlers are addressed by, as `alias::onHandler`. Default: `form`. A second form on the same page needs its own, like the [quick post form](/guides/lists#quick-create).

Options without a named argument pass through `make()` by name as well. To build a form from a
configuration array put together at runtime, call `Form::makeWithConfig($config)`.

## Extending the form {#extending-the-form}

The model can adjust its own form. A `filterFields($fields, $context)` method on the model runs every
time the form renders, refreshes or saves, with each field reachable by name. The post fills its
read-only permalink field from the category and the slug:

<<< @/../app/Models/Post.php#docs-filter-fields{php}

The permalink field lists `category` and `slug` in `dependsOn`, so changing either refreshes it and
`filterFields` runs again. Setting `hidden`, `disabled`, `label`, `options` or `value` on a field works
the same way, see [dependent fields](/forms/behaviour#dependent-fields).

For changes that belong to the form rather than the model, subclass `Larajax\Ui\Widgets\Form`, override
its extension points, and build your class with `make()` as before:

Method | Called
------------- | -------------
**eventExtendFieldsBefore()** | before the field definitions are read, to add fields with `addField()`, `addTabField()` or `addSecondaryTabField()`.
**eventExtendFields($fields)** | once the fields are defined, to change or remove them. `$fields` holds every field by name.
**eventBeforeRefresh($dataHolder)** | before a refresh. Changing `$dataHolder->data` changes the values the fields are filled from.
**eventRefreshFields($fields)** | during a refresh, before the fields render again.
**eventRefresh($result)** | after a refresh, with the partial updates for the browser. Return it with your own updates added.

From the controller you can also change a form after building it, with `addFields()`, `removeField()`,
`removeTab()` and `getField($name)`, before it renders.

A form built without `fields`, `tabs` or `secondaryTabs` asks the model for its fields instead. The
model's `defineFormFields($form)` method adds the fields outside the tabs, and
`definePrimaryFormFields($form)` and `defineSecondaryFormFields($form)` add those of each set of tabs.
Each adds a field with `$form->addFormField('name', 'Label')`, which returns a definition to chain the
field's options on, such as `->displayAs('textarea')->span('left')`.

## Next steps

- [Building a List Page](/guides/lists): the posts list that links to this form, and the quick post popup
- [Models](/guides/models): the relations, casts and contracts the widgets read
- [Field Types](/forms/fields): every field a form can hold
