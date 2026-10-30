---
subtitle: A search box that remembers its term, for a list or any handler.
demo: /reference/lists/search
---
# Search

The `Search` widget is a search box that remembers its term for the session and fires `search.submit`
when the term changes. A [toolbar](/lists/toolbar#search) builds one from its `search` option and binds
it to a list. `Ui::searchInput()` renders the same box for any AJAX handler.

## Search widget on its own {#standalone}

Made with `Search::make()`, the widget runs its own `onSubmit` handler as you type, saves the term in
the session and fires `search.submit`. Whatever the listeners return goes back as the response, here an
update of the tag results. `getActiveTerm()` reads the saved term, so the page renders the same results
on its first load, and the term is still in the box after a reload. `growable: false` keeps the box at
its full width.

<<< @/../app/Controllers/Reference/ListsController/SearchExamples.php#docs-tag-search{php}

<<< @/../resources/views/reference/lists/search/_tag_results.blade.php{blade}

## Bound to a list {#bound}

A toolbar's search box searches the list the toolbar is bound to with `bindToListWidget()`. The list
looks in its columns marked `searchable`, including columns read from a relation, and every word of
the term must match, each in any of those columns. A new term takes the list back to its first page.
The box grows while it has focus, and its clear button empties it.

Searching works on plain Eloquent models as well as October Rain models, since Larajax UI adds the
`searchWhere()` query methods it uses to Eloquent as builder macros.

<<< @/../app/Controllers/Reference/ListsController/SearchExamples.php#docs-bound{php}

<<< @/../resources/ui/reference/lists/search/posts.yaml

## Search scope {#scope}

`scope` names a model scope that runs the search in place of the searchable columns. It receives the
term and the searchable columns, so it can search those and more. The post model's
`scopeApplySearch()` also matches category and tag names, so "frontend" finds the posts in the Frontend
category and "javascript" the posts with that tag.

<<< @/../app/Controllers/Reference/ListsController/SearchExamples.php#docs-scope{php}

<<< @/../app/Models/Post.php#docs-apply-search{php}

## Search mode {#mode}

`mode` decides how the words match: `all`, the default, needs every word, `any` needs one of them, and
`exact` looks for the whole phrase. Try "dark lists" here, which finds the posts with either word.

<<< @/../app/Controllers/Reference/ListsController/SearchExamples.php#docs-mode{php}

## Search on Enter {#search-on-enter}

`searchOnEnter` waits for Enter before searching, for searches that are slow to run. `growable: false`
keeps the box at one width. With `partial`, the widget renders a partial of the controller in place of
its own box.

<<< @/../app/Controllers/Reference/ListsController/SearchExamples.php#docs-search-on-enter{php}

## Ui::searchInput {#search-input}

`Ui::searchInput()` renders the search box on its own, for any page. The `search-input` control gives
it a clear button, which shows once there is text. `name`, `value` and `placeholder` go on the input,
along with any other named argument, while `class` goes on the field around the input and
`outerClass` on the element around that.

<<< @/../resources/views/reference/lists/examples/search-input.blade.php{blade}

## Ui::searchInput with a handler {#search-input-ajax}

With a `handler`, the input runs that AJAX handler half a second after typing stops, posting the term
under the input's `name`, and shows a loading indicator while it runs. Clearing the box runs the
handler again. Here the handler filters a list of cities and updates the results partial.

<<< @/../resources/views/reference/lists/examples/search-input-ajax.blade.php{blade}

<<< @/../resources/views/reference/lists/search/_city_results.blade.php{blade}

<<< @/../app/Controllers/Reference/ListsController/SearchExamples.php#docs-search-cities{php}

## Search options {#options}

`Search::make()` takes these options as named arguments, and a toolbar's `search` array takes them as
keys. The term is kept in the session until the box is cleared.

Option | Description
------------- | -------------
**prompt** | the placeholder text.
**mode** | how the words match: `all`, `any` or `exact`. Passed to the bound list. Default: `all`.
**scope** | a model scope that runs the search, receiving the term and the searchable columns. Passed to the bound list.
**searchOnEnter** | searches when Enter is pressed, in place of as you type. Default: `false`.
**growable** | widens the box while it has focus. Default: `true`.
**partial** | a partial of the controller to render in place of the box.
**alias** | the widget's name, unique on the page. A toolbar names its search box after itself, such as `searchToolbarSearch`.

## Ui::searchInput arguments {#search-input-arguments}

Argument | Description
------------- | -------------
**name** | the input name, which the term is posted under.
**value** | the starting value.
**placeholder** | the placeholder text, which is also the input's label for screen readers. Default label: Search.
**handler** | an AJAX handler to run as you type, half a second after typing stops.
**class** | classes for the field around the input.
**outerClass** | classes for the outer element.

Any other named argument becomes an attribute of the input, with its name in kebab case, so
`dataRequestFlash: true` adds `data-request-flash`.
