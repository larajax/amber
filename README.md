# Amber

A sample Laravel application demonstrating [`larajax/ui`](https://github.com/larajax/ui),
the Form, List and UI widget engine (the same one behind the October CMS admin panel),
packaged for any Laravel app. No CMS dependency, no JavaScript framework, no build step.

The app has two halves:

- **Reference** (`/reference`) has one page for every part of the library, each example
  shown beside the YAML or Blade that renders it. The pages are listed in
  [`config/reference.php`](config/reference.php).
- **Showcase** is a small blog that puts the pieces together, with October Rain models
  (posts, categories, tags, groups) and a plain Eloquent model (the authors) side by side.

## The Showcase

| Screen | URL | What it shows |
| --- | --- | --- |
| Posts | `/posts` | Toolbar with a quick post popup and bulk actions, Filter, List with a setup popup |
| Create / edit post | `/posts/create`, `/posts/{id}/edit` | Form with tabs, relations to the category, author and tags, an image upload committed with the session key |
| Categories | `/categories` | ListStructure as a nested tree, reordered by dragging |
| Authors | `/authors` | Filter, List with a post count, bulk delete |
| Create / edit author | `/authors/create`, `/authors/{id}/edit` | Form on an Eloquent model, validated in the controller |
| Groups | `/groups` | List whose rows edit the group in a popup |

The widget definitions live in [`resources/ui`](resources/ui) and the controllers that
build them in [`app/Controllers`](app/Controllers).

## Requirements

- PHP 8.3+
- Composer
- SQLite (default), or any database Laravel supports

## Getting started

```bash
git clone https://github.com/larajax/amber.git
cd amber

# Install dependencies, copy .env, generate a key, create the SQLite
# database and run migrations + seeders.
composer setup

# Serve the app. There is no build step; assets load from a CDN and the
# published vendor/larajax files.
composer dev
```

Then open <http://localhost:8000>. The seeder creates five user groups and fifty users
(each wired to a primary and secondary groups) plus a stable `demo@example.com` account,
and a blog of thirty posts written by the first six users.

> The default password for every seeded user is `password`.

### Manual setup

If you prefer to run the steps yourself:

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan serve
```

## How it fits together

A controller extends `App\Classes\ControllerBase` (itself a `Larajax\LarajaxController`),
builds one or more widgets from YAML, and passes them to a Blade view that echoes them;
widgets render as safe HTML, so `{{ $widget }}` works directly. Because the widgets are
Larajax view components, their AJAX handlers (`onStore`, `onUpdate`, `onDestroy`, sorting,
pagination, filtering) route automatically back to the controller that created them.

```php
// app/Controllers/AuthorsController/AuthorList.php
$list = Lists::make(
    model: new User,
    columns: '~/resources/ui/user/columns.yaml',
    recordsPerPage: 10,
);

return view('authors.index', ['list' => $list]);
```

```blade
{{-- resources/views/authors/index.blade.php --}}
{{ $list }}
```

For the full documentation see the [`docs/`](docs) directory (a VitePress site) or the
[`larajax/ui`](https://github.com/larajax/ui) package.

## License

MIT
