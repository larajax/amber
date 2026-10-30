---
subtitle: Buttons, a search box and the list setup icon above a list.
demo: /reference/lists/toolbar
---
# Toolbar

The `Toolbar` widget is the bar above a list. It has three optional parts: a row of buttons, a search
box and the list setup icon. `bindToListWidget()` links the toolbar to a list, so its buttons can
follow and send the list's checked rows, and its search box searches the list. Placing a toolbar and
its list on a page is covered in [Building a List Page](/guides/lists). The handlers on this page only
report what they would do.

## Buttons from YAML {#yaml-buttons}

`buttons` takes a YAML file of button definitions, or the same definitions as an array. Each one
renders through the [`Ui` factory](/ui/buttons):

- A button with a `url` is a link, and the path becomes an address through
  [`Ui::url()`](/extending/seams#widget-paths).
- A button with a `handler` runs that AJAX handler, or with `popup: true` opens what the handler
  returns in a popup.
- `type` picks the style, `confirm` asks before the request runs, and `hotkey` binds a key, here
  Shift+P for the preview.
- `attributes` adds HTML attributes, here `data-request-flash` so the button shows the flash message
  its handler returns.
- `checked: true` keeps the button disabled until rows of the bound list are checked, and sends their
  ids to the handler as `checked`.

The Refresh handler returns the list's `onRefresh()`, which renders the list again, and the Count and
Delete handlers read the checked ids with `input('checked')`.

<<< @/../app/Controllers/Reference/ListsController/ToolbarExamples.php#docs-buttons-toolbar{php}

<<< @/../resources/ui/reference/lists/toolbar/buttons.yaml

<<< @/../app/Controllers/Reference/ListsController/ToolbarExamples.php#docs-button-handlers{php}

## Buttons from a partial {#partial}

Given any path other than a YAML file, `buttons` renders that file as a partial of the controller, so
the buttons can be anything the [`Ui` factory](/ui/buttons) makes. Here that is a popup button with a
hotkey, Shift+Q, a [dropdown](/ui/dropdowns) of bulk actions and a plain button that counts the
checked rows. The `data-list-checked-*` attributes link them to the list, as
[checked-state buttons](#checked-state) explains, and the `data-control="toolbar"` container keeps
them on [one line](#overflow).

The partial renders through the controller, so the controller needs to render views, as set up in
[Installation](/installation#prepare-your-controller).

<<< @/../app/Controllers/Reference/ListsController/ToolbarExamples.php#docs-partial-toolbar{php}

<<< @/../resources/views/reference/lists/toolbar/_list_toolbar.php

## Search box and list setup {#search}

`search` adds a search box, configured with the [search options](/lists/search#options), such as its
`prompt`. On a toolbar bound to a list, the box searches the list's `searchable` columns as you type.
Given a partial name in place of an array, `search` renders that partial instead of the box.

`setupHandler` adds the icon that opens the [list setup](/lists/options#setup). It takes the list's
`onLoadSetup` handler through `getEventHandler()`, and the visitor can then choose the columns, here
including the invisible excerpt, and the page size.

<<< @/../app/Controllers/Reference/ListsController/ToolbarExamples.php#docs-search-toolbar{php}

<<< @/../app/Controllers/Reference/ListsController/ToolbarExamples.php#docs-toolbar-list{php}

<<< @/../resources/ui/reference/lists/toolbar/columns.yaml

## Overflowing buttons {#overflow}

The `toolbar` control on the button container keeps the buttons on one line. When they do not fit,
drag them sideways or scroll with the mouse wheel, and a shadow marks the side with more buttons. Each
button here sends its name with `requestData`, which the handler reads with `input('button')`.

The control takes the options of [drag scroll](#drag-scroll), such as `data-no-drag-support="true"`.

<<< @/../app/Controllers/Reference/ListsController/ToolbarExamples.php#docs-overflow-toolbar{php}

<<< @/../resources/ui/reference/lists/toolbar/overflow.yaml

<<< @/../app/Controllers/Reference/ListsController/ToolbarExamples.php#docs-toolbar-button{php}

## Drag scroll {#drag-scroll}

The `drag-scroll` control, which the toolbar builds on, scrolls any row that is wider than its
container, by dragging or with the mouse wheel, and touch devices drag it too. Add
`data-control="drag-scroll"` to the element that overflows. A click that ends a drag is not passed on,
so the links inside do not open by accident.

While there is more to see, the control adds the `scroll-before` and `scroll-after` classes to the
element's parent, which this page styles as fades. When an item with the `active` class is out of
view, the parent also gets `scroll-active-before` or `scroll-active-after`.

<<< @/../resources/views/reference/lists/examples/drag-scroll.blade.php{blade}

The control, and the `toolbar` control, take these data attributes. Give each one the value `true`,
since an attribute with no value counts as off.

Attribute | Description
------------- | -------------
**data-no-drag-support** | turns dragging off, leaving the mouse wheel.
**data-vertical** | scrolls a column instead of a row.
**data-use-native-drag** | leaves the scrolling to the browser when the `html` element has the `mobile` class, and adds `is-native-drag` to the element.

The element dispatches `drag-scroll:start` when a drag begins, `drag-scroll:drag` as it moves or the
wheel scrolls it, and `drag-scroll:stop` when the drag ends. The events bubble, so a parent can listen
for them.

### DragScroll class {#drag-scroll-class}

The library's `index.js` exports the `DragScroll` class, for a Vite app that imports the library, as
in `import { DragScroll } from '../../vendor/larajax/ui/resources/assets/index.js'`.
`new DragScroll(element, options)` makes an element scrollable without the control.

Option | Description
------------- | -------------
**vertical** | scrolls a column instead of a row. Default: `false`.
**useDrag** | allows dragging. Default: `true`.
**useScroll** | allows the mouse wheel. Default: `true`.
**useComboScroll** | lets a vertical wheel scroll a row, and a horizontal one a column. Default: `true`.
**useNative** | leaves the scrolling to the browser when the `html` element has the `mobile` class. Default: `false`.
**noOverScroll** | keeps the wheel from scrolling the page once the element reaches its end. Default: `false`.
**scrollClassContainer** | the element, or a selector, that gets the `scroll-before` and `scroll-after` classes. Default: the element.
**noScrollClasses** | leaves the scroll classes out. Default: `false`.
**scrollMarkerContainer** | an element, or a selector, to add `before` and `after` marker spans to.
**dragSelector** | starts a drag only on elements matching this selector.
**scrollSelector** | listens for the wheel on the first child matching this selector.
**dragClass** | the class on `body` while dragging. Default: `drag`.
**start**, **drag**, **stop** | functions called when a drag starts, moves and ends.

Method | Description
------------- | -------------
**goToElement(element, callback, options)** | scrolls an item into view, animated over `options.duration` milliseconds (300 by default), then calls `callback`.
**goToStart()** | scrolls back to the start.
**isStart()** | returns whether the element is scrolled to its start.
**isEnd()** | returns whether the element is scrolled to its end.
**fixScrollClasses()** | updates the scroll classes, such as after the content changes.
**pause()** | stops dragging and the wheel until `resume()`.
**resume()** | allows dragging and the wheel again.
**dispose()** | removes the listeners and the markers.

## Checked-state buttons {#checked-state}

Buttons inside an element with `data-list-linkage="{list id}"` follow the checked rows of that list. A
toolbar bound with `bindToListWidget()` sets the attribute on itself, so every button in it is
linked.

- `data-list-checked-trigger` keeps the element disabled until rows are checked. An element with
  `data-list-checked-counter` inside it shows how many are checked, in brackets.
- `data-list-checked-request` adds the ids of the rows checked on the page to the element's AJAX
  request as `checked`, which the handler reads with `input('checked')`.

A button definition with `checked: true` sets both attributes, the second only when the button has a
`handler`. The `Ui` factory takes them as named arguments, `dataListCheckedTrigger: true` and
`dataListCheckedRequest: true`, as the partial above does.

## Conditional buttons {#conditional-buttons}

`visible: false` hides a button. `visible` can also name a controller method, which the toolbar calls
with the button definition when it renders, showing the button when the method returns `true`. A
method that does not exist throws an error. `permissions` hides the button from visitors without one
of the named gate abilities.

For changes that involve several buttons, add a `toolbarExtendButtons($toolbar)` method to the
controller. The toolbar calls it after defining its buttons, and it can use the toolbar's
`addButtons()`, `getButton()` and `removeButton()` methods. Each definition is a `ToolbarButton`
object whose options change with fluent calls, as in
`$toolbar->getButton('delete')->confirm('This cannot be undone.')`. Listeners of the toolbar's
`toolbar.extendButtons` event receive the toolbar in the same way.

## Toolbar options {#toolbar-options}

`Toolbar::make()` takes these options as named arguments.

Option | Description
------------- | -------------
**alias** | the widget's name, unique on the page.
**buttons** | the buttons, as a YAML file path or an array of [button definitions](#button-definitions), or the path of a partial that renders them.
**search** | the [search options](/lists/search#options) for a search box, or a partial to render in its place. Without it there is no search box.
**setupHandler** | the handler that opens the list setup, usually `$list->getEventHandler('onLoadSetup')`. It shows the setup icon.

`bindToListWidget($list)` links the toolbar to a list: its buttons follow the list's checked rows, and
its search box sets the list's search term and refreshes the list when the term changes.

## Button definitions {#button-definitions}

Option | Description
------------- | -------------
**label** | the button text, passed through the translator.
**icon** | an [icon](/extending/icons) name or CSS classes, such as `ph ph-plus`.
**type** | the style: `default`, `primary`, `secondary` or `danger`. Default: `default`.
**url** | makes the button a link to this address.
**handler** | makes the button run this AJAX handler.
**popup** | with `handler`, opens the handler's response in a popup. Default: `false`.
**confirm** | a message to confirm before the request runs.
**checked** | links the button to the checked rows of the bound list. Default: `false`.
**visible** | `false` hides the button, and the name of a controller method decides when it shows. Default: `true`.
**permissions** | gate abilities needed to see the button, as a string or an array where any one is enough.
**hotkey** | a key combination that clicks the button, such as `shift+p`.
**requestData** | data sent with the AJAX request.
**attributes** | HTML attributes added to the button, such as `data-request-flash: true`.
