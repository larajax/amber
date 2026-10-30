---
subtitle: Semantic icon keys, icon packs and how to swap them.
demo: /reference/extending/icons
---
# Icons

Widgets name their icons with semantic keys, such as `action.delete` or `search.icon`, and an icon
pack maps each key to icon classes. Swap the pack, or replace a few keys, and every widget follows,
with no change to their views. Your own views can use the same keys.

## Semantic keys {#keys}

`Ui::icon()` renders the `<i>` element for a key from the default pack, with `aria-hidden="true"` so
screen readers skip it. It returns a plain string, not an `Htmlable` object, so print it with
<span v-pre>`{!! !!}`</span>.

<<< @/../resources/views/reference/extending/examples/icon-keys.blade.php{blade}

## Attributes and plain classes {#attributes}

The second argument adds attributes to the element. Its `class` merges with the icon's own classes,
and setting `aria-hidden` to `false` with an `aria-label` makes screen readers announce an icon that
carries meaning on its own.

A name that is not a key passes through as icon classes, so `ph ph-rocket-launch` works wherever a key
does. This is why the `icon` argument of buttons and callouts takes either. Set `fallback` to `false`
in the [configuration](#configuration) to render nothing for unknown names instead.

<<< @/../resources/views/reference/extending/examples/icon-attributes.blade.php{blade}

## Classes for your own markup {#icon-class}

`Ui::iconClass()` returns only the classes, for markup you write yourself, such as an icon inside a
button with your own attributes. Both methods take a pack name as their last argument, to use a pack
other than the default.

<<< @/../resources/views/reference/extending/examples/icon-class.blade.php{blade}

## Icon packs {#packs}

The library ships three packs: `phosphor` ([Phosphor Icons](https://phosphoricons.com), the default),
`bootstrap` ([Bootstrap Icons](https://icons.getbootstrap.com)) and `october`, each mapping every key.
A pack only maps keys to classes, so load the icon font itself in your layout.

<<< @/../resources/views/reference/extending/examples/icon-packs.blade.php{blade}

`Ui::registerIconPack()` adds a pack, or replaces one, by name. The pack is an array of keys and
classes, a class that implements `Larajax\Ui\Classes\IconPack` (or its name), or a closure that returns
the array. A closure or class name is only resolved when the pack is first used. This page registers
an `amber` pack built on the phosphor pack, with three keys swapped:

<<< @/../app/Controllers/Reference/ExtendingController/IconRegistrations.php{php}

The page registers its pack in its action, so it applies to that page only. An app registers its packs
once, in a service provider's `boot()` method.

To make a pack the default, set `LARAJAX_ICONS` in your `.env` file, such as `LARAJAX_ICONS=bootstrap`.

## Adding keys to every pack {#extend}

`Ui::extendIcons()` adds or replaces keys in every pack. It takes an array, which applies to every
pack alike, or a closure that receives each pack's icons and name and returns the icons to use. The
method above adds two status keys with an array, then gives the bootstrap pack its own glyphs for them
with a closure.

<<< @/../resources/views/reference/extending/examples/icon-extend.blade.php{blade}

Extensions apply after the pack and the configured `overrides`, in the order they were added.

## Every key in the default pack {#all-keys}

The demo page lists every key of the default pack with its icon, including the two that page adds,
from the icon manager's `icons()` method, which returns a pack's resolved keys and classes:

<<< @/../resources/views/reference/extending/examples/icon-pack-keys.blade.php{blade}

The keys are grouped by the widget that uses them:

Keys | Used by
------------- | -------------
`action.*` | shared actions, such as `action.delete`.
`search.*`, `dropdown.*`, `tooltip.*` | the search input, dropdown carets and field tooltips.
`callout.*` | the callout types: `info`, `success`, `warning` and `danger`.
`filter.*` | the filter widget.
`list.*`, `pagination.*` | the list widget: reordering, setup, tree rows, sorting, switch columns and paging.
`checkboxlist.*`, `datepicker.*`, `sensitive.*` | the checkbox list field, the date picker and the sensitive field.
`fileupload.*`, `file.type.*` | the file upload widget, with an icon per file type.
`recordfinder.*`, `repeater.*`, `datatable.*` | the record finder, repeater and data table widgets.
`codeeditor.*`, `markdowneditor.*`, `richeditor.*` | the editor toolbars.

## Configuration {#configuration}

The icon settings live in `config/larajax-icons.php`. Publish the file to change them:

```bash
php artisan vendor:publish --tag=larajax-ui-config
```

Option | Description
------------- | -------------
**default** | the pack that `Ui::icon()`, `Ui::iconClass()` and the widgets use. Default: the `LARAJAX_ICONS` environment value, or `phosphor`.
**fallback** | passes names that are not keys through as icon classes. Default: `true`.
**packs** | the packs by name, each an `IconPack` class name or an array of keys and classes.
**overrides** | keys and classes that replace those keys in every pack.

## Methods {#methods}

Method | Description
------------- | -------------
**Ui::icon($icon, $attributes, $pack)** | renders the `<i>` element for a key or icon classes, as a string.
**Ui::iconClass($icon, $pack)** | returns the classes for a key, or the name itself when it is not a key.
**Ui::registerIconPack($name, $pack)** | adds or replaces a pack.
**Ui::extendIcons($extension)** | adds or replaces keys in every pack, from an array or a closure.
