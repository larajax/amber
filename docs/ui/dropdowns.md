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
send values with `dataRequestData`. The `class` argument merges into the item's `menu-item` class:

- `disabled` greys an item out. Add `ariaDisabled: 'true'` so screen readers know it too.
- A theme class, such as `theme-danger`, colors an item, hover included, for destructive actions.

<<< @/../resources/views/reference/ui/examples/dropdown-items.blade.php{blade}

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
**caret** | shows the caret after the label. Default: `true`.

Any other argument becomes an attribute on the toggle button, as on [buttons](/ui/buttons#attributes).

### Ui::dropdownItem() {#dropdown-item-arguments}

Argument | Description
------------- | -------------
**label** | the item text, escaped.
**handler** | an AJAX handler to run when the item is clicked.
**href** | an address to follow. Default: `javascript:;`.
**icon** | icon classes, rendered before the label.

Any other argument becomes an attribute on the item, with `class` merged into `menu-item`.

### Ui::dropdownDivider() {#dropdown-divider-arguments}

Takes no arguments, and renders `<hr class="menu-divider">`.
