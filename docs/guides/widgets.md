---
subtitle: How a widget attaches to the controller that builds it, and how its handlers are named.
---
# Widgets and Controllers

The widgets are [Larajax](https://larajax.org) view components. Larajax itself, with its controller,
AJAX handlers, responses, flash messages and browser API, has its own documentation; this page covers
only what the widgets add on top of it.

## Built in the action {#built-in-the-action}

A widget built with `::make()` inside a controller action is bound to that controller. Larajax runs the
page's action again before it calls an AJAX handler (see Larajax's
[controller integration](https://larajax.org/guide/laravel-integration.html)), so the widget exists again
when one of its own handlers is called, such as a list's paging or a form's file upload.

Build the widgets in the action, or a method it calls, on every request. A handler cannot reach a
widget the action did not build, so a widget built only on the first page load stops answering its
AJAX requests.

## Handler names {#handler-names}

A widget's handlers are addressed as `alias::onHandler`. The alias comes from the `alias` option, or
defaults to the widget's name: `list`, `form`, `filter`, `toolbar`. The posts toolbar opens the list
setup with `$list->getEventHandler('onLoadSetup')`, which returns `list::onLoadSetup`. Two widgets of the
same kind on one page need their own aliases, as the [quick post form](/guides/lists#quick-create) does
beside the main post form.

Handlers without an alias, such as the post form's `onUpdate`, are the controller's own, and Larajax
finds and calls them as its [AJAX handlers](https://larajax.org/guide/ajax-handlers.html) guide
describes.

## Widget events {#widget-events}

Widgets fire local events that you can bind to from the controller with `bindEvent()`. A bound event
that returns an array of partial updates adds them to the AJAX response, which lets one widget's
handler refresh another widget's markup. This is how a toolbar's search refreshes its bound list: the
search fires `search.submit` when a term is entered, and the binding answers with the refreshed list.
A search box on its own uses the same event to update any part of the page, see
[a standalone search](/lists/search#standalone).

The list fires an event for each of its extension points, such as `list.injectRowClass` for the classes
of a row, see [rows by record](/lists/options#row-events) and
[extending the list](/guides/lists#extending-the-list). For deeper changes, subclass a widget and
override its `event*` methods, listed in each guide.

## In the Larajax docs

The rest of the request belongs to Larajax:

- [Controller integration](https://larajax.org/guide/laravel-integration.html): extending the Larajax
  controller, and routes that answer both GET and POST.
- [AJAX handlers](https://larajax.org/guide/ajax-handlers.html) and
  [responses](https://larajax.org/guide/ajax-responses.html): the `ajax()` helper's updates, redirects
  and validation errors.
- [Flash messages](https://larajax.org/guide/flash-messages.html) and
  [loading indicators](https://larajax.org/guide/loading-indicators.html).
- [Hot controls](https://larajax.org/controls/definition.html): `jax.registerControl()`, which the
  library's own controls and a [custom widget](/extending/custom-widgets)'s script use.
