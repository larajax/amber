# Larajax UI Documentation

The source of [ui.larajax.org](https://ui.larajax.org), the documentation for Larajax UI
(`larajax/ui`). It is a [VitePress](https://vitepress.dev) site that lives inside Amber, the demo app,
because the docs and the app split the work:

- **The docs own the words**: concepts, explanations of every example, option tables and guides.
- **The app owns the code**: every example, the Showcase, and the tests. The docs never retype code
  that exists in the app; they import it, so a code sample is always the code the demo runs.

## Pages

| Path | Covers |
|------|--------|
| `index.md` | The landing page |
| `getting-started.md` | What Larajax UI is, running the demo app, the sixty second version, a map of the docs |
| `installation.md` | Requirements, Composer, assets, the layout, the base controller and routes |
| `guides/` | Building a form page, building a list page, models, AJAX and Larajax |
| `lists/`, `filters/`, `forms/`, `form-widgets/`, `ui/`, `extending/` | One page for each page of the demo app's Reference |

Each reference page carries `demo: /reference/{group}/{page}` in its frontmatter, which shows the "Run
it in the demo app" box (`.vitepress/theme/DemoLink.vue`). Its `##` sections follow the Reference page's
examples in order, each with the same anchor as the example's section id, so the book icon beside each
example links straight to its explanation. The sidebar lives in `.vitepress/config.mjs`.

## Code samples

Import code with VitePress's snippet syntax, a line starting with `<<<` followed by the path. `@` is this
folder, so `@/../` is the app's root: `<<< @/../resources/ui/post/columns.yaml`. Import YAML and Blade
files whole, and add a language when the extension does not give it, as in `{blade}` for a
`.blade.php` file.

For part of a larger PHP file, wrap the part in the app's file with two marker lines,
`// #region docs-{name}` and `// #endregion docs-{name}`, and import it with
`<<< @/../app/Models/Post.php#docs-{name}{php}`. Keep the region names unique within a file.

`tests/Feature/Reference/DocsTest.php` in the app checks that every Reference page has its docs page and
sidebar entry, that every example has a docs heading with its anchor, that every example file is
imported, and that every import points to a file and region that exist. Run it after changing the
docs or renaming an example:

```bash
vendor/bin/phpunit tests/Feature/Reference/DocsTest.php
```

## Building

The site runs on [Bun](https://bun.sh), whose own runtime `bunfig.toml` selects, or on Node 18 and
newer with `npm run`. From this folder:

```bash
# Install the dependencies
bun install

# Serve the docs with live reload at http://localhost:5173
bun run dev

# Build the static site into .vitepress/dist, failing on dead links
bun run build

# Preview the built site at http://localhost:8080
bun run preview
```

The build fails on a dead link between pages, while an import of a missing file only renders an error in
place of the code, which `DocsTest` catches. The `deploy-docs` GitHub workflow
(`.github/workflows/deploy-docs.yml`) builds the site and publishes it to GitHub Pages when files under
`docs/` change on the `main` branch, or when it is run by hand.
