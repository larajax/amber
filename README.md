# Amber

The demo app for [Larajax UI](https://ui.larajax.org) ([`larajax/ui`](https://github.com/larajax/ui)),
the form, list and UI tools for Laravel. Every example in the documentation runs here, beside the code
that renders it. No JavaScript framework, no build step.

## Quickstart

```bash
git clone https://github.com/larajax/amber.git
cd amber

# Install dependencies, copy .env, generate a key, create the SQLite
# database, run migrations + seeders and link public/storage.
composer setup

# Serve the app. There is no build step; assets load from a CDN and the
# published vendor/larajax files.
composer dev
```

Then open <http://localhost:8000/reference>.

The seeder creates five user groups and fifty users (each wired to a primary and secondary groups)
plus a stable `demo@example.com` account, and a blog of thirty posts written by the first six users.

> The default password for every seeded user is `password`.

### Requirements

- PHP 8.3+
- Composer
- SQLite (default), or any database Laravel supports

### Manual setup

If you prefer to run the steps yourself:

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

## What's inside

- **Reference** (`/reference`) has one page for every part of the library, each example running beside
  the YAML or Blade that renders it, where you can change it and save it. The pages are listed in
  [`config/reference.php`](config/reference.php), and each one links to its page on
  [ui.larajax.org](https://ui.larajax.org).
- **Showcase** is a small blog that puts the pieces together, with October Rain models (posts,
  categories, tags, groups) and a plain Eloquent model (the authors) side by side. Each screen has a
  guide that walks through its code:

| Screen | URL | What it shows | Guide |
| --- | --- | --- | --- |
| Posts | `/posts` | Toolbar with a quick post popup and bulk actions, Filter, List with a setup popup | [Building a List Page](https://ui.larajax.org/guides/lists) |
| Create / edit post | `/posts/create`, `/posts/{id}/edit` | Form with tabs, relations to the category, author and tags, an image upload committed with the session key | [Building a Form Page](https://ui.larajax.org/guides/forms) |
| Categories | `/categories` | ListStructure as a nested tree, reordered by dragging | [A tree of records](https://ui.larajax.org/guides/lists#list-structure) |
| Authors | `/authors` | Filter, List with a post count, bulk delete, on a plain Eloquent model | [Models](https://ui.larajax.org/guides/models) |
| Create / edit author | `/authors/create`, `/authors/{id}/edit` | Form on an Eloquent model, validated in the controller | [Validation in the controller](https://ui.larajax.org/guides/forms#validation-in-the-controller) |
| Groups | `/groups` | List whose rows edit the group in a popup | [Editing rows in a popup](https://ui.larajax.org/guides/lists#popup-editing) |

The widget definitions live in [`resources/ui`](resources/ui) and the controllers that build them in
[`app/Controllers`](app/Controllers).

## Tests

```bash
composer test
```

The suite covers every Reference page and Showcase screen. `tests/Feature/Reference/CoverageTest.php`
fails when a part of the library has no Reference page, and `tests/Feature/Reference/DocsTest.php`
keeps the docs in step: a docs page for every Reference page, a docs section for every example, and
every code sample in the docs imported from a file that exists here.

## Documentation

The documentation site lives in [`docs/`](docs) and is published at
[ui.larajax.org](https://ui.larajax.org). Its code samples are imported from this app's files, so the
docs always show the code the demo runs and the tests cover. See [`docs/README.md`](docs/README.md) to
build it.

## License

MIT
