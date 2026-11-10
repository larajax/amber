---
subtitle: Show parent and child records as a tree, and reorder them by dragging.
demo: /reference/lists/list-structure
---
# List Structure

The `ListStructure` widget is a list that shows parent and child records as a tree and reorders them
by dragging. It takes every [list option](/lists/options#options), adds its own, and saves each
move to the model: a tree model gets its new parent, and a sortable model stores the new order. It
shows every record at once, without pages, until a header sorts it.

## Tree {#tree}

The categories here are a plain Eloquent model that implements `TreeInterface`, where each category
names its parent in a `parent_id` column and keeps its place in a `sort_order` column. Drag a row up or
down to reorder it, or to the right to nest it under the row above, down to `maxDepth` levels. The arrows expand and collapse a branch, and the list remembers each branch for the
session.

Sorting by a header or searching shows a flat list. After a sort, the "Show tree" button at the end of
the header, or a third click on the same header, brings the tree back; a flat reorderable list's button
reads "Show order". After a search, clearing the search brings it back. The toolbar's search box is
bound to the tree the same way as to any list, and looks in its `searchable` columns.

<<< @/../app/Controllers/Reference/ListsController/ListStructureExamples.php#docs-tree{php}

<<< @/../resources/ui/reference/lists/list-structure/categories.yaml

<<< @/../app/Models/Category.php#docs-tree{php}

After a drop, the list associates the category's `parent` relation with its new parent, and passes
`setSortableOrder()` the ids of every row in their new order, as `sort_orders`:

<<< @/../app/Models/Category.php#docs-sortable-order{php}

## Tree with checkboxes {#checkboxes}

`showCheckboxes: true` puts a checkbox before each row, and the drag handle beside it, so the tree can
be both reordered and worked on. Shift and click checks a run of rows, as in any list. A toolbar
button with `checked: true` sends the ids of the checked rows as `checked`, and is disabled while
none are checked.

<<< @/../app/Controllers/Reference/ListsController/ListStructureExamples.php#docs-checkboxes{php}

## Browsing tree {#browse}

`treeExpanded: false` starts with every branch closed, and `showReorder: false` removes the drag
handles, leaving a tree to browse. `permissions` does the same for visitors without one of the named
gate abilities: they see no handles, and a reorder request from them is refused.

<<< @/../app/Controllers/Reference/ListsController/ListStructureExamples.php#docs-browse{php}

## Reorderable list {#reorder}

`showTree: false` reorders a flat list, for a model that implements
`October\Contracts\Database\SortableInterface`, like the users here.
After a drop, the list posts the ids of every row in their new order as `sort_orders`, and calls the
model's `setSortableOrder()` with them. Until a header sorts the list, the rows are ordered by the
`sortOrderColumn`, `sort_order` by default.

`dragRow: false` drags a row by its handle only, so the rows can still be selected and clicked.

<<< @/../app/Controllers/Reference/ListsController/ListStructureExamples.php#docs-reorder{php}

<<< @/../resources/ui/reference/lists/list-structure/users.yaml

<<< @/../app/Models/User.php#docs-sortable-order{php}

## Model requirements {#model-requirements}

- **A tree**, the default, needs a model that implements `October\Contracts\Database\TreeInterface`,
  with `getChildren()`, `getChildCount()` and the `getNested()` and `listsNested()` scopes, like the
  categories above. Any other model throws an error that suggests `showTree: false`. See
  [trees](/guides/models#trees) for what each method returns.
- **A move is saved** in the ways the model supports. A model that implements `NestedSetInterface`
  moves the node next to its new neighbour or under its new parent. Otherwise, a model with a `parent`
  relation gets its new parent, and a model that implements `SortableInterface` gets its new order
  through `setSortableOrder()`. A model with none of these shows the drag handles but keeps its order.
- **A flat reorderable list** works on any model that implements `SortableInterface`, with a
  `sort_order` column and its own `setSortableOrder()`, like the users above.

## Structure and sorting {#structure-and-sorting}

The structure and column sorting do not mix. Clicking a sortable header hides the tree and sorts a flat
list, with pages when `recordsPerPage` is set, and a third click on the same header brings the
structure back, as does the icon at the end of the header. Searching also shows flat results until the
search is cleared. A list with a `defaultSort` starts sorted and flat, until the visitor brings the
structure back.

## Options {#options}

`ListStructure::make()` takes every [list option](/lists/options#options) and these.

Option | Description
------------- | -------------
**showTree** | shows the parent and child records as a tree. Default: `true`.
**treeExpanded** | starts with every branch open. The state of each branch is kept in the session. Default: `true`.
**showReorder** | shows a drag handle on each row. Default: `true`.
**maxDepth** | the most levels a row can be nested by dragging. Default: no limit, or `1` with `showTree: false`.
**dragRow** | lets the whole row drag, not only its handle. Default: `true`.
**permissions** | gate abilities needed to reorder, as a string or an array where any one is enough. Without them the handles are hidden.
**sortOrderColumn** | the column that orders a flat list while no header sorts it. Default: `sort_order`.
**includeSortOrders** | posts `sort_orders` with every move, which a sortable model always does. Default: `false`.
**includeReferencePool** | passes `setSortableOrder()` no orders, so the model can share out the current sort orders of the moved rows in place of numbering them from 1, for reordering a subset such as one page. Default: `false`.
**expandLastColumn** | while the list is flat, stretches the last column over the empty cell kept for the handles. Default: `false`.
