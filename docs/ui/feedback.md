---
subtitle: Callouts, scoreboards, loading states and the change monitor.
demo: /reference/ui/feedback
---
# Feedback

Messages and figures for the reader, plus the states that controls add while a request runs or a
form changes: callouts from `Ui::callout()`, the scoreboard element, the loader container, the load
indicator and the change monitor.

## Callout types {#callouts}

`Ui::callout()` renders a highlighted message block. Its header shows a `label` as the title and a
`comment` under it, both escaped. Info is the default type, and `success: true`, `warning: true` or
`danger: true` pick the others. Each type shows its icon from the icon pack: the `callout.info`,
`callout.success`, `callout.warning` and `callout.danger` keys.

<<< @/../resources/views/reference/ui/examples/callout-types.blade.php{blade}

Used inline like this, with no body, the call prints the callout straight away with
<span v-pre>`{{ }}`</span>.

## Callout body {#callout-body}

A callout can hold any markup below its header. Open it with `->slot()`, which captures everything
printed after it as the body, up to `Ui::end()`, which renders the callout. Because the body is
captured rather than returned, the opening and closing calls go in `<?php ?>` tags. The body is plain
Blade, so it can hold any markup, print variables or include a view.

<<< @/../resources/views/reference/ui/examples/callout-body.blade.php{blade}

<<< @/../resources/views/reference/ui/_callout_checklist.blade.php{blade}

With a body and no `label` or `comment`, the callout has no header, and so no icon.

## Callout actions {#callout-action}

`Ui::slot('action')` switches the capture to the action area, which sits on the right of the header,
such as a button that fixes what the callout is about. Call it after the body, before `Ui::end()`.
When the callout has an action and no body, open it with `->slot('action')` in place of `->slot()`.

<<< @/../resources/views/reference/ui/examples/callout-action.blade.php{blade}

<<< @/../app/Controllers/Reference/UiController/FeedbackHandlers.php{php}

## Callout icons {#callout-icons}

`icon` swaps the type's icon for any icon classes, and `icon: false` leaves the icon
out, so the text starts at the left edge.

<<< @/../resources/views/reference/ui/examples/callout-icons.blade.php{blade}

## Callout arguments {#callout-arguments}

Argument | Description
------------- | -------------
**label** | the title in the header, escaped.
**comment** | the text under the title, escaped unless `commentHtml` is set.
**success** | success styling. Default: `false`.
**warning** | warning styling. Default: `false`.
**danger** | danger styling. Default: `false`.
**icon** | icon classes, or `false` for no icon. Default: the type's icon.
**commentHtml** | prints the comment as HTML, unescaped. Use it only with markup you trust. Default: `false`.

Any other argument becomes an attribute on the callout, with `class` merged into its classes. Add
`class: 'is-flush'` to drop the bottom margin and corners, so the callout sits on top of another panel.

The callout's capture calls:

Call | Description
------------- | -------------
**Ui::callout(...)->slot()** | opens the callout and captures what follows as its body.
**Ui::callout(...)->slot('action')** | opens the callout and captures what follows as its action area.
**Ui::slot('action')** | switches the capture of the open callout to its action area.
**Ui::end()** | ends the capture and prints the callout.

The form's `hint` field renders the same callout markup, from YAML (see [Field Types](/forms/fields)).

## Scoreboard {#scoreboard}

The scoreboard element lays out key figures in a row. Each `scoreboard-item title-value` holds a
heading, the figure and a `description` line. A `positive` or `negative` class on the figure adds an
up or down trend arrow, and the figure can hold an icon.

<<< @/../resources/views/reference/ui/examples/scoreboard.blade.php{blade}

The items sit inside a `data-control="toolbar"` element, so the row scrolls sideways by dragging when
it runs out of room, as a [toolbar](/lists/toolbar) does.

## Loader container {#loader-container}

`data-control="loader-container"` dims an element and ignores clicks on it while a request sent from
inside it runs, so the visitor cannot start a second action until the first one finishes. The
container has an `is-loading` class while the request runs. The handler here sleeps for a moment so
you can see it.

<<< @/../resources/views/reference/ui/examples/loader-container.blade.php{blade}

## Load indicator {#load-indicator}

A `loading-indicator` element inside a `loading-indicator-container` covers the container with a
spinner and an optional line of text in a `<div>`. Add `size-small` for a smaller spinner, or
`indicator-center` to center it. Put the indicator first in the container, and remove it when the work
is done.

<<< @/../resources/views/reference/ui/examples/load-indicator-markup.blade.php{blade}

Class | Description
------------- | -------------
**loading-indicator-container** | positions the container, so the indicator can cover it.
**loading-indicator** | the indicator, with a `<span>` for the spinner and an optional `<div>` of text.
**size-small** | a smaller spinner.
**indicator-center** | centers the spinner.
**is-opaque** | covers the content with a solid background, in place of a see-through one.

## Load indicator from JavaScript {#load-indicator-api}

The `LoadIndicator` class adds and removes the indicator for you. `LoadIndicator.show(element, options)`
places it in the element and adds an `in-progress` class, and `LoadIndicator.hide(element)` removes it.
Import it from the library's `index.js`, at the same published address that `ui.js` loads it from, so
the page shares one copy.

<<< @/../resources/views/reference/ui/examples/load-indicator-api.blade.php{blade}

Option | Description
------------- | -------------
**text** | a line of text under the spinner.
**opaque** | covers the content with a solid background.
**centered** | centers the spinner.
**size** | `small` for a smaller spinner.

## Change monitor {#change-monitor}

`data-change-monitor` on a form watches its fields. As soon as one changes, it adds the
`oc-data-changed` class to the form, and the browser warns before the visitor leaves the page with
unsaved changes. Here the class switches the badge from saved to unsaved.

A button with `dataChangeMonitorCommit`, which renders `data-change-monitor-commit`, marks the form
saved: when clicked, and again when its request succeeds. In text fields only keys that change the
value count, so moving the cursor with the arrow keys does not.

<<< @/../resources/views/reference/ui/examples/change-monitor.blade.php{blade}

Attribute | Description
------------- | -------------
**data-change-monitor** | enables the control on a form.
**data-change-monitor-commit** | on an element inside the form, marks the form saved when clicked and when its request succeeds.

The form dispatches `change-monitor:ready` when the control starts, `change-monitor:changed` when the
first change is made, and `change-monitor:unchanged` when it is marked saved. Dispatch these on the
form to steer it:

Event | Description
------------- | -------------
**change-monitor:unchange** | marks the form saved.
**change-monitor:pause** | stops watching for changes.
**change-monitor:resume** | starts watching again.
**change-monitor:pause-unload** | stops the warning before leaving the page.
**change-monitor:resume-unload** | turns the warning back on.

`jax.changeMonitor.disable()` turns every change monitor on the page off, and
`jax.changeMonitor.enable()` turns them back on.
