---
subtitle: Pick one record from a searchable list in a popup.
demo: /reference/form-widgets/record-finder
---
# Record Finder

The `recordfinder` field shows the picked record by its name and description, and opens a searchable,
paginated list in a popup to pick another. It suits relations with too many records for a dropdown.

## Picking a record {#default}

By default the field name is a relation on the model, here the `reviewer` relation to the `User`
model. Select opens the popup, clicking a row picks that record, and Clear empties the field. The field
posts the record's key, and saving the record sets the relation.

<<< @/../resources/ui/reference/form-widgets/record-finder/default.yaml

<<< @/../app/Models/Sample.php#docs-belongs-to{php}

`list` defines the popup's columns, inline or as the path to a columns YAML file, and the columns marked
`searchable` feed the search box. `nameFrom` and `descriptionFrom` name the columns that describe the
picked record. `title` heads the popup, and `recordsPerPage` sets how many rows each page lists.

On a plain Eloquent model the posted key needs mapping onto the relation when you save, as the
[relation field](/form-widgets/relation#eloquent) shows.

## Scopes {#scopes}

`scope` narrows the popup list. It takes the name of a model scope on the related model, or a static
`Class::method`, and either is called with the query and the record being edited. Here a static method
on `Sample` lists verified users only.

<<< @/../resources/ui/reference/form-widgets/record-finder/scopes.yaml

<<< @/../app/Models/Sample.php#docs-owner-scope{php}

`searchScope` replaces the built-in search with a scope on the related model, called with the search
term and the searchable columns. This one matches the start of a name or an email, where the built-in
search matches words anywhere in the columns. The search box still needs at least one `searchable`
column.

<<< @/../app/Models/User.php#docs-search-scope{php}

## The popup list {#popup}

`filter` adds filter scopes above the list, inline or as the path to a scopes YAML file, with the same
scope types as any [filter](/filters/scopes). `defaultSort` orders the list by a `column` and a
`direction`. `showSetup` adds the list setup button, where the user picks the visible columns and the
page size. `popupSize` sets the popup width: `tiny`, `small`, `large`, `huge` (the default), `giant`
or `adaptive`.

<<< @/../resources/ui/reference/form-widgets/record-finder/popup.yaml

## Trees and the stacked layout {#tree}

When the related model is a tree, using the `NestedTree` or `SimpleTree` trait, the popup lists the
records as a tree, with children under their parent and no reordering. `structure: false` turns the
tree off, and `structure` also takes [list structure](/lists/list-structure) options.

<<< @/../resources/ui/reference/form-widgets/record-finder/tree.yaml

<<< @/../app/Models/Category.php#docs-nested-tree{php}

`inlineOptions: false` stacks the picked record above the Replace and Clear buttons, instead of beside
them, which leaves room for a longer description or a narrow column.

## Without a relation {#without-relation}

`useRelation: false` saves a plain value instead of setting a relation. `modelClass` names the model the
popup lists, and `keyFrom` names the column whose value the field saves, `id` by default. Here the
field saves a group's `code` in the `group_code` column.

<<< @/../resources/ui/reference/form-widgets/record-finder/without-relation.yaml

`conditions` filters the list with a raw SQL where clause. It takes the place of `scope`, so when both
are set only `conditions` applies. With neither, a relation's own constraints filter the list.

## Read-only {#read-only}

`readOnly` shows the record without its buttons, and still posts its key from a hidden input. This copy
reads the reviewer through `valueFrom`, and its name starts with an underscore, which keeps it out of
the saved data.

<<< @/../resources/ui/reference/form-widgets/record-finder/read-only.yaml

## Preview {#preview}

`disabled` shows the record in preview mode, and the field is left out of the save. With no record, the
field says so.

<<< @/../resources/ui/reference/form-widgets/record-finder/preview.yaml

## Options

Option | Description
------------- | -------------
**list** | the popup list's columns, inline or as the path to a columns YAML file.
**title** | the popup title. Default: `Find Record`.
**nameFrom** | the column shown as the picked record's name. Default: `name`.
**descriptionFrom** | the column shown beside the name. Default: none.
**keyFrom** | the column whose value the field saves. Default: the relation's key, or `id` without a relation.
**recordsPerPage** | the rows on each page of the popup list. Default: `10`.
**scope** | a model scope on the related model, or a static `Class::method`, that narrows the list. Also accepted as `modelScope`.
**conditions** | a raw SQL where clause that narrows the list, used in place of `scope`.
**searchMode** | how the search box matches words: `all` (every word), `any` (any word) or `exact` (the whole phrase). Default: `all`.
**searchScope** | a model scope on the related model that performs the search instead.
**filter** | filter scopes shown above the list, inline or as the path to a scopes YAML file.
**defaultSort** | the list order, as a `column` and a `direction`.
**showSetup** | adds the list setup button to the popup. Default: `false`.
**structure** | `false` turns off the tree for a tree model, `true` forces it, or list structure options. Default: a tree when the related model is a tree.
**inlineOptions** | shows the buttons beside the picked record. `false` stacks them below it. Default: `true`.
**popupSize** | the popup width: `tiny`, `small`, `large`, `huge`, `giant` or `adaptive`. Default: `huge`.
**useRelation** | treats the field name as a relation. `false` saves a plain value from `modelClass` records. Default: `true`.
**modelClass** | the model the popup lists when `useRelation` is `false`.

The field also takes the common [field properties](/forms/fields#field-properties), such as `readOnly`,
`disabled` and `valueFrom`.
