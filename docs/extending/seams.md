---
subtitle: Adapt the widgets' links and dates to your application.
demo: /reference/extending/seams
---
# URLs and Timezones

Three resolvers adapt the widgets to your application. One turns the paths that widgets link to into
addresses, one turns links with your own scheme, such as `page://about`, into addresses, and one gives
the timezone of the person viewing the page. Each is a closure you set once, and the widgets work
without any of them.

## Setting the resolvers {#resolvers}

Set your resolvers in a service provider's `boot()` method, such as your `AppServiceProvider`, so they
apply to every request. This demo page sets them in its action instead, so they only apply there, and
removes them when the request ends by passing `null`, which restores the default.

<<< @/../app/Controllers/Reference/ExtendingController/SeamResolvers.php{php}

Each resolver here does what the sections below show: widget paths go back to this page, so you can
see the path a widget produced, `reference://` links go to the Reference page they name, and the
viewer timezone comes from the address.

## Ui::url() {#ui-url}

`Ui::url()` is what the widgets call for every path they link to, and you can call it in your own
views for the same result. It goes through these steps:

1. A link whose scheme has a link resolver, such as `reference://ui/tabs`, goes to that resolver, and
   comes back as an empty string when the resolver finds nothing.
2. Any other path goes to the URL resolver, when one is set.
3. Without a URL resolver, it returns `url($path)`.

<<< @/../resources/views/reference/extending/examples/seams-url.blade.php{blade}

## Widget paths {#widget-paths}

`Ui::resolveUrlUsing()` receives each path a widget links to, after the widget has filled in its
parameters, such as `:id`, and returns the address. The paths come from:

- a list's `recordUrl`, for the link on each row;
- a linkage column's `linkUrl`, for the link in each cell;
- a toolbar button's `url`.

A real resolver might add an admin prefix to every path, or map paths to named routes. This one sends
every path back to the demo page, so clicking a row, a Preview link or the New Sample button shows the
path it produced. Full addresses in `linkUrl`, starting with `https://`, `http://` or `//`, are left
alone.

<<< @/../resources/ui/reference/extending/seams/path-buttons.yaml

<<< @/../resources/ui/reference/extending/seams/path-columns.yaml

The list sets its `recordUrl` where it is built:

<<< @/../app/Controllers/Reference/ExtendingController/SeamWidgets.php#docs-path-widgets{php}

## Scheme links {#scheme-links}

`Ui::resolveLinkUsing()` registers a resolver for one scheme, such as `reference` for
`reference://` links. The resolver receives the whole link and returns its address, or `null` when it
points nowhere, such as a page that was deleted. The scheme is matched without regard to case. A link
whose scheme has no resolver, such as `https://`, is not a scheme link and is handled like any other
address.

The widgets treat a scheme link as follows:

- A linkage column shows a resolved link with the path of its address as the text, unless the column
  sets `linkText`, and shows a link that points nowhere as plain text.
- A toolbar button with a scheme link links to its address, or renders as a plain button when there
  is none.

<<< @/../resources/ui/reference/extending/seams/link-buttons.yaml

<<< @/../resources/ui/reference/extending/seams/link-columns.yaml

Scheme links let an app link to its own records, such as `page://about`, without storing an address
that changes when the record's URL does. Register one resolver per scheme, and pass `null` as the
resolver to remove one.

## Viewer timezone {#timezone}

Dates are stored in the application timezone, `app.timezone`. To show them in each person's own
timezone, give `Ui::resolveTimezoneUsing()` a closure that returns the timezone of the person viewing,
usually one saved on their account, as in
`Ui::resolveTimezoneUsing(fn () => auth()->user()?->timezone)`. The closure can return a timezone name
or a `DateTimeZone`. On the demo page it comes from the address, so pick one.

`Ui::timezone()` returns the viewer timezone, for your own views. When there is no resolver, or it
returns `null` or a name that is not a timezone, such as `Mars/Olympus`, it returns the application
timezone.

<<< @/../resources/views/reference/extending/examples/seams-timezone.blade.php{blade}

## Lists and filters in the viewer timezone {#timezone-lists}

With a viewer timezone set, a `datetime` column shows the stored time in that timezone, while a `date`
column shows the date as stored, since a date has no time to convert. A `date` filter reads the day
the visitor picks in the viewer timezone, so in Sydney the 21st of October includes a sample published
at 13:30 on the 20th, app time, which is already the 21st there.

<<< @/../resources/ui/reference/extending/seams/timezone-scopes.yaml

<<< @/../resources/ui/reference/extending/seams/timezone-columns.yaml

`useTimezone` on a column, field or scope turns the conversion on or off for it:

Type | Converts by default
------------- | -------------
**datetime** and **timetense** columns | yes. Set `useTimezone: false` to show the stored time.
**date** and **time** columns | no. Set `useTimezone: true` to convert.
**date** filter scopes | yes. Set `useTimezone: false` to read picked days in the application timezone.
**datepicker** fields in `datetime` mode | yes. Set `useTimezone: false` to show and save the value as stored.
**datepicker** fields in `date` or `time` mode | never.

## Storing scheme links {#page-link}

A field can store the scheme link itself, such as `reference://ui/buttons`, in place of the address.
The address is worked out each time the link is shown, so it follows the page when its URL changes, and
a link to a page that no longer exists shows as plain text instead of a broken link. On the demo page,
the first sample's link shows in the Scheme links list after saving.

<<< @/../resources/ui/reference/extending/seams/page-link.yaml

## Date picker in the viewer timezone {#published-at}

The date picker shows the stored time in the viewer timezone, and converts what the visitor picks back
to the application timezone when the form saves, so the database always holds application time. The
`date` and `time` modes, and `useTimezone: false`, leave the value as stored. See the
[Date Picker](/form-widgets/date-picker) for its other options.

<<< @/../resources/ui/reference/extending/seams/published-at.yaml

## Methods {#methods}

Method | Description
------------- | -------------
**Ui::resolveUrlUsing($resolver)** | sets the closure that turns a widget path into an address, or `null` to use `url()`.
**Ui::resolveLinkUsing($scheme, $resolver)** | sets the closure that turns links with this scheme into addresses, or `null` to remove it.
**Ui::url($path)** | returns the address for a widget path or a scheme link, empty when a scheme link points nowhere.
**Ui::resolveTimezoneUsing($resolver)** | sets the closure that returns the viewer timezone, or `null` to use the application timezone.
**Ui::timezone()** | returns the viewer timezone, or the application timezone when there is none.
