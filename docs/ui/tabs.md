---
subtitle: Tabbed panes from plain markup.
demo: /reference/ui/tabs
---
# Tabs

The `tab` control works on any `control-tabs` element: a `nav-tabs` list of tabs and a `tab-content`
element with one `tab-pane` per tab, matched by their order. A class on the element picks the style.
Forms use the same control for their tabs, set up from the fields YAML (see
[Layout](/forms/layout)).

## Primary tabs {#tabs-primary}

`primary-tabs` is the main style, for the top level sections of a page. Mark the open tab's `li` and
its pane with `active`; the control moves the class as tabs are picked. Each tab is an `<a>` element
inside its `li`.

`data-linkable` keeps the open tab in the address: picking a tab writes the anchor's `href` hash, such
as `#tab-activity`, to the address bar, and a page opened with that hash opens the tab again, so a
reload or a shared link lands on it. Without `data-linkable`, the anchors can use `href="javascript:;"`.

<<< @/../resources/views/reference/ui/examples/tabs-primary.blade.php{blade}

A tab anchor with `data-tab-url` writes that address to the address bar when its tab is picked, with or
without `data-linkable`.

## Secondary tabs {#tabs-secondary}

`secondary-tabs` gives a lighter style, for a smaller group of panes inside a page.

<<< @/../resources/views/reference/ui/examples/tabs-secondary.blade.php{blade}

## Pill tabs {#tabs-pill}

`pill-tabs` joins the tabs into a segmented control, which suits switching between views of the same
data, such as a week, month and year.

<<< @/../resources/views/reference/ui/examples/tabs-pill.blade.php{blade}

## Underline tabs {#tabs-underline}

`underline-tabs` marks the open tab with a line under its title, for a light row of sections.

<<< @/../resources/views/reference/ui/examples/tabs-underline.blade.php{blade}

## Overflowing tabs {#tabs-overflow}

Tabs that do not fit their row scroll sideways, by dragging or with the mouse wheel, and a small arrow
at each edge shows there are more tabs that way. There is nothing to set up: every tab control does
this with the same helper as the [drag scroll](/lists/toolbar#drag-scroll) control. Opening a tab from
JavaScript scrolls the row to it.

<<< @/../resources/views/reference/ui/examples/tabs-overflow.blade.php{blade}

## Closable tabs {#tabs-closable}

`data-closable` adds a close button to each tab, and a middle click closes a tab too. When the open tab
closes, the tab before it opens.

The control's `addTab()` method opens a new tab, given a title and the pane content as HTML. Fetch the
control with `jax.fetchControl(element, 'tab')`, as the Add a tab button does. Scripts inside the
content run when the pane is added.

<<< @/../resources/views/reference/ui/examples/tabs-closable.blade.php{blade}

## Modified tabs {#tabs-modified}

A `tab:modified` event dispatched from inside a pane marks its tab as modified, which swaps the close
button for a dot. With `data-close-confirmation`, closing a modified tab asks first, with that text,
and stays open when the visitor cancels. A `tab:unmodified` event clears the mark. Both events must
bubble, as in the example.

<<< @/../resources/views/reference/ui/examples/tabs-modified.blade.php{blade}

## Data attributes {#data-attributes}

Set these on the `control-tabs` element.

Attribute | Description
------------- | -------------
**data-control="tab"** | enables the control.
**data-linkable** | keeps the open tab in the address hash, and opens the tab matching the hash on page load.
**data-closable** | adds a close button to each tab.
**data-close-confirmation** | the question to ask before a modified tab closes.
**data-pane-classes** | classes added to the panes that `addTab()` creates.
**data-title-as-file-names** | shows only the file name part of the titles that `addTab()` and `updateTab()` set.
**data-max-title-symbols** | shortens those titles to this many characters, keeping the end.

On a tab, `data-tab-id` gives it an identifier for `goTo()`, and `data-tab-url` on its anchor sets the
address shown when it opens.

When an AJAX request fails validation, the control opens the tab that holds the first invalid field
and focuses the field.

## JavaScript API {#javascript-api}

Fetch the control with `jax.fetchControl(element, 'tab')`, then call:

Method | Description
------------- | -------------
**addTab(title, content, identifier, tabClass)** | adds a tab and opens it. `content` is HTML or an element, and the optional `identifier` sets its `data-tab-id`.
**updateTab(tab, title, content)** | replaces a tab's title and content.
**closeTab(tab, force)** | closes a tab. `force` skips the close confirmation.
**modifyTab(tab)** | marks a tab as modified.
**unmodifyTab(tab)** | clears the modified mark.
**goTo(identifier)** | opens the tab with this `data-tab-id`.
**goToIndex(index)** | opens the tab at this position, from `0`.
**goToPane(pane)** | opens the tab of this pane.
**goToElement(element)** | opens the tab that contains the element.
**prev()** / **next()** | opens the tab before or after the open one.

A `tab` argument is a tab's `li`, its pane, or a selector of either inside the control.

## Events {#events}

Event | Description
------------- | -------------
**tab:shown** | dispatched on a tab's anchor after its tab opens.
**tab:shown-linkable** | dispatched on the anchor of the tab opened from the address hash.
**tab:init-tab** | dispatched on the control after a tab is set up, with `detail.tab` and `detail.pane`.
**tab:before-close** | dispatched on the control before a tab closes, with the pane as `detail.relatedTarget`. `preventDefault()` keeps it open.
**tab:closed** | dispatched on the control after a tab closes, with `detail.tab` and `detail.pane`.
**tab:after-all-closed** | dispatched on the control when its last tab closes.

Dispatch these, with `bubbles: true`, on a tab, a pane or any element inside them:

Event | Description
------------- | -------------
**tab:close** | closes the tab. `detail: { force: true }` skips the close confirmation.
**tab:modified** | marks the tab as modified.
**tab:unmodified** | clears the modified mark.
