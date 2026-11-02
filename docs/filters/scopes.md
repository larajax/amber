---
subtitle: The filter widget and every scope type it offers.
demo: /reference/filters/scopes
---
# Scope Types

The `Filter` widget is a bar of scopes above a list, each shown as a chip that narrows the list. A chip
toggles, opens a list of options, or opens a popover with a small form. An active chip shows its value
and a button that clears it, and active chips move to the front of the bar. `bindToListWidget()`
applies the active scopes to the list query and refreshes the list when one changes. Each scope
remembers its value for the session, and the menu at the start of the bar clears them all or
[saves them](/filters/options#saved-filters) as a named set to apply again. Adding a filter to a page
is covered in [Building a List Page](/guides/lists).

## The filters on this page {#building}

Each example binds a filter, with the scopes from its own YAML file, to a list of the seeded posts. The
lists share one columns file.

<<< @/../app/Controllers/Reference/FiltersController/ScopesExamples.php

<<< @/../resources/ui/reference/filters/posts.yaml

## Checkbox {#checkbox}

`checkbox` is on or off, and a click on its chip toggles it, with a check mark in place of the plus
while it is on. Off, it applies nothing. On, it keeps the records whose column of the same name is
true, like `published`, or the records that match the SQL in `conditions`, like the posts with
comments here.

<<< @/../resources/ui/reference/filters/scopes/checkbox.yaml

## Switch {#switch}

`switch` has three states, picked from its chip's menu like a dropdown: Any applies nothing, a check
mark applies the second of its two `conditions` and a cross the first. Without conditions, the check
keeps the records whose column of the same name is true and the cross those where it is false. The
chip shows the check or cross beside its label, and screen readers announce it as yes or no.

<<< @/../resources/ui/reference/filters/scopes/switch.yaml

## Dropdown {#dropdown}

`dropdown` picks one value for the column of the same name, applied as soon as an option is picked
from its chip. Its options come from `options` in the
YAML, or from `optionsMethod`, given here as a static method, `Class::method`, whose options carry
colors. An option is written as `value: label`, or as `value: [label, icon]` or `value: [label, color]`
like the options of a [dropdown field](/forms/fields#option-styles). A color is a value starting with
`#`, `var(`, `rgb(`, `rgba(` or `hsl(`, and shows as a status dot before the label. `emptyOption` is the
option that applies no filter.

A static options method is called with the filter's model and the scope, and a plain method name is
called on the filter's model with the scope.

<<< @/../resources/ui/reference/filters/scopes/dropdown.yaml

<<< @/../app/Models/Post.php#docs-list-status-color-options{php}

## Group {#group}

`group` opens a popover with a searchable list of options, to pick several, and the chip shows how many
are picked, after a cross while it [excludes them](/filters/options#group-options). When the scope is named after a relation of the model, like `category`, the options are the
related records, labelled by their `nameFrom` attribute, and the filter keeps the records related to
any picked one. The popover lists the first 200 related records, and its search box looks up the rest.
The search box takes the focus when the popover opens, so typing starts a search, except on touch
devices, where the on-screen keyboard would cover the options.

With `options` or `optionsMethod` in place of a relation, the filter keeps the records whose column
holds any picked value, or, for a column the model stores as JSON, contains one.

<<< @/../resources/ui/reference/filters/scopes/group.yaml

## Date {#date}

`date` compares the column with a date picked from a calendar: the popover offers is equal to, not
equal to, between, before and after. `daterange` offers only between, and leaving one end of the range
empty turns it into before or after. Equal to matches any time on the picked day, which is read in the
[viewer's timezone](/extending/seams#timezone-lists).

<<< @/../resources/ui/reference/filters/scopes/date.yaml

## Text {#text}

`text` matches the column against typed text, either as the whole value (is equal to) or as a part of
it (contains).

<<< @/../resources/ui/reference/filters/scopes/text.yaml

## Number {#number}

`number` compares the column with a number: equal to, greater than, less than, or between two numbers,
where greater, less and between include the numbers given. `numberrange` offers only between, and
leaving one end empty turns it into greater or less.

<<< @/../resources/ui/reference/filters/scopes/number.yaml

## Scope properties {#scope-properties}

Every scope takes these properties, where they apply.

Property | Description
------------- | -------------
**label** | the scope's name in the bar.
**shortLabel** | a shorter name for the bar, in place of `label`.
**type** | the [scope type](#types). Default: `group`.
**valueFrom** | the column, or relation for a group, that the scope filters. Default: the scope name.
**conditions** | raw SQL in place of the default query. See [conditions](/filters/options#conditions).
**modelScope** | a model scope, or a static `Class::method`, in place of the default query, receiving the query and the scope with its value. See [model scopes](/filters/options#depends).
**default** | the value the scope starts with, and returns to when the filter is cleared. See [default values](/filters/options#defaults).
**options** | the options of a dropdown or a group.
**optionsMethod** | a model method, or a static `Class::method`, that returns the options.
**emptyOption** | the label of an extra option: on a dropdown it applies no filter, and on a group it matches the records with no value.
**nameFrom** | the attribute that labels a group's options from a relation. Default: `name`.
**modelClass** | the model that supplies a group's options and answers its options method, in place of the relation named by the scope.
**dependsOn** | a scope name, or an array of them. When one of them changes, this scope is cleared and rendered again.
**permissions** | gate abilities the visitor needs to see the scope, as a string or an array where any one is enough, checked with Laravel's `Gate`.
**context** | the filter context, or an array of them, that shows the scope. A scope named `name@context` does the same.
**cssClass** | a class added to the scope.
**order** | a number for the scope's position. Default: 100, 200 and so on, in the order of the definitions.
**after** | places the scope after the named scope.
**before** | places the scope before the named scope.

## Scope types {#types}

Type | Shows | Options
------------- | ------------- | -------------
**checkbox** | a checkbox, on or off. | `conditions`, `modelScope`
**switch** | a checkbox with three states: off, the first condition and the second. | `conditions`, `modelScope`
**dropdown** | a select of options, one picked. | `options`, `optionsMethod`, `emptyOption`, `conditions`, `modelScope`
**group** | a popover with a searchable list, several picked. | `options`, `optionsMethod`, `nameFrom`, `modelClass`, `optionsScope`, `emptyOption`, `matchMode`, `conditions`, `modelScope`
**date** | a popover comparing with a picked date. | `conditions`, `minDate`, `maxDate`, `yearRange`, `firstDay`, `showWeekNumber`, `useTimezone`, `modelScope`
**daterange** | a `date` scope offering only between. | as `date`
**text** | a popover matching typed text. | `conditions`, `modelScope`
**number** | a popover comparing with a number. | `conditions`, `modelScope`
**numberrange** | a `number` scope offering only between. | as `number`

### Type options {#type-options}

Option | Description
------------- | -------------
**conditions** | `checkbox`: SQL applied while it is on. `switch`: a list of two SQL statements, one for each state. `dropdown`: SQL with `:value` for the picked option. `group`: SQL with `:value` for the picked values, quoted and separated by commas, as in `status in (:value)`. `date`, `text` and `number`: the choices offered, keyed by name, each `true` for the default query or SQL of its own, as listed under [conditions](/filters/options#conditions).
**optionsScope** | `group`: a scope of the options model that narrows the options.
**matchMode** | `group`: `toggle` lets the visitor choose between including and excluding the picked options, and `include` or `exclude` fixes the mode. Default: `include`.
**minDate** | `date`: the first date that can be picked. Default: `1970-01-01`.
**maxDate** | `date`: the last date that can be picked. Default: `2199-12-31`.
**yearRange** | `date`: how many years either side the calendar's year view offers. Default: `10`.
**firstDay** | `date`: the first day of the week, from `0` for Sunday. Default: the locale's.
**showWeekNumber** | `date`: numbers the weeks in the calendar. Default: `false`.
**useTimezone** | `date`: reads the picked days in the viewer's timezone. `false` compares them with the stored dates as they are. Default: `true`.

## Filter widget {#filter-widget}

`Filter::make()` takes these options as named arguments.

Option | Description
------------- | -------------
**model** | the model the scopes filter, which also supplies relation options and options methods.
**scopes** | the scope definitions, as a YAML file path or an array.
**context** | the filter's context, which shows only the scopes for that context.
**alias** | the widget's name, unique on the page.
**search** | a search box after the scopes, as a [search widget](/lists/search) configuration, a partial name or a `Search` widget. See [search and list setup](/filters/options#search).
**setupHandler** | the handler that opens the list setup, usually `$list->getEventHandler('onLoadSetup')`, shown as an icon after the scopes.
**primary** | a row above the scopes: `search` for the search box and list setup, or the name of a dropdown scope, shown as [tabs](/filters/options#primary-tabs). Default: none, one row.
**savedFilters** | lets the menu save the scope values under a name and apply them again, see [saved filters](/filters/options#saved-filters). Default: `true`.

`bindToListWidget($list)` applies the active scopes to the list query, refreshes the list when a scope
changes, and shares the list's page parameter, so the list goes back to its first page. With `search`,
it also searches the list and keeps the search term for the session, as a toolbar search does.
