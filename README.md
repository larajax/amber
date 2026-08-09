Amber
=======

Form, List and UI tools for Laravel and [October CMS](https://octobercms.com).

Amber is the foundation layer for rendering forms, lists, filters and other backend widgets. It is the same widget engine that powers the October CMS admin panel, packaged as a standalone library so it can be used anywhere. This includes front-end pages, inside October CMS themes/components, or directly from a plain Laravel route/controller.

This package (`larajax/amber`) is the Laravel implementation of Amber. It brings in [october/rain](https://github.com/octobercms/library) and [october/amber](https://github.com/octobercms/amber) together so the widget engine is ready to consume in any Laravel application.

## What is this?

Amber provides a reusable, YAML-driven widget system for building data-editing interfaces. It is not tied to a specific application shell. Use it to:

- Render forms and lists in a regular Laravel app, outside of any CMS.
- Build the field-rendering pipeline inside October CMS itself.
- Compose admin-style UIs from configuration rather than hand-written markup.

In short, Amber is the wider abstraction that sits below October's backend module; the part that knows how to turn a `fields.yaml` into a working form, or a set of `columns.yaml` into a sortable list.

Each widget implements the [Larajax](https://larajax.org/guide/defining-components.html) `ViewComponentInterface`, so AJAX handlers (uploads, validation, partial updates, etc.) are wired in automatically the same way as any other Larajax view component. A widget rendered by Amber behaves like a first-class Larajax component on the page.

## Requirements

- PHP 8.2 or higher
- Laravel 12
- [october/rain](https://github.com/octobercms/library) (used for the underlying database, validation, and HTML helpers)
- [october/amber](https://github.com/octobercms/amber) (the widget engine itself)
- [larajax/larajax](https://larajax.org) (provides the View Component interface widgets implement)

Installing `larajax/amber` pulls all of these in for you.

## Installation

```bash
composer require larajax/amber
```

The package registers an `AmberServiceProvider` automatically via Laravel's package discovery.

### Publishing assets

Amber widgets ship a small CSS + JS bundle that the browser loads at runtime. Publish it once after installing (and again after upgrading):

```bash
php artisan vendor:publish --tag=amber-assets
```

Files are copied verbatim into `public/vendor/amber/`. There is no build step; the JS is plain ES modules and the CSS is plain CSS with `@import`.

Include them in your layout, after the larajax framework bundle:

```blade
<link rel="stylesheet" href="{{ asset('vendor/amber/amber.css') }}">
<script src="{{ asset('vendor/larajax/framework.js') }}"></script>
<script type="module" src="{{ asset('vendor/amber/amber.js') }}"></script>
```

Larajax exposes the `window.jax` API that Amber widgets register their controls against, so it must load first.

## Usage

Build a widget inline in your controller action with `Form::make(...)`, then pass it to the view:

```php
use App\Models\User;
use October\Amber\Widgets\Form;

public function edit($id)
{
    $user = User::findOrFail($id);

    $form = Form::make([
        'model' => $user,
        'fields' => '~/resources/amber/user/fields.yaml',
    ]);

    return view('users.edit', ['form' => $form]);
}
```

`Form::make([...])` constructs the widget, binds it to the current controller, and returns it. The widget is a regular PHP object after that - pass it to the view, store it in a variable, do whatever you would do with any other object. AJAX handlers defined on the widget (file uploads, inline validation, partial reloads, etc.) are wired up automatically.

Render the widget in a Blade view:

```blade
{!! $form->render() !!}
```

Amber widgets are [Larajax view components](https://larajax.org/guide/defining-components), and `Form::make` is the standard inline registration pattern documented there. For details on how the action lifecycle handles AJAX requests, how to guard side effects with `request()->ajax()`, or how to use widgets outside a `LarajaxController`, see the Larajax docs.

## Included Widgets

- **Form** - YAML or array-driven form builder with field widgets (text, dropdown, repeater, file upload, etc.)
- **Lists** - sortable, paginated record lists with column types and row actions
- **ListStructure** - tree and reorderable list variants
- **Filter** - scope-based filtering for list views
- **Toolbar** - action buttons and search bar
