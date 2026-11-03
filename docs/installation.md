---
subtitle: Add Larajax UI to a Laravel application.
---
# Installation

Larajax UI renders forms, lists and filters from YAML configuration in any Laravel application. This
page installs it and wires it into a layout and a controller, using the demo app's own files as the
example.

## Requirements {#requirements}

- PHP 8.2 or higher
- Laravel 12 or higher
- [october/rain](https://github.com/octobercms/library), which provides the database, validation and
  HTML helpers the library builds on. Composer installs it with the library.
- [Larajax](https://larajax.org), which provides the view component interface the widgets implement and
  the `window.jax` browser API their controls register with. Composer installs it with the library;
  follow its [installation guide](https://larajax.org/guide/installation.html) for its own assets and
  setup.
- [Bootstrap 6](https://getbootstrap.com). The library's styles layer over Bootstrap's, and its menus
  use Bootstrap's data API. Load Bootstrap's stylesheet and JavaScript module in your layout before the
  library's assets (see [Bootstrap 6](#bootstrap-6) below).

Larajax UI runs against a plain Laravel install, with no CMS. File uploads also need a file model and
two database tables, described in [file attachments](/guides/models#file-attachments).

### Bootstrap 6 {#bootstrap-6}

The library targets Bootstrap `6.0.0-alpha.1`. Bootstrap 6 is still an alpha release, so its class
names and tokens may change before the stable version, and a few things follow from it:

- **Modern browsers only.** Bootstrap 6 needs Chrome or Edge 130, Firefox 132 or Safari 18 and newer,
  with no fallbacks below that.
- **ES modules only.** Bootstrap's JavaScript loads with `<script type="module">` or an `import` in your
  bundle. There is no `window.bootstrap` global, and the library does not need one.
- **The library owns its popups.** A popup is the library's own native `<dialog>`, styled with
  Bootstrap's `dialog-*` classes, so it needs no Bootstrap JavaScript. Menus open through Bootstrap's
  `data-bs-toggle="menu"` data API, which the Bootstrap module provides.
- **Set the colour mode on `<html>`.** Bootstrap follows the operating system's light or dark preference
  when no `data-bs-theme` attribute is set, but the library's palette follows the attribute. Set
  `data-bs-theme="light"` or `data-bs-theme="dark"` on `<html>`, as the layout
  [below](#include-the-assets) does. To follow a saved choice or the system preference, set it from a
  script before the page paints, as Bootstrap's
  [color modes guide](https://getbootstrap.com/docs/6.0/customize/color-modes/) shows.
- **Sizes follow Bootstrap's.** Lists, filters, forms and the other widgets are sized on Bootstrap's
  scale, with 16px text beside its 38px buttons and inputs, and every size is in `rem`. For a denser
  interface, set a smaller root font size and everything shrinks together, such as
  `html { font-size: 87.5%; }` for 14px text and 33px controls. A percentage keeps the visitor's own
  browser text size in play. Borders stay in pixels and breakpoints keep their widths.

## Install the package {#install-the-package}

Require the package with Composer:

```bash
composer require larajax/ui
```

The package registers its `UiServiceProvider` through Laravel's package discovery, so there is no
provider to add by hand.

## Publish the assets {#publish-the-assets}

The widgets load a small CSS and JS bundle in the browser. It is **plain CSS and ES modules, with no
build step**. The package's `resources/assets` directory is the browser-ready root, and publishing
copies it as it is, so the published paths always match the source paths. Publish them once after
installing, and again after upgrading:

```bash
php artisan vendor:publish --tag=larajax-ui-assets
```

The files are copied into `public/vendor/larajax/ui/`. Larajax's own assets are published as its
[installation guide](https://larajax.org/guide/installation.html) describes. The demo app republishes
both with `--force` from the `post-autoload-dump` scripts in its `composer.json`, so every
`composer install` and `composer update` keeps them current.

## Include the assets in your layout {#include-the-assets}

Add the stylesheets and scripts to your Blade layout:

```blade
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>My App</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@6.0.0-alpha.1/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="{{ asset('vendor/larajax/ui/ui.css') }}" rel="stylesheet">

        <script src="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.2"></script>
        <script type="module" src="https://cdn.jsdelivr.net/npm/bootstrap@6.0.0-alpha.1/dist/js/bootstrap.bundle.min.js"></script>
        <script src="{{ asset('vendor/larajax/framework-bundle.js') }}"></script>
        <script type="module" src="{{ asset('vendor/larajax/ui/ui.js') }}"></script>

        {{ Ui::renderAssets() }}
    </head>
    <body>
        @yield('content')
    </body>
</html>
```

The order matters:

- Bootstrap's stylesheet comes before `ui.css`, so the library's rules layer over it.
- Larajax's `framework-bundle.js` loads **before** `ui.js`, because the widgets register their controls
  with the `window.jax` API it creates. Use the **bundle** build: the plain `framework.js` build does
  not include the control API (`jax.registerControl`) the library needs.
- `Ui::renderAssets()` prints, at the end of `<head>`, any files that widgets add during the request,
  such as a [custom widget](/extending/custom-widgets) that loads its own script with `addJs()`. It works
  in the layout because the page content renders before the layout does. AJAX responses carry the same
  files when a widget is created during a request, and the browser skips files it already has. See
  [printing the assets](/extending/custom-widgets#render-assets).

The Phosphor script draws the library's built-in icons; to use [another pack](/extending/icons), load
its font instead. Bootstrap's JavaScript module opens the menus. Beyond Bootstrap and Larajax, the
library has no framework dependency. The demo app's
[own layout](https://github.com/larajax/amber/blob/main/resources/views/layouts/app.blade.php) adds a
colour mode switch, a sidebar and a top bar on top of this.

### Using with Vite {#using-with-vite}

If your application already runs a [Vite](https://laravel.com/docs/vite) pipeline, you can skip
publishing the library's assets and import the entries straight from the vendor directory. The source
tree resolves the same way the published tree does:

```js
// resources/js/app.js
import '../../vendor/larajax/ui/resources/assets/ui.js';
```

```css
/* resources/css/app.css */
@import '../../vendor/larajax/ui/resources/assets/ui.css';
```

Vite then bundles the library into your application build. Larajax must still load before your bundle
runs, and Bootstrap's stylesheet before `ui.css`.

## Prepare your controller {#prepare-your-controller}

Pages that show widgets use a Larajax controller, as Larajax's
[controller integration](https://larajax.org/guide/laravel-integration.html) guide sets up, and add the
library's `ViewMaker` trait, which lets widgets render partials through the controller, such as toolbar
buttons, partial columns and hint fields. The demo app's controllers extend this base class:

<<< @/../app/Classes/ControllerBase.php

## Routes {#routes}

A page and its widgets' AJAX requests share one address, so each page route answers both GET and POST,
as Larajax's [controller integration](https://larajax.org/guide/laravel-integration.html) guide
explains:

<<< @/../routes/web.php#docs-post-routes{php}

## Render your first form {#render-your-first-form}

Describe the form in a YAML file. The demo app keeps its definitions in `resources/ui`, one folder per
model, and the group form has four fields:

<<< @/../resources/ui/user_group/fields.yaml

The controller's `create` and `edit` actions build the form for their record and pass it to the view:

<<< @/../app/Controllers/GroupsController.php

`makeFormWidget()` builds the form with `Form::make()`, binding it to the model. A path starting with
`~/` resolves from the application root:

<<< @/../app/Controllers/GroupsController/GroupForm.php#docs-make-form{php}

The view wraps the widget in a `<form>` element whose `data-request` names the handler that saves it,
and adds the buttons:

<<< @/../resources/views/groups/edit.blade.php{blade}

That is a complete, working form: the fields render from YAML, show the group's values, and post back
to the `onUpdate` handler through Larajax. [Building a Form Page](/guides/forms) covers saving,
validation and the rest.

## Organising a controller {#organising-a-controller}

As a controller grows, keep the class to its page actions, as `GroupsController` does above, and move
each widget's setup into a trait, together with the AJAX handlers that belong to it, in a folder named
after the controller. The posts pages have three:

```text
app/Controllers/PostsController.php
app/Controllers/PostsController/PostList.php        the list, filter and toolbar, and the bulk actions
app/Controllers/PostsController/PostForm.php        the form, and the store, update and destroy handlers
app/Controllers/PostsController/QuickPostForm.php   the quick post popup and its handler
```

Each trait is plain code you own. It declares the widget properties it uses, builds its widgets with
`Form::make()`, `Lists::make()` and the others, and holds their handlers. The traits share one class, so
give each method a name that says which concern it belongs to, such as `makeListWidget()` and
`makeQuickPostFormWidget()`. The [form guide](/guides/forms) walks through `PostForm`, and the
[list guide](/guides/lists) through `PostList` and `QuickPostForm`.

## Timezones {#timezones}

Dates are stored in the application timezone. To show them in each person's own timezone, register a
resolver with `Ui::resolveTimezoneUsing()`, see [timezones](/extending/seams#timezone). The columns,
fields and scopes that convert, and the `useTimezone` option that turns it off, are listed in
[timezones in lists, filters and forms](/extending/seams#timezone-lists).

## Next steps

- [Building a Form Page](/guides/forms): render, save and validate a form
- [Building a List Page](/guides/lists): a list with a toolbar and a filter
- [Models](/guides/models): drive the widgets from Rain or plain Eloquent models
