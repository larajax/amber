---
subtitle: Build your own form widget, standalone widget or list widget, with its own partials, handlers and assets.
demo: /reference/extending/custom-widgets
---
# Custom Widgets

Your app can add its own widgets beside the built-in ones. A widget is a PHP class with its own
partials, any number of AJAX handlers, and a script and stylesheet that load only on the pages that
render it. There are three kinds, and this page builds one of each:

- A **form widget** is a field type. A form creates it for any field that names it as its `type`. The
  first is a time checker that compares the browser clock with the server clock, and the page walks
  through each of its files.
- A **standalone widget** is a part of a page that a controller builds itself, the way it builds a list
  or a form. The second is a feed of a post's comments, see [a standalone widget](#standalone-widget).
- A **list widget** is a column type. A list creates it for any column that names it as its `type`. The
  third is a meter that draws a number as a bar, see [a list widget](#list-widget).

When a screen needs something the built-in fields and widgets do not cover, build it as one of these
rather than as markup and script in a view. The widget carries its handlers and assets with it, so it
works on every page that uses it.

## The time checker widget {#time-checker}

The widget shows the browser clock ticking, and a button that asks the server for its time. Checking
runs the widget's own `onCheckTime` handler, which stamps the server time into the field and shows how
far the two clocks drift apart. Saving the form stores the stamp like any other field.

A field uses the widget by naming its code as the `type`, and passes options beside it, here the
`format` the stamp is shown in:

<<< @/../resources/ui/reference/extending/custom-widgets/time-checker.yaml

## The widget class {#widget-class}

A form widget extends `Larajax\Ui\Classes\FormWidgetBase`. The form creates it for the field, then
calls these methods:

- `init()` runs once the widget is created. `fillFromConfig()` copies options from the field's
  configuration onto the public properties of the same name, here `format`, keeping the property's
  value as the default.
- `render()` returns the widget's markup, usually from a partial.
- `getSaveValue($value)` receives the posted value when the form saves, and returns the value to store.
  Here an empty stamp is stored as `null`. When the form posts no value for the field at all, the
  widget is skipped and the field keeps its value.
- Public methods whose names start with `on` are AJAX handlers. The widget's markup calls them by the
  widget's alias, as `alias::onCheckTime`, which `getEventHandler('onCheckTime')` returns.

<<< @/../app/FormWidgets/TimeChecker.php{php}

The base class gives the widget what it needs to read its field:

Member | Description
------------- | -------------
**getLoadValue()** | the field's value, from the form's model or data, or the field's `default` on a new record.
**getFieldName()** | the name of the field's input, which the posted value must use.
**getId($suffix)** | a unique id for the widget's markup, with an optional suffix.
**getEventHandler($name)** | the full name of one of the widget's handlers, for a `data-request` attribute.
**makePartial($name, $vars)** | renders a partial from the widget's `partials` folder.
**fillFromConfig($names)** | copies field options onto the public properties of the same name.
**getConfig($name, $default)** | reads one field option.
**$this->vars** | the variables passed to every partial.
**$this->previewMode** | `true` when the form shows its values read-only.
**$this->model** | the model the form edits.
**$this->formField** | the field's definition, with its `label`, `comment` and other properties.

## Partials {#partials}

`makePartial('timechecker')` renders `_timechecker.php` from the `partials` folder beside the class,
with `$this` as the widget and the entries of `$this->vars` as variables. A partial can render another
one, as the outer partial does here.

The split matters for the handler. The outer partial keeps the element with the widget's id, which
stays on the page, and the inner `_clock` partial is what goes inside it. The handler returns the inner
partial keyed by `#` and that id, so Larajax replaces only the clock. The inner partial hides the
check button in preview mode.

<<< @/../app/FormWidgets/TimeChecker/partials/_timechecker.php{php}

<<< @/../app/FormWidgets/TimeChecker/partials/_clock.php{php}

The hidden input carries the stamp, named with `getFieldName()`, so the form posts it with the other
fields and `getSaveValue()` receives it.

## Registering the widget {#registering}

`registerFormWidget()` gives the class a code, which fields name as their `type`. Register your
widgets in a service provider's `boot()` method, through the widget manager's `registerFormWidgets()`
callback. The provider also registers the [list widget](#list-widget) further down this page:

<<< @/../app/Providers/AppServiceProvider.php{php}

## Its own assets {#assets}

`loadAssets()` runs when the widget is created, before `init()`, and adds the widget's files with
`addCss()` and `addJs()`. Each takes a path and the attributes for its tag, such as
`['defer' => true]` or `['type' => 'module']`. The path is resolved as follows:

- a relative path, such as `js/widgets/timechecker.js`, is under `public`, so it is served from
  `asset()`;
- when the widget sets an `$assetPath`, relative paths are under that path instead, while a path
  starting with `/` stays under `public`;
- a full URL, starting with `https://`, `http://` or `//`, is used as it is.

The script registers a control, `timechecker`, that the markup starts with
`data-control="timechecker"`. The control reads the server time from `data-server-time`, which the
partial sets after a check, ticks the browser clock, and works out the drift. Because the handler
replaces the element, a new control starts for each check, and `disconnect()` stops the old one's
timer.

<<< @/../public/js/widgets/timechecker.js{js}

<<< @/../public/css/widgets/timechecker.css{css}

See the control API of [Larajax](https://larajax.org) for `jax.registerControl()` and
`jax.ControlBase`.

## Printing the assets {#render-assets}

`Ui::renderAssets()` prints a `<link>` or `<script>` tag for every asset the widgets added during the
request, stylesheets first and each file once. Call it in your layout's `<head>`, after the library's
own assets. This works because Blade renders the page content before the layout around it, so by the
time the head renders, the page's widgets have added their assets.

<<< @/../resources/views/layouts/app.blade.php{blade}

`Ui::renderAssets('css')` or `Ui::renderAssets('js')` prints one kind only, for a layout that places
scripts at the end of the body.

A widget created by an AJAX handler, such as a form loaded into a popup, sends its assets with the
handler's response instead, and the browser loads any that the page does not have yet.

## A standalone widget {#standalone-widget}

A widget does not have to be a form field. A standalone widget is built by a controller and printed in
a view, the way the list, the filter and the form are. It suits a part of a page that has behaviour of
its own and that more than one page can use, such as a feed, a panel or a picker.

The comment feed shows the newest comments on a post, three at a time, with a button that loads the
next three. The controller builds it with `make()`, passing its options as named arguments:

<<< @/../app/Controllers/Reference/ExtendingController/StandaloneWidgets.php{php}

The page's action passes the widget to its view, which prints it with `{{ $commentFeed }}`. Three
things follow from building it this way:

- **Nothing to register.** A form widget needs a code because YAML names it. A standalone widget is
  built by its class, so the class is all there is.
- **Built in the action.** `make()` binds the widget to the controller, which is how its handlers are
  reached. Build it in the action on every request, see
  [built in the action](/guides/widgets#built-in-the-action).
- **Named by its alias.** The alias is the class name in lower case, here `commentfeed`, so the handler
  is `commentfeed::onLoadMore`. Two feeds on one page need an alias each, passed as
  `CommentFeed::make(alias: 'latestComments', ...)`.

## The standalone widget class {#standalone-class}

A standalone widget extends `Larajax\Ui\Classes\WidgetBase`, the class that `FormWidgetBase` and the
built-in widgets extend too. Everything [the widget class](#widget-class) says about `init()`,
`render()`, handlers, partials and `fillFromConfig()` applies here. What a standalone widget leaves out
is the field: it has no `getLoadValue()`, `getFieldName()` or `getSaveValue()`, and takes what it needs
through its options instead.

<<< @/../app/Widgets/CommentFeed.php{php}

- `init()` copies the `post` and `pageSize` options onto the properties of the same name, keeping the
  property's value as the default.
- `render()` prepares the first page of comments and returns the outer partial.
- `onLoadMore()` reads the number of comments the page shows from the posted `shown` value, prepares
  that many and one more page, and returns the inner partial keyed by `#` and the widget's id, so
  Larajax replaces what is inside the widget's element.

The feed keeps nothing between requests. The button posts the number of comments shown, and Larajax
runs the page's action again before the handler, so the handler works on the post the page was built
with. A widget that has to remember something, as the search box remembers its term, can keep it in
the session with `putSession()` and `getSession()`.

The partials sit in a `partials` folder beside the class, in a folder named after it. The outer partial
holds the element with the widget's id, and the inner one is what goes inside it, the same split the
time checker uses:

<<< @/../app/Widgets/CommentFeed/partials/_commentfeed.php{php}

<<< @/../app/Widgets/CommentFeed/partials/_comments.php{php}

The button is a [`Ui::ajaxButton()`](/ui/buttons#ajax-button). Its `handler` is the widget's own, from
`getEventHandler('onLoadMore')`, and its `requestData` posts the number of comments shown. Everything
a reader wrote goes through `e()`, since a partial is plain PHP and escapes nothing by itself.

The feed needs no script, only a stylesheet. `loadAssets()` adds it the way the time checker adds
[its own assets](#assets), and [`Ui::renderAssets()`](#render-assets) prints it:

<<< @/../public/css/widgets/commentfeed.css{css}

## A list widget {#list-widget}

A list widget is a column type of your own. Where a [partial column](/lists/columns#partial) suits one
list, a list widget is a class with options, partials and assets, so any list can name it as a column's
`type`. The meter draws the views of each post as a bar, filled by its share of `max`:

<<< @/../resources/ui/reference/extending/custom-widgets/meter.yaml

<<< @/../app/Controllers/Reference/ExtendingController/ListWidgetExamples.php{php}

The list makes one meter for the column the first time it renders a cell, and asks it for each row's
markup. The widget gets its code from `registerListWidget()`, inside the widget manager's
`registerListWidgets()` callback, in the same [service provider](#registering) as the form widget.

A built-in column type keeps its code: a list widget registered as `text` or `image` is never used for
those columns, and a code goes to the first widget registered under it.

## The list widget class {#list-widget-class}

A list widget extends `Larajax\Ui\Classes\ListWidgetBase`, a `WidgetBase` that knows its column.

<<< @/../app/ListWidgets/Meter.php{php}

- `init()` copies the column's options onto the properties of the same name, here `max`, as a form
  widget's `init()` does.
- `renderValue($record, $value)` returns the markup of one cell, given the row's record and the
  column's value. The list calls it for every row, so the widget keeps nothing per row.
- `loadAssets()` adds the stylesheet the first time a meter column renders.
- `columnDefaults()`, a static method, returns options a column of the type takes unless it sets them,
  such as `['sortable' => false]` for a column without a value of its own.

`getColumn()` returns the column definition and `getParentList()` the list. The partial receives the
values `renderValue()` passes it:

<<< @/../app/ListWidgets/Meter/partials/_meter.php

<<< @/../public/css/widgets/meter.css
