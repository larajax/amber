---
subtitle: How the widgets work as Larajax view components.
---
# AJAX & Larajax

Every widget is a [Larajax view component](https://larajax.org/guide/defining-components). Building a
widget with `::make()` inside a controller action binds it to that controller, and from then on the
widget's AJAX handlers (sorting, paging, filtering, uploads, refreshing a field) reach it on their own.
This page explains that lifecycle, so you can reason about it and hook into it.

## The request lifecycle {#request-lifecycle}

1. The page loads as usual: the action builds the widgets and the view renders them.
2. A control in the browser sends an AJAX request to the same address, naming a handler such as
   `list::onPaginate`, the widget's alias and the method.
3. Larajax runs the **same controller action** first, so the widgets are built exactly as they were for
   the page, then calls the named handler.
4. The handler returns a response, usually partial updates keyed by element id
   (`['#element-id' => '<html>']`) along with a flash message or a redirect, which the browser
   applies.

Two things follow from this.

**Routes accept POST.** The AJAX request is a POST to the page's own address, so each page route
matches both methods:

<<< @/../routes/web.php#docs-post-routes{php}

**Build the widgets the same way on every request.** The action runs again for each AJAX request, so
build widgets in the action, or a method it calls, rather than only on some requests. A handler cannot
reach a widget the action did not build.

## Handler names {#handler-names}

A widget's handlers are addressed as `alias::onHandler`. The alias comes from the `alias` option, or
defaults to the widget's name: `list`, `form`, `filter`, `toolbar`. The posts toolbar opens the list
setup with `$list->getEventHandler('onLoadSetup')`, which returns `list::onLoadSetup`. Two widgets of the
same kind on one page need their own aliases, as the [quick post form](/guides/lists#quick-create) does
beside the main post form.

A handler name without an alias, such as `onUpdate`, is looked up on the controller, in this order:

1. a method named after the action and the handler, such as `edit_onUpdate`, which only the `edit` page
   answers;
2. a method named after the handler, such as `onUpdate`;
3. the first widget on the page that has a method by that name.

Handlers receive the action's route parameters, so the post form's `onUpdate(Post $post)` gets the same
post that `edit(Post $post)` showed. See [saving submitted data](/guides/forms#saving).

In markup, a handler is named by a `data-request` attribute, such as `data-request="onStore"` on the
post form, or by the `handler` argument of a [`Ui` button](/ui/buttons#ajax-button).

## Responses {#responses}

A handler returns what the browser should do next. The Showcase handlers build their responses with
Larajax's `ajax()` helper:

- `ajax()->flash('success', 'Post updated.')` shows a message, when the request element has
  `data-request-flash`;
- `->redirect(route('posts.edit', $post))` opens another page;
- `->update($this->listWidget->onRefresh())` replaces the list's markup with its refreshed rows.

A handler may also return a string of markup, as the popup handlers do when they render a form into a
popup, or an array of partial updates. Throwing a `ValidationException` stops the handler and returns
its messages, see [validation](/guides/forms#validation).

## Guarding side effects {#guarding-side-effects}

The action runs for the page load and again for every AJAX request on the page. Keep work that should
happen once per visit, such as marking notifications as read or counting a view, inside
`if (! request()->ajax()) { ... }`, and leave the widget building outside it.

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

## Further reading

The action lifecycle, flash messages, loading indicators, redirects and the `window.jax` browser API are
Larajax features shared by every view component. See the [Larajax documentation](https://larajax.org)
for the full picture.
