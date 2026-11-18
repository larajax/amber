# Amber

Amber is the demo app for Larajax UI (`larajax/ui`), and the reference for how a Laravel application
is built with it. Every screen here is built from the library's widgets, and the documentation in
`docs/` imports its code samples from this app's files.

## Larajax UI

This application builds its forms, lists and filters with `larajax/ui` widgets, not hand-written
markup. Before building or changing a screen, read `vendor/larajax/ui/AGENTS.md` and follow it.

## Using Amber as a reference

When you are building another application and were pointed here, find the screen closest to what you
need and follow its pattern. Each controller keeps to its page actions, and each widget's setup and
handlers live in a trait in a folder named after the controller.

- **A list with a filter, a toolbar and bulk actions:** `app/Controllers/PostsController/PostList.php`,
  the definitions in `resources/ui/post`, and `resources/views/posts/index.blade.php`.
- **A form with tabs, relations, an upload and validation:**
  `app/Controllers/PostsController/PostForm.php`, `resources/ui/post/fields.yaml` and
  `resources/views/posts/create.blade.php`.
- **A plain Eloquent model, validated in the controller:**
  `app/Controllers/AuthorsController/AuthorForm.php` and `AuthorList.php`.
- **Editing a row in a popup:** `app/Controllers/GroupsController` and
  `resources/views/groups/_popup_form.blade.php`.
- **A quick create popup:** `app/Controllers/PostsController/QuickPostForm.php`.
- **A tree reordered by dragging:** `app/Controllers/CategoriesController/CategoryList.php`.
- **Scoreboards and charts:** `app/Controllers/DashboardController` and
  `resources/views/dashboard/index.blade.php`.
- **A custom form widget:** `app/FormWidgets/TimeChecker.php`, registered in
  `app/Providers/AppServiceProvider.php`.
- **A standalone widget:** `app/Widgets/CommentFeed.php`, built in
  `app/Controllers/Reference/ExtendingController/StandaloneWidgets.php`.
- **The base controller, the routes and the layout:** `app/Classes/ControllerBase.php`,
  `routes/web.php` and `resources/views/layouts/app.blade.php`.

For a single option rather than a whole screen, the Reference pages have one example of each.
`config/reference.php` lists the pages, their controllers are in `app/Controllers/Reference`, their
definitions in `resources/ui/reference` and their views in `resources/views/reference`.

## Changing Amber

- **The docs own the words and the app owns the code.** A docs page never retypes code that exists
  here. It imports the file, as `docs/README.md` describes.
- **Every example needs its docs section.** Each section of a Reference page has a docs heading with
  the same anchor, and `tests/Feature/Reference/DocsTest.php` and `CoverageTest.php` fail when one is
  missing. Run `composer test` after a change.
- **The library is not developed here.** `vendor/larajax/ui` is the installed copy, so do not edit it.
  Its source is https://github.com/larajax/ui.
