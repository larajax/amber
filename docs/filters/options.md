---
subtitle: Starting values, SQL conditions, group options and linked scopes.
demo: /reference/filters/options
---
# Filter Options

Scope options set the values scopes start with, replace their default queries with SQL or model
scopes, shape the options of a group and link scopes together. The
[scope properties](/filters/scopes#scope-properties) and [scope types](/filters/scopes#types) list
every option in one place.

## The filters on this page {#building}

Most examples are built like those on [Scope Types](/filters/scopes#building): a filter of posts with
the scopes from its own YAML file, bound to a list of posts. The last two show their own methods.

<<< @/../app/Controllers/Reference/FiltersController/OptionsExamples.php#docs-option-filters{php}

## Default values {#defaults}

`default` sets the value a scope starts with, so these posts start without the archived ones and with
only the approved status. A change to a scope lasts for the session, and Clear Filters goes back to the
defaults.

A checkbox takes `1` to start on, a switch `1` or `2` for its first or second state, a dropdown the
value of an option and a group a list of values. The popover types take an array of their values, such
as a `condition` and a `value`.

<<< @/../resources/ui/reference/filters/options/defaults.yaml

## Conditions {#conditions}

`conditions` replaces a scope's default query with SQL.

- A checkbox takes one condition, applied while it is on, and a switch takes a list of two, one for
  each state.
- A dropdown takes one condition, where `:value` is the picked option, and a group takes one where
  `:value` is the picked values, quoted and separated by commas.
- Number, date and text scopes take one condition for each choice they offer, keyed by its name, where
  `true` keeps the default query. They offer only the named choices, and with a single choice the
  popover shows it as a label in place of a select, like the Address scope here.

Choice | Scope types | SQL placeholders
------------- | ------------- | -------------
**equals** | number, date, text | `:value`, and `:valueDate` for a date
**notEquals** | date | `:value`, `:valueDate`
**between** | number, date | number: `:min`, `:max`. Date: `:after`, `:afterDate`, `:before`, `:beforeDate`
**greater** | number | `:value`
**lesser** | number | `:value`
**before** | date | `:value`, `:valueDate`
**after** | date | `:value`, `:valueDate`
**contains** | text | `:value`

In a date condition, `:value`, `:after` and `:before` hold a date and time in the app timezone, while
`:valueDate`, `:afterDate` and `:beforeDate` hold the date alone.

<<< @/../resources/ui/reference/filters/options/conditions.yaml

## Empty option {#empty-option}

`emptyOption` on a group adds an option for the records with no value, here the posts without tags.
Picked with other options, it keeps the records with any of them or with none. It widens only its own
scope, so the published switch and the search box still apply. On a dropdown, `emptyOption` is the
option that applies no filter instead.

<<< @/../app/Controllers/Reference/FiltersController/OptionsExamples.php#docs-empty-option{php}

<<< @/../resources/ui/reference/filters/options/empty-option.yaml

## Group options {#group-options}

`modelClass` names the model whose records are a group's options, in place of the model of the
relation the scope is named after. `optionsScope` applies one of that model's scopes to the options,
here offering only the categories that hold posts. `matchMode: toggle` adds a choice between including
and excluding the picked options, while `matchMode: exclude` always excludes them.

<<< @/../resources/ui/reference/filters/options/group-options.yaml

<<< @/../app/Models/Category.php#docs-has-posts{php}

## Date picker options {#date-options}

`minDate` and `maxDate` limit the dates that can be picked, here to 2026, and `yearRange` sets how many
years either side the calendar's year view offers. `firstDay: 1` starts the week on Monday in place of
the locale's first day, and `showWeekNumber` numbers the weeks. `useTimezone: false` compares the picked
days with the stored dates as they are, in place of reading them in the
[viewer's timezone](/extending/seams#timezone-lists).

<<< @/../resources/ui/reference/filters/options/date-options.yaml

## Names, labels and order {#names}

`valueFrom` filters a column with a name other than the scope's, here `published` for the `live`
scope, and `shortLabel` replaces the label in the bar. Scopes show in the order of the file, which
`before` and `after` change, so the status scope here comes before the views, and `order` sets a
position as a number. `cssClass` adds a class to the scope.

<<< @/../resources/ui/reference/filters/options/names.yaml

## Model scopes and dependent scopes {#depends}

`modelScope` runs a model scope in place of the default query. The scope method receives the query and
the filter scope, whose `value` holds the picked value, here filtering the users by their email
domain. `modelScope` also takes a static method as `Class::method`, called with the query and the
scope.

`dependsOn` links scopes together: when the named scope changes, this one is cleared and rendered
again, so a picked name resets when the domain does. On a group with a `modelClass`, an
`optionsMethod` given as a method name is called on that model with every scope and its current value,
so it can narrow its options by another scope, as in `$scopes->domain->value`.

<<< @/../app/Controllers/Reference/FiltersController/OptionsExamples.php#docs-depends{php}

<<< @/../resources/ui/reference/filters/options/depends.yaml

<<< @/../resources/ui/reference/filters/users.yaml

<<< @/../app/Models/User.php#docs-apply-domain{php}

<<< @/../app/Models/User.php#docs-name-group-options{php}

### Scope values {#scope-values}

A model scope reads the scope's value from these properties.

Scope type | Properties
------------- | -------------
**checkbox** | `value`, set while the checkbox is on.
**switch** | `value`: `1` or `2` for the first or second state.
**dropdown** | `value`: the picked option.
**group** | `value`: the picked values. `mode`: `include` or `exclude`.
**date** | `condition`, with `value`, or `after` and `before` for between.
**number** | `condition`, with `value`, or `min` and `max` for between.
**text** | `condition` and `value`.
