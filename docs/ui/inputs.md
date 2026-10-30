---
subtitle: Searchable selects, tri-state checkboxes and keyboard shortcuts.
demo: /reference/ui/inputs
---
# Inputs

Controls that enhance plain inputs from markup: searchable selects, tri-state checkboxes, range
selection for a list of checkboxes, and keyboard shortcuts. Each one starts from a class or a data
attribute, with no script of your own, and form fields use the same controls.

## Custom select {#custom-select}

A `select` with the `custom-select` class becomes a searchable dropdown, built on
[Select2](https://select2.org). It brings its own copy of jQuery, so the page needs none. The select
keeps its name and value, and still dispatches `change`, so forms and your own listeners read it as a
plain select.

`select-no-search` hides the search box, for a short list. Option groups (`<optgroup>`) carry over as
headings in the dropdown.

<<< @/../resources/views/reference/ui/examples/custom-select.blade.php{blade}

Class | Description
------------- | -------------
**custom-select** | enables the control.
**select-no-search** | hides the search box.
**select-hide-selected** | hides the options already selected, in a multiple select.
**select-dropdown-auto-width** | sizes the dropdown to its options, in place of the select's width.
**form-control-sm** / **form-control-lg** | a smaller or larger select, with its dropdown to match.

An `<option>` can carry a `data-status` color, shown as a dot, a `data-icon` with icon classes, or a
`data-image` address, shown before its text. Inside a popup, the dropdown opens inside the dialog, so it
shows above the backdrop.

## Custom select with a handler {#custom-select-ajax}

`data-handler` loads the options from the server as the visitor types, once
`data-minimum-input-length` characters are in the search box. The control sends the search text as
`term`, and the handler returns the matching options in a `results` array of `id` and `text` pairs.
`data-placeholder` shows a hint while nothing is picked and adds a button to clear the choice.

<<< @/../resources/views/reference/ui/examples/custom-select-ajax.blade.php{blade}

<<< @/../app/Controllers/Reference/UiController/InputHandlers.php{php}

Each result can also set `disabled` or `selected`, or `status`, `icon` and `image` as the option data
attributes do. A result with `text` and a `children` array of results is an option group, and a
`pagination` key with `more: true` makes the dropdown ask for the next page as the visitor scrolls,
sending its number as `page`. A plain array of values keyed by id works too, though JavaScript does not
keep the order of its keys, so prefer the list. The results can also go under a `result` key.

Attribute | Description
------------- | -------------
**data-handler** | the AJAX handler that returns the options.
**data-minimum-input-length** | how many characters to type before the handler runs.
**data-ajax--delay** | how long to wait after the last key press before asking, in milliseconds.
**data-request-data** | extra values sent with each request, as `key: value` pairs.
**data-placeholder** | a hint shown while nothing is picked, with a button to clear the choice.
**data-token-separators** | characters, separated by `\|`, that turn the typed text into a new tag, for a select that accepts its own values.
**data-language** | the language of Select2's own messages.

Select2 reads its other options from `data-*` attributes as well, as described in its documentation.

## Tri-state checkbox {#checkbox}

A `form-field` with the `is-indeterminate` class holds a checkbox with three states, which a click
cycles through: unchecked, mixed and checked. `data-checked` on the input sets the starting state:
`0` unchecked, `1` mixed and `2` checked. Without it, the input's `checked` attribute decides.

<<< @/../resources/views/reference/ui/examples/checkbox-tristate.blade.php{blade}

The control updates `data-checked` on each click before the input's `change` event fires, so a
`change` listener reads the new state from `input.dataset.checked`. A mixed checkbox is never
`checked`, so it is left out of the posted form, like an unchecked one.

## Checkbox range selection {#checkbox-range}

Hold <kbd>Shift</kbd> and click a checkbox to set every checkbox between it and the one clicked before
it, to the state that first checkbox was left in. Call `jax.checkboxRangeRegisterClick()` from your own
click listener with the event, a selector for the element around each checkbox and a selector for the
checkbox inside it. The elements around the checkboxes must be siblings, such as the `li` items of one
list or the rows of a table. Each checkbox it sets dispatches `change`.

<<< @/../resources/views/reference/ui/examples/checkbox-range.blade.php{blade}

The list widget uses the same helper for its row checkboxes.

Argument | Description
------------- | -------------
**ev** | the click event.
**containerSelector** | a selector for the element around each checkbox, such as `li` or `tr`.
**checkboxSelector** | a selector for the checkbox inside it.

## Input hotkey {#input-hotkey}

`data-hotkey` clicks any element from the keyboard, so the shortcut does what a click does, whether
the element runs a handler or follows a link. Separate several shortcuts with commas, such as
`ctrl+s, cmd+s`. The `hotkey` argument of the [Ui buttons](/ui/buttons#hotkeys) renders this attribute.

<<< @/../resources/views/reference/ui/examples/input-hotkey.blade.php{blade}

A shortcut is one key, with any of the modifiers before it, joined by `+`. The modifiers must match
exactly, so `alt+shift+k` does not fire for <kbd>Ctrl</kbd> + <kbd>Alt</kbd> + <kbd>Shift</kbd> +
<kbd>K</kbd>.

- **Modifiers**: `shift`, `ctrl`, `alt` (or `option`) and `cmd` (or `command` or `meta`).
- **Keys**: a letter or digit, `esc`, `tab`, `space`, `enter` (or `return`), `backspace`, `delete`,
  `insert`, `home`, `end`, `pageup`, `pagedown`, `left`, `up`, `right`, `down`, and `f1` to `f12`.

The shortcut only fires while the element is visible, so a hotkey on a hidden tab or a closed menu
does nothing. While a popup is open, only the hotkeys inside the top popup fire.

Attribute | Description
------------- | -------------
**data-hotkey** | the shortcuts that click the element, separated by commas.
**data-hotkey-visible** | `false` fires the shortcut even while the element is hidden. Default: `true`.
**data-hotkey-target** | a selector for the element that listens for the keys. Default: `html`.
