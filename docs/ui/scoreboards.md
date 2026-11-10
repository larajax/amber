---
subtitle: A row of key figures, with trends, sparklines, markers, goal meters, links, thumbnails and charts.
demo: /reference/ui/scoreboards
---
# Scoreboards

`Ui::scoreboard()` lays out key figures in a row, which scrolls sideways by dragging when it runs out
of room, as a [toolbar](/lists/toolbar) does. Open it with `->slot()` and close it with `Ui::end()`,
and write each figure with `Ui::scoreboardItem()` in between.

## Figures {#scoreboard-figures}

Each item shows a `label` as its title, the `value` as the figure, and a `comment` under it, all
escaped, with a thin line between items. A `unit`, such as `'ms'`, shows smaller after the figure.
`success: true` and `danger: true` color the figure. A `null` value shows "No data" in place of the
figure, so a figure without data does not leave a blank.

<<< @/../resources/views/reference/ui/examples/scoreboard-figures.blade.php{blade}

## Trends {#scoreboard-trends}

`change` shows how the figure moved, such as `'14.9%'`, in a pill beside it. `positive: true` tints the
pill green with an up arrow and `negative: true` red with a down arrow, and without either the pill is
grey. A trend without a `change` shows the arrow alone. The figure keeps its own color, and screen
readers hear "Increased" or "Decreased" before the change.

`compare` takes the current and previous values, and works out the change as a percentage and its
direction, when `change`, `positive` and `negative` are not set. A previous value of zero gives the
direction alone. For a figure where lower is better, such as a response time, `inverse: true` tints a
fall green and a rise red, while the arrow still points the way the figure moved.

`sparkline` takes recent values, oldest first, and draws them as a small line under the figure, scaled
between the lowest and highest value, with a dot on the latest. It is drawn on the server, with no
script, in the trend's green or red, and the primary color otherwise.

<<< @/../resources/views/reference/ui/examples/scoreboard-trends.blade.php{blade}

## Markers {#scoreboard-markers}

An item can have a marker, as an [option](/forms/fields#option-styles) can. `icon` takes icon classes and
shows them in a tile beside the text, tinted with the `color` when there is one. Without an icon,
`color` shows a swatch before the figure, and `image` a small image. A `color` that is not a hex,
`rgb()`, `hsl()` or `var()` color is left out.

<<< @/../resources/views/reference/ui/examples/scoreboard-markers.blade.php{blade}

## Goal meters {#scoreboard-meters}

`meter` adds a bar under the figure that fills to a percentage, and with no `value` the figure shows
that percentage. The bar fills in green, or in red with `inverse: true`, for a rate where higher is
worse. Screen readers read it as a progress bar.

<<< @/../resources/views/reference/ui/examples/scoreboard-meters.blade.php{blade}

## Links and help {#scoreboard-links}

`href` makes the whole item a link, such as to the list or report behind the figure, which goes through
[`url()`](/extending/seams#addresses) like other widget paths. Links inside a slot still work on their
own. `tooltip` adds an icon beside the title that shows the text on hover.

<<< @/../resources/views/reference/ui/examples/scoreboard-links.blade.php{blade}

## Markup in a figure {#scoreboard-slots}

For a figure or comment with markup, such as a link, open the item with `->slot()`: what prints after
it is the figure, and `Ui::slot('comment')` switches to the comment, up to `Ui::end()`. Open it with
`->slot('comment')` when only the comment has markup. Markup in a slot is printed as it is, so escape
any values in it.

<<< @/../resources/views/reference/ui/examples/scoreboard-slots.blade.php{blade}

## Thumbnails {#scoreboard-thumbnail}

`Ui::scoreboardThumbnail()` is an item that shows an `image`, such as a product photo, with the
`label` as its text alternative.

<<< @/../resources/views/reference/ui/examples/scoreboard-thumbnail.blade.php{blade}

## Charts {#scoreboard-charts}

A [pie or bar chart](/ui/charts) written directly inside a scoreboard lays out as one of its items, at
the height of the row.

<<< @/../resources/views/reference/ui/examples/scoreboard-charts.blade.php{blade}

## Markup {#scoreboard-markup}

The components write the scoreboard element, which you can also write by hand. Each
`scoreboard-item title-value` holds an `h4` title, the figure, and a `description` line, and the
items sit in a `data-control="toolbar"` element. A `positive` or `negative` class on the figure adds a
green or red arrow after it.

<<< @/../resources/views/reference/ui/examples/scoreboard-markup.blade.php{blade}

## Methods {#methods}

Method | Description
------------- | -------------
**Ui::scoreboard()** | opens a scoreboard with `->slot()`, closed by `Ui::end()`.
**Ui::scoreboardItem(...)** | a figure, with the arguments below. Its `->slot()` captures the figure, and `Ui::slot('comment')` the comment.
**Ui::scoreboardThumbnail($image, $label)** | an item that shows an image.

`Ui::scoreboardItem()` takes these arguments:

Argument | Description
------------- | -------------
**label** | the title above the figure.
**value** | the figure, or `null` for no data.
**unit** | a unit shown smaller after the figure.
**comment** | the line under the figure.
**icon** | icon classes, shown in a tile beside the text.
**color** | a color, which tints the icon tile, or shows as a swatch without an icon.
**image** | the address of a small image before the figure.
**change** | how the figure moved, shown in a pill beside it.
**compare** | the current and previous values, which give the change and its direction.
**positive**, **negative** | an up or down trend, which tints the pill and adds an arrow.
**sparkline** | recent values, drawn as a small line under the figure.
**success**, **danger** | colors the figure green or red.
**meter** | a percentage that adds a goal meter.
**inverse** | for a figure where lower is better: a fall is green, a rise red, and the goal meter fills in red.
**href** | makes the whole item a link.
**tooltip** | text shown from an icon beside the title.

Any other named argument, such as `id` or `class`, is added to the item's element, and likewise for
the scoreboard itself.
