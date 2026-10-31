---
subtitle: Change the icons the library draws, with icon packs and key overrides.
demo: /reference/extending/icons
---
# Icons

The widgets name every icon they draw with a semantic key, such as `search.icon` for the search field or
`callout.info` for an info callout, and an icon pack maps each key to icon classes. To change the
library's icons, override a few keys or switch the whole pack, and every widget follows with no change
to its views.

Your own views don't need any of this: write icon markup with the classes directly, such as
`<i class="ph ph-rocket-launch"></i>`. Where a library component takes an `icon` argument, pass the
classes the same way.

## Overriding keys {#extend}

`Ui::extendIcons()` replaces keys in every pack. It takes an array, which applies to every pack alike,
or a closure that receives each pack's icons and name and returns the icons to use. The demo page swaps
the info callout's icon and the search field's icon with an array, then gives the bootstrap pack its own
glyphs for them with a closure, and the callout and search field below pick up the new icons:

<<< @/../resources/views/reference/extending/examples/icon-extend.blade.php{blade}

<<< @/../app/Controllers/Reference/ExtendingController/IconRegistrations.php{php}

The demo page registers these in its action, so they apply to that page only. An app overrides its icons
once, in a service provider's `boot()` method. Extensions apply after the pack and the configured
[`overrides`](#configuration), in the order they were added.

## Icon packs {#packs}

The library ships three packs: `phosphor` ([Phosphor Icons](https://phosphoricons.com), the default),
`bootstrap` ([Bootstrap Icons](https://icons.getbootstrap.com)) and `october`, each mapping every key.
A pack only maps keys to classes, so load the icon font itself in your layout.

To switch the pack every widget uses, set `LARAJAX_ICONS` in your `.env` file, such as
`LARAJAX_ICONS=bootstrap`.

`Ui::registerIconPack()` adds a pack, or replaces one, by name. The pack is an array of keys and
classes, a class that implements `Larajax\Ui\Classes\IconPack` (or its name), or a closure that returns
the array. A closure or class name is only resolved when the pack is first used. The registrations
above add an `amber` pack built on the phosphor pack with one key swapped. The table compares a few keys
across the packs:

<<< @/../resources/views/reference/extending/examples/icon-packs.blade.php{blade}

## The keys the library uses {#all-keys}

The demo page lists every key of the default pack with its icon, from the icon manager's `icons()`
method, which returns a pack's resolved keys and classes:

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
**default** | the pack the widgets use. Default: the `LARAJAX_ICONS` environment value, or `phosphor`.
**fallback** | passes names that are not keys through as icon classes, so an `icon` argument can take classes. Default: `true`.
**packs** | the packs by name, each an `IconPack` class name or an array of keys and classes.
**overrides** | keys and classes that replace those keys in every pack.

## Methods {#methods}

Method | Description
------------- | -------------
**Ui::extendIcons($extension)** | replaces or adds keys in every pack, from an array or a closure.
**Ui::registerIconPack($name, $pack)** | adds or replaces a pack.

The widgets draw their icons through `Ui::icon()` and `Ui::iconClass()`, which resolve a key in the
default pack. They exist for the library's own views, so an app rarely needs to call them.
