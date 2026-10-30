---
subtitle: What Larajax UI is, and the demo app to try it in.
---
# Getting Started

Larajax UI builds data-driven interfaces for Laravel from configuration. You describe a form's fields or
a list's columns in YAML, bind a model, and the widget renders working markup with its AJAX behaviour
(sorting, pagination, search, validation, uploads) wired up.

There is no JavaScript framework and no build step: the browser assets are plain CSS and ES modules,
styled on top of Bootstrap 6. The widgets run on plain Eloquent models as well as October Rain models,
and every widget is a [Larajax](https://larajax.org) view component, so its AJAX handlers reach the
controller that built it without any routing of your own.

## Run the demo app {#run-the-demo-app}

[Amber](https://github.com/larajax/amber) is the demo app for Larajax UI, a Laravel application that
runs every example in these docs. Clone it, set it up and serve it:

```bash
git clone https://github.com/larajax/amber.git
cd amber
composer setup
composer dev
```

`composer setup` installs the dependencies, creates the `.env` file and an SQLite database, and seeds
it with users, groups and a blog of posts. `composer dev` serves the app at `http://localhost:8000`.
Every seeded user's password is `password`.

The app has two halves:

- **Reference**, at `/reference`, has one page for every part of the library, grouped the way these docs
  are: list columns and options, filter scopes, form fields and layout, each form widget, the UI
  elements, and the extension points. Every example runs beside the YAML or Blade that renders it, and
  you can change it and save it. Each example's heading links to its section in these docs.
- **Showcase** is a small blog that puts the pieces together: posts with a filtered list, bulk actions
  and a quick post popup (`/posts`), a category tree you reorder by dragging (`/categories`), authors on
  a plain Eloquent model (`/authors`), and groups edited in a popup (`/groups`). Each page links to the
  guide that builds it.

## The sixty second version {#sixty-second-version}

The demo's Authors page lists Laravel's stock `User` model. Its columns are a YAML file:

<<< @/../resources/ui/user/columns.yaml

A controller builds the list with `Lists::make()`, and a filter and a toolbar bound to it:

<<< @/../app/Controllers/AuthorsController/AuthorList.php#docs-list-page{php}

The view prints the widgets:

<<< @/../resources/views/authors/index.blade.php{blade}

That renders a sortable, searchable, paginated table of authors, with filter scopes from
`resources/ui/user/scopes.yaml` and a toolbar of buttons and a search box above it. Clicking a row opens
the author's form, which works the same way from a `fields.yaml` file.

Each widget declares its common options as `make()` arguments, so your editor can suggest and document
them as you type, and less common options pass through the same call by name. A configuration array
built at runtime goes to `Lists::makeWithConfig($config)`. Widgets are `Htmlable`, so Blade's `{{ }}`
prints their markup as it is.

## How it fits together {#how-it-fits-together}

A widget built with `make()` inside a controller action is bound to that controller. When a control in
the browser sends an AJAX request, such as a click on a sortable header, Larajax runs the same action
again and calls the widget's handler, so the page needs no routes beyond its own address, accepting
GET and POST. The [AJAX guide](/guides/ajax) explains the lifecycle.

Configuration is declarative but never a cage: each widget also takes plain PHP arrays, can be extended
by subclassing or by binding to its events, and reads any model, including plain Eloquent models (see
[Models](/guides/models)).

## The docs {#the-docs}

- **Introduction**: this page and [Installation](/installation), which adds Larajax UI to an
  application.
- **Guides** walk through the Showcase pages: [Building a Form Page](/guides/forms),
  [Building a List Page](/guides/lists), [Models](/guides/models) and [AJAX & Larajax](/guides/ajax).
- **Reference** pages explain every option, one page for each page of the demo's Reference, section for
  section:
  - [Lists](/lists/columns): columns, list options, the list structure, the toolbar and search;
  - [Filters](/filters/scopes): scope types and filter options;
  - [Forms](/forms/fields): field types, layout and behaviour;
  - [Form Widgets](/form-widgets/relation): relations, repeaters, uploads, editors, pickers and more;
  - [UI](/ui/buttons): buttons, dropdowns, popups, tabs, feedback and inputs;
  - [Extending](/extending/custom-widgets): custom widgets, icons, and URL and timezone rules.

## Next steps

- [Installation](/installation): get Larajax UI into your application
