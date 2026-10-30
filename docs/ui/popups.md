---
subtitle: Modal dialogs, popovers and tooltips.
demo: /reference/ui/popups
---
# Popups

The `popup` control opens a modal dialog on a native `<dialog>` element, usually with markup returned
by an AJAX handler. It is styled with Bootstrap 6's dialog classes and needs no Bootstrap JavaScript.
Popovers open a panel beside an element, and tooltips show a short hint on hover.

## Popup button {#popup-button}

`Ui::popupButton()` renders a button that runs its `handler` and opens the response in a dialog. The
`dataSize` attribute sets the width, from `tiny` to `giant` (every size is listed under
[Sizes](#window-sizes)), and `requestData` sends extra values to the handler, here the size, so the
dialog can name it in its title.

<<< @/../resources/views/reference/ui/examples/popup-buttons.blade.php{blade}

The handler returns the dialog markup, rendered from a view:

<<< @/../app/Controllers/Reference/UiController/PopupHandlers.php{php}

<<< @/../resources/views/reference/ui/_dialog.blade.php{blade}

The markup goes straight inside the `<dialog>`, in three parts: `dialog-header` with a `dialog-title`
and a close button, `dialog-body`, and `dialog-footer` with the actions. A `dialog-body` with no header
or footer can add `dialog-no-header` or `dialog-no-footer` for even padding. Any element with
`data-dismiss="popup"` closes the popup when clicked.

Wrapping the parts in a form lets its buttons submit everything in the dialog. A request element with
`data-popup-load-indicator`, here the form with its `data-request`, hides the popup behind a loading
indicator while its request runs, then closes the popup when the request succeeds or shows it again
with a shake when it fails.

A popup opened from inside another stacks over it, and the one below steps back until the top one
closes, as the dialog's Open another button shows. While a handler's content loads, a backdrop with a
loading indicator covers the page.

## Sizes from the window and content {#window-sizes}

Besides the fixed widths, a size can follow the window or the content. `adaptive` fills the window
less 100 pixels (the whole width on small screens), `auto` fits the content, a number sets the width
in pixels and `fullscreen` covers the window. `dataAdaptiveHeight` stretches any size to the window
height, less 100 pixels.

<<< @/../resources/views/reference/ui/examples/popup-window-sizes.blade.php{blade}

Size | Width
------------- | -------------
*(none)* | 598px.
**tiny** | 300px.
**small** | 400px.
**large** | 750px.
**huge** | 900px.
**giant** | 982px.
**adaptive** | the window width less 100px, and the full width on small screens.
**auto** | fits the content.
**fullscreen** | covers the window.
a number, such as **600** | that many pixels.

The handler can also set the size from its response, with a `data-popup-size` attribute on the first
element of the markup or any element inside it. Every size adds a `size-*` class to the dialog, for
your own styles.

## Closing {#closing}

Escape closes a popup, and a click on the backdrop shakes it, so a stray click does not lose what the
visitor typed. `dataKeyboard: 'false'` makes Escape shake it too, and `dataBackdrop: true` lets a
backdrop click close it. The close button and any other `data-dismiss="popup"` element always close
it.

<<< @/../resources/views/reference/ui/examples/popup-closing.blade.php{blade}

To close a popup from JavaScript, dispatch a bubbling `popup:close` event on the popup or any element
inside it, such as `new CustomEvent('popup:close', { bubbles: true })`.

## Popup control in markup {#popup-markup}

`data-control="popup"` works on any element, which then opens the popup when clicked. This is what
`Ui::popupButton()` renders. `data-handler` names the handler, and `data-request-data` sends extra
values with it, written as `key: value` pairs. Every option has a data attribute, listed under
[Popup options](#popup-options).

<<< @/../resources/views/reference/ui/examples/popup-markup.blade.php{blade}

## Popups from JavaScript {#popup-javascript}

`jax.popup.open()` takes the same options as the attributes, in camel case, such as `handler`,
`extraData` and `size`. `content` places markup in the dialog without a request, here copied from a
`<template>` already on the page.

<<< @/../resources/views/reference/ui/examples/popup-javascript.blade.php{blade}

`jax.popup` is the `Popup` class, also exported by the library's `index.js` for code that imports it.
Its static methods:

Method | Description
------------- | -------------
**open(options)** | opens a popup with no trigger element and returns it.
**fromElement(element, options)** | opens the popup for a trigger element, or reloads it when it is open. Given a method name in place of the options, it calls that method on the popup: `fromElement(element, 'lock', true)`.
**getInstance(element)** | returns the popup for its trigger, its `<dialog>` or the first element of its content, or `null`.
**bindToPopups(container, vars)** | adds the variables as hidden inputs to the form of any popup opened from inside the container, and from popups opened from that popup.
**focusFirstInput(container)** | focuses the first `form-control` input, unless an element has `autofocus`.

A popup instance has `show()`, `hide()`, `reload()`, `toggle()`, `lock(true)` to stop it closing,
`setLoading(true)` and `setShake()`.

## Popover {#popover}

`data-control="popover"` opens a panel next to the element when it is clicked, and a second click
closes it. Only one popover is open at a time. `data-content-from` copies the content from an element
on the page, such as a `<template>`, `data-placement` picks the side, `data-width` fixes the width in
pixels, and `data-modal` dims the page behind it.

<<< @/../resources/views/reference/ui/examples/popover.blade.php{blade}

The content can use a `popover-head`, with a heading, a line of text and a close button, and a
`popover-body`. Any element with `data-dismiss="popover"` inside it closes the popover, and so does a
click outside it or the Escape key. When the preferred side has no room, the popover moves to another
side that fits.

Option | Description
------------- | -------------
**placement** | `top`, `bottom`, `left`, `right` or `center`, the middle of the window. Default: `bottom`.
**fallbackPlacement** | the side used when no side fits. Default: `bottom`.
**content** | the content as HTML, without a template.
**contentFrom** | a selector of an element whose HTML becomes the content.
**width** | the width in pixels. Default: the width of the content.
**modal** | dims the page behind the popover. Default: `false`.
**highlightModalTarget** | raises the trigger above the dimmed page. Default: `false`.
**closeOnPageClick** | closes the popover on a click outside it. Default: `true`.
**closeOnEsc** | closes the popover on Escape. Default: `true`.
**containerClass** | extra classes for the popover element.
**offset** | how far the popover is shifted along the trigger, in pixels. Default: `15`.
**offsetX** | the offset for the `top` and `bottom` sides, in place of `offset`.
**offsetY** | the offset for the `left` and `right` sides, in place of `offset`.
**container** | an element or selector to mount the popover in. Default: the enclosing `<dialog>`, or else the body.
**useAnimation** | fades the popover in and out. Default: `false`.
**onClose** | a function called after the popover is removed, from JavaScript only.
**onCheckDocumentClickTarget** | a function given the target of a click outside the popover, which keeps it open by returning `true`, from JavaScript only.

Set each option as a data attribute in kebab case, such as `data-content-from` or
`data-close-on-page-click="false"`. The `Popover` class, exported by the library's `index.js`, takes the
same options from JavaScript: `new Popover(trigger, options).show()`, with `setContent(html)` and
`hide()`.

The popover dispatches `popover:showing` before it opens and `popover:hiding` before it closes, where
`preventDefault()` cancels either, then `popover:show` and `popover:hide`, all on the trigger. Dispatch
`popover:close` on any element inside it to close it.

## Tooltip {#tooltip}

Any element with `data-tooltip-text` shows a tooltip after 300 milliseconds of hovering, below it
unless `data-tooltip-position="top"`. It hides when the pointer leaves, on any click or key press, and
before an AJAX update. The control listens on the document, so elements added later need no setup.

`data-tooltip-hotkey` adds key labels after the text, separated by commas. This is how buttons with a
[hotkey](/ui/buttons#hotkeys) show their shortcut, since they set all three attributes.

<<< @/../resources/views/reference/ui/examples/tooltip.blade.php{blade}

Attribute | Description
------------- | -------------
**data-tooltip-text** | the tooltip text.
**data-tooltip-hotkey** | key labels shown after the text, separated by commas, such as `⌃S, ⌘S`.
**data-tooltip-position** | `top` places the tooltip above the element. Default: below.

`jax.tooltips.clear()` hides the tooltip and cancels one that is waiting to show. Inside a popup, the
tooltip mounts in the dialog so it shows above the backdrop. The tooltips on form fields and list
columns are a different one: Bootstrap's own tooltip, with `data-bs-toggle="tooltip"`.

## Popup options {#popup-options}

Set these as data attributes on a `data-control="popup"` element, as attributes of
`Ui::popupButton()`, or as options of `jax.popup.open()`.

Attribute | Option | Description
------------- | ------------- | -------------
**data-handler** | **handler** | the AJAX handler that returns the dialog markup.
**data-ajax** | **ajax** | a URL to load the dialog markup from, in place of a handler.
*(none)* | **content** | the dialog markup as an HTML string, an element or a function, with no request.
**data-extra-data** | **extraData** | extra request data, as `key: value` pairs or an object. The trigger's `data-request-data` is merged in.
**data-size** | **size** | the width, from the [sizes](#window-sizes) table.
**data-adaptive-height** | **adaptiveHeight** | stretches the popup to the window height. Default: `false`.
**data-keyboard** | **keyboard** | `false` makes Escape shake the popup in place of closing it. Default: `true`.
**data-backdrop** | **backdrop** | what a backdrop click does: `static` shakes the popup, `true` closes it and `false` ignores the click. Default: `static`.

Inside the dialog:

Attribute | Description
------------- | -------------
**data-dismiss="popup"** | closes the popup when clicked.
**data-popup-load-indicator** | on an element with `data-request`, hides the popup behind a loading indicator while that request runs, closes it on success and shakes it on failure.
**data-popup-size** | on the first element of the markup, or any element in it, sets the popup size.

## Popup events {#popup-events}

The popup dispatches these on its trigger and on the first element of its content. `event.detail`
holds `popup`, the instance, `trigger`, and `modal`, the `<dialog>` element.

Event | Description
------------- | -------------
**popup:show** | the content was placed and the popup is opening. Also dispatched on the document.
**popup:shown** | the opening transition finished.
**popup:hide** | the popup is closing.
**popup:hidden** | the popup closed and was removed.
**popup:complete** | the handler's content loaded.
**popup:error** | the handler's request failed.
**popup:toggle** | `toggle()` was called.
**popup:download** | the handler responded with a file download.

## Arguments {#arguments}

### Ui::popupButton() {#popup-button-arguments}

Argument | Description
------------- | -------------
**label** | the button text, escaped.
**handler** | the AJAX handler that returns the dialog markup, rendered as `data-handler`.
**requestData** | an array of extra values to send to the handler, rendered as `data-request-data`.
**icon** | icon classes or an icon pack key, rendered before the label.
**primary** | primary styling, `btn-solid theme-primary`. Default: `false`.
**secondary** | secondary styling, `btn-solid theme-secondary`. Default: `false`.
**danger** | danger styling, `btn-solid theme-danger`. Default: `false`.
**hotkey** | a keyboard shortcut, or an array of them, that opens the popup.

Any other argument becomes an attribute, as on [buttons](/ui/buttons#attributes), so the
[popup options](#popup-options) go in as `dataSize`, `dataKeyboard`, `dataBackdrop` and
`dataAdaptiveHeight`.
