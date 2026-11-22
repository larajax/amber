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
  the [URL resolver](/extending/seams#widget-paths).
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

## Dropdown menus {#menus}

A button with `items` opens a menu of them instead of running an action of its own. Each item takes the
options of a button, so it can follow a link, run a handler or open a popup, and a menu adds these:

- `header` labels the items below it, and an item given as the word `divider` draws a line between
  groups of items. Give a divider any key that is unique in its menu, such as `deleteDivider`.
- An item with `items` of its own opens them in a submenu, here the Export formats.
- `hotkey` on an item clicks it while the menu is closed, and the menu shows the keys at the end of the
  item. Here Shift+X exports the checked posts as CSV.
- `active: true` marks the current choice with a check, as on the Sort menu.
- `type` colors an item, such as `danger` for the Delete item.

`checked: true` on the menu keeps its button disabled until rows are checked, and every item with a
`handler` sends their ids as `checked`. A menu with no visible items is hidden, and a divider or header
left with nothing to separate or label is left out.

<<< @/../app/Controllers/Reference/ListsController/ToolbarExamples.php#docs-menus-toolbar{php}

<<< @/../resources/ui/reference/lists/toolbar/menus.yaml

<<< @/../app/Controllers/Reference/ListsController/ToolbarExamples.php#docs-checked-button{php}

## Split buttons and single rows {#split-buttons}

A button with `items` and an action of its own, a `url` or a `handler`, is a split button: the button
runs its action, and the caret beside it opens the menu. With `checked: true`, both halves wait for
checked rows.

`checked: single` enables a button only while exactly one row is checked, for an action on one record,
such as the Show button here. Check a second row and it is disabled again.

<<< @/../app/Controllers/Reference/ListsController/ToolbarExamples.php#docs-split-toolbar{php}

<<< @/../resources/ui/reference/lists/toolbar/split.yaml

## Groups and alignment {#groups-alignment}

- `group: true` with `items` joins the items into one button group, side by side.
- `active` marks a button as pressed. Like `visible`, it takes `true` or the name of a controller
  method, which receives the button definition. Here `isToolbarViewActive` presses the List button.
- `disabled` greys a button out, and takes a controller method name in the same way.
- An entry given as the word `divider` draws a line between buttons.
- `partial` renders a partial of the controller in place of a button, here a count of the published
  posts. The partial receives the definition as `$button` and the toolbar as `$toolbar`.
- `align: end` places a button at the end of the toolbar, outside the scrolling row and before the
  search box, and its menu opens aligned to its right edge.

<<< @/../app/Controllers/Reference/ListsController/ToolbarExamples.php#docs-grouped-toolbar{php}

<<< @/../resources/ui/reference/lists/toolbar/grouped.yaml

<<< @/../resources/views/reference/lists/toolbar/_toolbar_status.php

## Extending buttons {#extend-buttons}

The toolbar fires `toolbar.extendButtons` once it has defined its buttons, before it renders them. A
listener bound to one toolbar with `bindEvent()`, or a `toolbarExtendButtons($toolbar)` method on the
controller for every toolbar it builds, receives the toolbar and can change its buttons:

- `addButtons($buttons, before: 'name')` places new buttons before the named one, and `after:` after
  it. Without either, they go at the end, and a button with the name of an existing one replaces it
  where it stands.
- A dotted name reaches an item of a menu: `after: 'actions.publish'` places the new item in the
  Actions menu, and `removeButton('actions.archive')` takes one out of it.
- `getButton()` returns a definition, a `ToolbarButton` object whose options change with fluent calls,
  as in `$toolbar->getButton('actions')->label('More')`. A definition with items also has `addItems()`
  and `removeItem()`.

<<< @/../app/Controllers/Reference/ListsController/ToolbarExamples.php#docs-extended-toolbar{php}

<<< @/../resources/ui/reference/lists/toolbar/extend.yaml

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

- `data-list-checked-trigger` keeps the element disabled until rows are checked, or with the value
  `single`, while any number other than one is checked. An element with `data-list-checked-counter`
  inside it shows how many are checked, in brackets.
- `data-list-checked-request` adds the ids of the rows checked on the page to the element's AJAX
  request as `checked`, which the handler reads with `input('checked')`.

A button definition with `checked: true` sets both attributes, the second only when the button has a
`handler`, and `checked: single` sets the trigger to `single`. The `Ui` factory takes them as named arguments, `dataListCheckedTrigger: true` and
`dataListCheckedRequest: true`, as the partial above does.

## Conditional buttons {#conditional-buttons}

`visible: false` hides a button. `visible` can also name a controller method, which the toolbar calls
with the button definition when it renders, showing the button when the method returns `true`. A
method that does not exist throws an error. `permissions` hides the button from visitors without one
of the named gate abilities.

`context` shows a button only on a toolbar made with a matching `context`, given as a string or a list
of them, as [form fields](/forms/fields) do. A button with a `context` is hidden on a toolbar without
one. These options work on menu items too. For changes that involve several buttons, see
[extending buttons](#extend-buttons).

## Toolbar options {#toolbar-options}

`Toolbar::make()` takes these options as named arguments.

Option | Description
------------- | -------------
**alias** | the widget's name, unique on the page.
**buttons** | the buttons, as a YAML file path or an array of [button definitions](#button-definitions), or the path of a partial that renders them.
**search** | the [search options](/lists/search#options) for a search box, or a partial to render in its place. Without it there is no search box.
**setupHandler** | the handler that opens the list setup, usually `$list->getEventHandler('onLoadSetup')`. It shows the setup icon.
**context** | shows only the buttons whose `context` matches, as described in [conditional buttons](#conditional-buttons).

`bindToListWidget($list)` links the toolbar to a list: its buttons follow the list's checked rows, and
its search box sets the list's search term and refreshes the list when the term changes.

## Button definitions {#button-definitions}

Buttons and menu items take the same options. An entry given as the word `divider`, in place of its
options, draws a line.

Option | Description
------------- | -------------
**label** | the button text, passed through the translator.
**icon** | an [icon](/extending/icons) name or CSS classes, such as `ph ph-plus`.
**type** | the style: `default`, `primary`, `secondary`, `danger`, `success`, `warning` or `info`. On a menu item, it colors the item. Default: `default`.
**outline** | uses the outline variant of the style. Default: `false`.
**url** | makes the button a link to this address.
**handler** | makes the button run this AJAX handler.
**popup** | with `handler`, opens the handler's response in a popup. Default: `false`.
**confirm** | a message to confirm before the request runs.
**checked** | links the button to the checked rows of the bound list: `true` for any rows, or `single` for exactly one. Items of a checked menu send the rows. Default: `false`.
**items** | the [items of a menu](#menus), opened by the button. With a `url` or `handler`, the button is a [split button](#split-buttons). On a menu item, they open a submenu.
**group** | with `items`, shows them side by side as a [button group](#groups-alignment). Default: `false`.
**caret** | shows the caret on a button with a menu. Default: `true`.
**header** | makes the entry a label for the menu items below it.
**divider** | makes the entry a line, the same as giving it as the word `divider`. Default: `false`.
**partial** | renders this partial of the controller in place of the button, with `$button` and `$toolbar`.
**align** | `end` places the button at the end of the toolbar.
**active** | marks the button as pressed, or a menu item as the current choice, as `true` or a controller method name. Default: `false`.
**disabled** | greys the button out, as `true` or a controller method name. Default: `false`.
**visible** | `false` hides the button, and the name of a controller method decides when it shows. Default: `true`.
**permissions** | gate abilities needed to see the button, as a string or an array where any one is enough.
**context** | the toolbar contexts the button shows in, as a string or an array.
**hotkey** | a key combination that clicks the button, such as `shift+p`.
**requestData** | data sent with the AJAX request.
**attributes** | HTML attributes added to the button, such as `data-request-flash: true`.
