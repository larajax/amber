---
subtitle: Render buttons, inputs and callouts with the Ui facade.
---
# UI Elements

Amber owns the **`Ui` facade** — a small factory for rendering common UI elements (buttons, search
inputs, dropdowns, callouts) from your views. Each call returns a renderable object, so you drop it
straight into Blade.

```blade
{{ Ui::button(label: 'Save', primary: true) }}
```

The objects returned by `Ui` implement Laravel's `Htmlable` contract, so Blade's escaped <span v-pre>`{{ }}`</span> output
renders them as raw HTML. You do **not** need `{!! !!}`.

## Buttons

### Basic button

`Ui::button()` renders a plain button. Use PHP named arguments for readability:

```blade
{{ Ui::button(label: 'Save', primary: true) }}
```

Common arguments:

Argument | Description
------------- | -------------
**label** | the button text.
**primary** | primary styling (`btn-primary`).
**secondary** | secondary styling (`btn-secondary`).
**danger** | danger styling (`btn-danger`).
**icon** | an icon class rendered before the label.
**href** | render an `<a>` link instead of a `<button>`.
**hotkey** | a keyboard shortcut, e.g. `'ctrl+s'` or `['ctrl+s', 'cmd+s']`.

Pass a `href` to render a link styled as a button:

```blade
{{ Ui::button(label: 'Back', href: '/users', secondary: true) }}
```

Add a keyboard shortcut with `hotkey`. Amber renders the platform key symbols in the tooltip
automatically (for example `ctrl+s` shows as `⌃S`):

```blade
{{ Ui::button(label: 'Save', primary: true, hotkey: ['ctrl+s', 'cmd+s']) }}
```

### AJAX button

`Ui::ajaxButton()` wires the button to a [Larajax](https://larajax.org) AJAX handler. This is the button
you want when saving a form without a full page reload:

```blade
{{ Ui::ajaxButton(label: 'Save', handler: 'onSave', primary: true) }}
```

Argument | Description
------------- | -------------
**handler** | the AJAX handler to run, e.g. `'onSave'`.
**requestData** | extra data to send with the request, as an array.

```blade
{{ Ui::ajaxButton(
    label: 'Delete',
    handler: 'onDelete',
    danger: true,
    requestData: ['id' => $user->id]
) }}
```

### Popup button

`Ui::popupButton()` opens the handler's response in a popup — a modal dialog built on Bootstrap 5.
The handler returns the modal contents (header/body/footer markup), and elements inside carrying
`data-dismiss="popup"` close it:

```blade
{{ Ui::popupButton(label: 'Edit', handler: 'onEditForm') }}
```

Pass a size through the attribute bag: `dataSize: 'large'` (sizes: `small`, `large`, `huge`, `giant`).

### Icon button

`Ui::iconButton()` renders an icon-only button (the label becomes its accessible title):

```blade
{{ Ui::iconButton(label: 'Refresh', icon: 'icon-refresh', handler: 'onRefresh') }}
```

## Dropdowns

Dropdowns are slot-based: open the button with `->slot()`, echo the items, then close with `Ui::end()`.
Because the content is captured rather than returned, use `<?php ... ?>` tags around the open and close
calls:

```blade
<?php Ui::dropdownButton(label: 'Actions', secondary: true)->slot() ?>
    {{ Ui::dropdownItem(label: 'Edit', handler: 'onEdit') }}
    {{ Ui::dropdownItem(label: 'Duplicate', handler: 'onDuplicate') }}
    {{ Ui::dropdownDivider() }}
    {{ Ui::dropdownItem(label: 'Delete', handler: 'onDelete') }}
<?php Ui::end() ?>
```

## Search input

`Ui::searchInput()` renders a search field. Give it a `handler` to fire an AJAX request as the user
types (debounced automatically):

```blade
{{ Ui::searchInput(name: 'q', placeholder: 'Search users...', handler: 'onSearch') }}
```

## Callouts

`Ui::callout()` renders a highlighted message block. It uses a fluent (chained) API rather than named
arguments:

```blade
{{ Ui::callout()->label('Heads up')->comment('This action cannot be undone')->warning() }}
```

The type methods set the callout style:

Method | Style
------------- | -------------
`->tip()` | informational (default)
`->success()` | success
`->warning()` | warning
`->danger()` | danger

## A note on syntax

Amber deliberately keeps the same call for every engine rather than wrapping these in component tags:

- **Blade** — call the facade directly: <span v-pre>`{{ Ui::button(label: 'Save', primary: true) }}`</span>.
- **Twig** *(when supported)* — the same element via a markup function taking an options hash:
  <span v-pre>`{{ ui_button({ label: 'Save', primary: true }) }}`</span>.

Both share one underlying `Ui` implementation, so the argument names and behavior are identical across
engines — only the surrounding call syntax differs, staying idiomatic to each.
