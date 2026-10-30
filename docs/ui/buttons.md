---
subtitle: Render buttons, links and AJAX buttons with the Ui facade.
demo: /reference/ui/buttons
---
# Buttons

The `Ui` facade renders buttons from your views: plain buttons and links, buttons that run an AJAX
handler, icon-only buttons and buttons with a keyboard shortcut. Each call takes PHP named arguments,
and any argument the button does not know becomes an HTML attribute.

Every `Ui` call returns an object that implements Laravel's `Htmlable` contract, so Blade's escaped
<span v-pre>`{{ }}`</span> output prints it as HTML and you do not need <span v-pre>`{!! !!}`</span>.
The call is the same in every view, with no component tags to learn: the arguments are the named
arguments of a static method, so your editor can suggest them as you type.

## Variants {#variants}

A styling flag picks the look: `primary`, `secondary` or `danger` render Bootstrap's solid button
(`btn-solid`) in that theme. Without a flag, the button renders Bootstrap's plain `btn` class. When you
pass more than one flag, the first in that order wins.

<<< @/../resources/views/reference/ui/examples/button-variants.blade.php{blade}

## Outline, subtle and link {#outline}

The `class` argument merges extra classes into the button's own, so any Bootstrap variant and theme
works. Leave the styling flag out and pass the variant and theme classes yourself, such as
`btn-outline theme-primary`, `btn-subtle theme-primary` or `btn-link`.

<<< @/../resources/views/reference/ui/examples/button-outline.blade.php{blade}

## Sizes {#sizes}

Sizes go through `class` too: `btn-xs`, `btn-sm` and `btn-lg`, next to the default. The same classes on
a `btn-group`, as `btn-group-sm` or `btn-group-lg`, size every button inside it.

<<< @/../resources/views/reference/ui/examples/button-sizes.blade.php{blade}

## With an icon {#icons}

The `icon` argument renders an icon before the label. It takes icon classes, such as
`ph ph-floppy-disk`, or a key from the icon pack, such as `action.delete`, which follows the pack your
app uses. See [Icons](/extending/icons) for the keys and packs.

<<< @/../resources/views/reference/ui/examples/button-icons.blade.php{blade}

## Links {#links}

An `href` renders an `<a>` element styled as a button, in place of a `<button>`. Pass `target` and
`rel` as you would on any link, since they are attributes.

<<< @/../resources/views/reference/ui/examples/button-links.blade.php{blade}

## Attributes {#attributes}

Any argument the button does not know becomes an attribute on the element, with its camel case name
written in kebab case: `dataRequestFlash` renders `data-request-flash` and `ariaLabel` renders
`aria-label`. The values follow these rules:

- A data attribute set to `true` renders as `"true"`, which the framework reads as on.
- An attribute set to `false` is left out.
- Any other attribute set to `true`, such as `disabled`, repeats its name: `disabled="disabled"`.
- `class` merges with the button's own classes, while any other attribute replaces the button's
  value. Buttons render `type="button"`, so pass `type: 'submit'` for a submit button.

<<< @/../resources/views/reference/ui/examples/button-attributes.blade.php{blade}

## Groups and text {#groups}

A `btn-group` element joins the buttons inside it into one bar. A `button-text` span sets muted text
beside a button, such as an "or Cancel" link next to a save button, and styles a link inside it to
match.

<<< @/../resources/views/reference/ui/examples/button-groups.blade.php{blade}

## AJAX button {#ajax-button}

`Ui::ajaxButton()` runs a handler on the controller when clicked, through Larajax's `data-request`
attribute. `requestData` sends extra values with the request, as an array that the button encodes as
JSON, and the handler reads them with `input()`. Since other arguments are attributes, the Larajax
request attributes work as they are: `dataRequestConfirm` asks first and runs the request only when
the visitor accepts, and `dataRequestFlash` shows the flash messages the handler returns.

<<< @/../resources/views/reference/ui/examples/ajax-buttons.blade.php{blade}

The handlers live in a trait on the page's controller:

<<< @/../app/Controllers/Reference/UiController/ButtonHandlers.php{php}

See [AJAX & Larajax](/guides/ajax) for how handlers are found and what they can return.

## Loading state {#loading}

`dataAttachLoading` disables the button and shows a spinner on it while its request runs, so the
visitor cannot send it twice. The handler here sleeps for a moment so you can see it.

<<< @/../resources/views/reference/ui/examples/button-loading.blade.php{blade}

## Icon button {#icon-button}

`Ui::iconButton()` renders the icon alone, as a `btn-icon-link` button with the icon classes on the
button itself. The `label` becomes its `title`, which the browser shows on hover and screen readers
announce. `danger` turns the icon red on hover, for destructive actions. With a `handler` it runs that
handler, and it takes the same request attributes as an AJAX button.

<<< @/../resources/views/reference/ui/examples/icon-buttons.blade.php{blade}

`btn-icon-link` is the library's own class. Bootstrap 6's `btn-icon` is a different, square button.

## Hotkeys {#hotkeys}

A `hotkey` clicks the button from the keyboard. It takes one shortcut as a string, such as
`'alt+shift+d'`, or several as an array, such as `['ctrl+s', 'cmd+s']` so the same shortcut works on
Windows and macOS. Hovering the button then shows a tooltip with the label and the shortcut, written
with key symbols: `ctrl` shows as ⌃, `cmd` as ⌘, `shift` as ⇧, `alt` as ⌥ and `enter` as ↵, and other
keys in capitals. Try <kbd>Ctrl</kbd> + <kbd>S</kbd> on the demo page.

<<< @/../resources/views/reference/ui/examples/hotkey-buttons.blade.php{blade}

Every button kind takes `hotkey`. It renders the `data-hotkey` attribute of the
[input hotkey](/ui/inputs#input-hotkey) control, which lists the keys you can use, and the
`data-tooltip-*` attributes of the [tooltip](/ui/popups#tooltip).

## Fluent props {#fluent}

Methods chained on the result set the same props as the named arguments: `primary()`, `icon()`,
`handler()`, `requestData()` and so on. A method called with no argument sets its prop to `true`.
Chained methods only set props, so attributes such as `dataRequestFlash` still go in the named
arguments. Since `label` is the first argument, `Ui::button('Publish')` works without its name.

<<< @/../resources/views/reference/ui/examples/button-fluent.blade.php{blade}

## Arguments {#arguments}

### Ui::button() {#button-arguments}

Argument | Description
------------- | -------------
**label** | the button text, escaped.
**href** | renders an `<a>` link with this address in place of a `<button>`.
**icon** | icon classes or an icon pack key, rendered before the label.
**primary** | primary styling, `btn-solid theme-primary`. Default: `false`.
**secondary** | secondary styling, `btn-solid theme-secondary`. Default: `false`.
**danger** | danger styling, `btn-solid theme-danger`. Default: `false`.
**hotkey** | a keyboard shortcut, or an array of them, that clicks the button and shows in a tooltip.

Any other argument becomes an attribute, as described in [Attributes](#attributes).

### Ui::ajaxButton() {#ajax-button-arguments}

Takes the arguments of `Ui::button()` except `href`, plus:

Argument | Description
------------- | -------------
**handler** | the AJAX handler to run, such as `onSave`, rendered as `data-request`.
**requestData** | an array of extra values to send with the request, rendered as `data-request-data`.

### Ui::iconButton() {#icon-button-arguments}

Argument | Description
------------- | -------------
**label** | the button's `title`, shown on hover and read by screen readers.
**icon** | icon classes or an icon pack key, set on the button itself.
**handler** | an AJAX handler to run when clicked.
**danger** | turns the icon red on hover. Default: `false`.
**hotkey** | a keyboard shortcut, or an array of them, that clicks the button.

`Ui::popupButton()` opens a handler's response in a dialog, and its arguments are listed on the
[Popups](/ui/popups#popup-button-arguments) page.
