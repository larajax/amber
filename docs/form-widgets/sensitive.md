---
subtitle: Keep a stored secret, such as an API key, off the page until revealed.
demo: /reference/form-widgets/sensitive
---
# Sensitive

The `sensitive` field keeps a stored secret, such as an API key, off the page. It renders a placeholder
in place of the value, fetches the real value over AJAX only when it is revealed, and keeps the stored
value when the form saves with the placeholder unchanged.

## Masked value {#default}

When a value is stored, the password input holds `__hidden__` rather than the secret. The eye button
calls the field's `onShowValue` handler and shows the answer, and clicking it again masks the value.
Typing clears the placeholder for a new value, and saving the form with the placeholder still in place
keeps the stored value. With no value stored, the input starts empty and shows the field's
`placeholder`.

`hiddenPlaceholder` changes the placeholder text, which is only ever compared with what the form posts,
so pick something nobody would type as a real value.

The reveal handler returns the value to anyone who can load the form, so protect the page as you would
the secret itself.

<<< @/../resources/ui/reference/form-widgets/sensitive/default.yaml

## Copy button {#allow-copy}

`allowCopy` adds a button that copies the value to the clipboard. When the value has not been revealed
yet, the button fetches it first, copies it, and masks it again.

<<< @/../resources/ui/reference/form-widgets/sensitive/allow-copy.yaml

## Textarea mode {#textarea}

`mode: textarea` is for multi-line secrets, such as private keys. It shows a row of dots in place of the
value, with Reveal Contents (and Copy to Clipboard, with `allowCopy`) below it. `size` sets the textarea
height.

<<< @/../resources/ui/reference/form-widgets/sensitive/textarea.yaml

## Read-only {#read-only}

A `readOnly` field disables the input but keeps the reveal and copy buttons, so the value can be looked
at but not changed. The input posts nothing, so saving the form leaves the stored value alone. A
`disabled` field behaves the same way.

<<< @/../resources/ui/reference/form-widgets/sensitive/read-only.yaml

## Hiding on tab change {#tab-change}

A revealed value masks itself again when the browser tab is hidden, so it is not left on screen while
you are away. `hideOnTabChange: false` leaves it revealed.

<<< @/../resources/ui/reference/form-widgets/sensitive/tab-change.yaml

## Options

Option | Description
------------- | -------------
**mode** | `text` for a password input, or `textarea` for a multi-line value. Default: `text`.
**allowCopy** | adds a button that copies the value to the clipboard. Default: `false`.
**hiddenPlaceholder** | the text the input holds in place of a stored value. Default: `__hidden__`.
**hideOnTabChange** | masks a revealed value when the browser tab is hidden. Default: `true`.

The field also takes the common [field properties](/forms/fields#field-properties), such as
`placeholder`, `readOnly` and `disabled`.
