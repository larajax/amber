---
subtitle: Dropdown menus from the Ui facade or from plain markup.
demo: /reference/ui/dropdowns
---
# Dropdowns

Dropdown menus are Bootstrap 6 menus, opened by Bootstrap's own `data-bs-toggle="menu"` data API, so
the Bootstrap JavaScript module must be on the page. The `dropdown` control is CSS only: it gives the
menus the library look. `Ui::dropdownButton()` renders the toggle and the menu together, or you write
the menu markup yourself.

## Dropdown button {#dropdown-button}

`Ui::dropdownButton()` is slot-based: open it with `->slot()`, print the items, and close it with
`Ui::end()`, which renders the button around everything printed in between. Because the items are
captured rather than returned, the opening and closing calls go in `<?php ?>` tags, while the items
print with <span v-pre>`{{ }}`</span> as usual.

`Ui::dropdownItem()` runs a handler when it has a `handler`, or follows a link when it has an `href`,
and `Ui::dropdownDivider()` draws a line between groups of items. Items take any attribute, so here
`dataRequestData` tells the handler which item was clicked.

<<< @/../resources/views/reference/ui/examples/dropdown-buttons.blade.php{blade}

<<< @/../app/Controllers/Reference/UiController/DropdownHandlers.php{php}

The output is a `btn-group` holding the toggle and a `menu control-dropdown` panel. The toggle sets
`data-bs-strategy="fixed"`, so the menu opens against the viewport and is not clipped by a scrolling
or `overflow: hidden` container around it.

## Icon and caret {#dropdown-caret}

The `icon` argument shows an icon before the label, and `caret: false` hides the arrow after it. A
button with an icon and no caret suits a "more actions" menu. The caret is the icon pack's
`dropdown.caret` key.

<<< @/../resources/views/reference/ui/examples/dropdown-caret.blade.php{blade}

## Item options {#dropdown-items}

Items take any attribute, as buttons do, so they can ask for confirmation with `dataRequestConfirm` or
send values with `dataRequestData`.

- `disabled: true` greys an item out and sets `aria-disabled`, so screen readers know it too.
- A theme class in the `class` argument, such as `theme-danger`, colors an item, hover included, for
  destructive actions. The class merges into the item's `menu-item` class.

<<< @/../resources/views/reference/ui/examples/dropdown-items.blade.php{blade}

## Headers, checks and hotkeys {#dropdown-headers}

`Ui::dropdownHeader()` labels the items below it. An item with `selected: true` is the current choice,
shown with a check at its end, and `requestData` sends values with its request as an array.

`hotkey` binds a key combination that clicks the item while the menu is closed, here Shift+R, and the
item shows the keys at its end. With `popup: true`, the item opens what its handler returns in a
popup, as [popup buttons](/ui/popups) do.

<<< @/../resources/views/reference/ui/examples/dropdown-headers.blade.php{blade}

## Submenus {#dropdown-submenus}

`Ui::dropdownItem()` with a slot opens the items printed in it as a submenu, beside the menu. Open it
with `->slot()` and close it with `Ui::end()`, as the dropdown button around it. A submenu opens on
hover or click, and the arrow keys move into and out of it.

<<< @/../resources/views/reference/ui/examples/dropdown-submenus.blade.php{blade}

## Split button {#split-button}

`Ui::splitButton()` runs its own action, a `handler` or an `href`, beside a caret that opens the items
printed in its slot. Arguments the method does not name become attributes of the button, and
`toggleAttributes` sets attributes on the caret. The menu lines up with the caret's right edge.

<<< @/../resources/views/reference/ui/examples/dropdown-split.blade.php{blade}

## Menus in markup {#dropdown-markup}

Any toggle with `data-bs-toggle="menu"` opens the `menu` element placed right after it, so no
`data-bs-target` or wrapper is needed. Add `control-dropdown` to the menu for the library look, and
give the toggle `aria-expanded="false"`, which Bootstrap updates as the menu opens and closes.

Keep the items, `<a class="menu-item">` elements, as direct children of the menu: Bootstrap's arrow
key navigation only looks there. An `active` class marks the current choice, and
`<hr class="menu-divider">` separates groups.

<<< @/../resources/views/reference/ui/examples/dropdown-markup.blade.php{blade}

## Placement {#dropdown-placement}

`data-bs-placement` on the toggle picks the side and alignment of the menu. The menu opens below and
aligned to the toggle's left edge by default. `top-start` opens it above, and `bottom-end` lines it up
with the toggle's right edge, which suits a toggle at the end of a row.

<<< @/../resources/views/reference/ui/examples/dropdown-placement.blade.php{blade}

## Toggle attributes {#toggle-attributes}

These are Bootstrap's menu attributes, set on the toggle.

Attribute | Description
------------- | -------------
**data-bs-toggle="menu"** | opens the `menu` element that follows the toggle.
**data-bs-placement** | the side and alignment, such as `bottom-start`, `bottom-end`, `top-start` or `top-end`. Default: `bottom-start`.
**data-bs-strategy="fixed"** | positions the menu against the viewport, for a toggle inside a scrolling or `overflow: hidden` container. `Ui::dropdownButton()` sets it.
**data-bs-container="body"** | moves the menu to `<body>` while it is open. Avoid it inside a popup: the moved menu renders beneath the dialog, while a menu left in place works.

Bootstrap dispatches `show.bs.menu`, `shown.bs.menu`, `hide.bs.menu` and `hidden.bs.menu` on the
toggle. `shown` and `hidden` fire after the menu's transition, so change a menu's items in a
`show.bs.menu` listener, before it opens.

## Arguments {#arguments}

### Ui::dropdownButton() {#dropdown-button-arguments}

Argument | Description
------------- | -------------
**label** | the toggle text, escaped.
**icon** | icon classes, rendered before the label.
**primary** | primary styling, `btn-solid theme-primary`. Default: `false`.
**secondary** | secondary styling, `btn-solid theme-secondary`. Default: `false`.
**danger** | danger styling, `btn-solid theme-danger`. Default: `false`.
**success** | success styling, `btn-solid theme-success`. Default: `false`.
**warning** | warning styling, `btn-solid theme-warning`. Default: `false`.
**info** | info styling, `btn-solid theme-info`. Default: `false`.
**outline** | the outline variant, `btn-outline`, in the theme of the flag beside it, or `theme-secondary` without one. Default: `false`.
**caret** | shows the caret after the label. Default: `true`.

Any other argument becomes an attribute on the toggle button, as on [buttons](/ui/buttons#attributes).

### Ui::dropdownItem() {#dropdown-item-arguments}

Argument | Description
------------- | -------------
**label** | the item text, escaped.
**handler** | an AJAX handler to run when the item is clicked.
**href** | an address to follow. Default: `javascript:;`.
**icon** | icon classes, rendered before the label.
**popup** | with `handler`, opens the handler's response in a popup. Default: `false`.
**requestData** | an array of data sent with the request.
**hotkey** | a key combination that clicks the item, shown at its end.
**selected** | marks the item as the current choice, with a check. Default: `false`.
**disabled** | greys the item out. Default: `false`.

Any other argument becomes an attribute on the item, with `class` merged into `menu-item`. Given a
slot, the item opens a submenu of the items in it.

### Ui::dropdownHeader() {#dropdown-header-arguments}

Argument | Description
------------- | -------------
**label** | the header text, escaped.

Any other argument becomes an attribute on the header, with `class` merged into `menu-header`.

### Ui::dropdownDivider() {#dropdown-divider-arguments}

Takes no arguments, and renders `<hr class="menu-divider">`.

### Ui::splitButton() {#split-button-arguments}

Argument | Description
------------- | -------------
**label** | the button text, escaped.
**handler** | an AJAX handler the button runs.
**href** | an address the button follows, when there is no `handler`.
**icon** | icon classes, rendered before the label.
**popup** | with `handler`, opens the handler's response in a popup. Default: `false`.
**primary**, **secondary**, **danger**, **success**, **warning**, **info** | the style of both halves, as on the [dropdown button](#dropdown-button-arguments). Default: `false`.
**outline** | the outline variant. Default: `false`.
**hotkey** | a key combination that clicks the button.
**requestData** | an array of data sent with the request.
**toggleAttributes** | an array of attributes for the caret.

Any other argument becomes an attribute on the button.
