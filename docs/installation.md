---
subtitle: Get Amber installed in a Laravel application.
---
# Installation

Amber is a standalone library that renders forms, lists and filters from YAML configuration. It is the
same widget engine that powers the October CMS admin panel, packaged so it can run in any Laravel
application. This page gets it installed and wired into a layout.

## Requirements

- PHP 8.2 or higher
- Laravel 12 or higher
- [october/rain](https://github.com/octobercms/library) — provides the underlying database, validation
  and HTML helpers Amber builds on
- [larajax/larajax](https://larajax.org) — provides the view component interface that Amber widgets
  implement, and the `window.jax` browser API their controls register against
- [Bootstrap 5](https://getbootstrap.com) — Amber's styles layer over Bootstrap's, and the popup
  control wraps the Bootstrap Modal. Load the Bootstrap CSS and bundle JS in your layout before
  Amber's assets.

You do not need October CMS itself. Amber runs against a plain Laravel install.

## Install the package

Require the package with Composer:

```bash
composer require october/amber
```

The package registers its `AmberServiceProvider` automatically through Laravel's package discovery, so
there is no provider to add by hand.

## Publish the assets

Amber widgets load a small CSS + JS bundle in the browser. It is **plain CSS and ES modules — there is
no build step**. The package's `resources/assets` directory is the browser-consumable root; publishing
copies it verbatim, so the published paths always match the source paths. Publish once after
installing, and again after upgrading:

```bash
php artisan vendor:publish --tag=amber-assets
```

The files are copied into `public/vendor/amber/`.

## Include the assets in your layout

Add the stylesheet and scripts to your Blade layout. Larajax must load **first**, because Amber widgets
register their controls against the `window.jax` API it exposes. Use the larajax **bundle** build — the
plain `framework.js` build does not include the control API (`jax.registerControl`) that Amber
requires.

```blade
<link rel="stylesheet" href="{{ asset('vendor/amber/amber.css') }}">
<script src="{{ asset('vendor/larajax/framework-bundle.js') }}"></script>
<script type="module" src="{{ asset('vendor/amber/amber.js') }}"></script>
```

That is everything the browser needs. Amber ships no framework dependency — the CSS and JS are
self-contained.

### Using with Vite

If your application already runs a [Vite](https://laravel.com/docs/vite) pipeline, you can skip
publishing entirely and import the entries straight from the vendor directory — the source tree
resolves the same way the published tree does:

```js
// resources/js/app.js
import '../../vendor/october/amber/resources/assets/amber.js';
```

```css
/* resources/css/app.css */
@import '../../vendor/october/amber/resources/assets/amber.css';
```

Vite bundles Amber into your application build. Larajax must still load before your bundle runs.

## Prepare your controller

Amber widgets are [Larajax view components](https://larajax.org/guide/defining-components.html), so their
AJAX handlers (uploads, validation, partial reloads) are wired up automatically when the controller
extends the Larajax controller. Extend `Larajax\LarajaxController` — directly, or through a base class of
your own — and include Amber's `ViewMaker` trait, which lets widgets resolve partials through the
controller (toolbar buttons, hint fields, partial columns):

```php
namespace App\Controllers;

use Larajax\LarajaxController;

abstract class ControllerBase extends LarajaxController
{
    use \October\Amber\Traits\ViewMaker;
}
```

## Render your first form

Describe the form in a YAML file. Paths beginning with `~/` resolve from the application root:

```yaml
# resources/amber/user/fields.yaml
fields:
    name:
        label: Name
        type: text
    email:
        label: Email Address
        type: email
```

Build the widget inline in a controller action with `Form::make(...)`, bind it to a model, and pass it to
the view:

```php
namespace App\Controllers;

use App\Models\User;
use October\Amber\Widgets\Form;

class UserController extends ControllerBase
{
    public function edit($id)
    {
        $widget = Form::make([
            'alias' => 'form',
            'model' => User::findOrFail($id),
            'fields' => '~/resources/amber/user/fields.yaml',
        ]);

        return view('users.edit', ['widget' => $widget]);
    }
}
```

Render it in the view. Wrap it in a `<form>` element and add your own submit control:

```blade
<form>
    {!! $widget->render() !!}

    <button type="submit">Save</button>
</form>
```

That is a complete, working Amber form: the fields render from YAML, bind to the model's attributes, and
post back through Larajax.

## Next steps

- [Forms](forms.md) — every field, UI element and widget type available in a form
- [Lists](lists.md) — render sortable, paginated record lists
- [Filters](filters.md) — add scope-based filtering to a list
- [Using Eloquent models](eloquent.md) — drive Amber from plain `Illuminate` models
