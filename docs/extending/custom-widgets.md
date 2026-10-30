---
subtitle: Build your own form widget, with its own partials, handler and assets.
demo: /reference/extending/custom-widgets
---
# Custom Widgets

Your app can add its own form widgets beside the built-in ones. A form widget is a PHP class with its
own partials, any number of AJAX handlers, and a script and stylesheet that load only on the pages
that render it. This page builds one, a time checker that compares the browser clock with the server
clock, and walks through each file.

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
callback:

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
