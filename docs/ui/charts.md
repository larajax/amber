---
subtitle: Pie, bar and line charts drawn with Chart.js.
demo: /reference/ui/charts
---
# Charts

The chart components draw with [Chart.js](https://www.chartjs.org), which loads the first time a
chart shows, so pages without charts never fetch it. A pie or bar chart shows its values beside a
legend, and a line chart draws data sets over a time axis.

## Pie charts {#chart-pie}

`Ui::pieChart()` draws its `values` as a ring, `size` pixels across, 200 by default. `centerText`
shows inside the ring, `centered: true` centers the ring above its legend, and `wrapLegend: true` lays
the legend out in a row. Hovering a part of the ring shows its label and value.

<<< @/../resources/views/reference/ui/examples/chart-pie.blade.php{blade}

## Values {#chart-values}

The `values` of a pie or bar chart take the forms [options](/forms/fields#option-styles) do: a label and
its value, a label and a `[value, color]` pair, or a definition with `label`, `value` and `color` keys.
A color can be any CSS color, including a variable such as `var(--bs-success-base)`, and a value without
one takes the next from the palette, which is Bootstrap's colors. Labels are escaped.

<<< @/../resources/views/reference/ui/examples/chart-values.blade.php{blade}

## Bar charts {#chart-bar}

`Ui::barChart()` draws one bar per value, rounded at the top, `height` pixels tall, 100 by default,
scaled to the largest value. `fullWidth: true` spreads the bars across their container, with the legend below.

<<< @/../resources/views/reference/ui/examples/chart-bar.blade.php{blade}

## Line charts {#chart-line}

`Ui::lineChart()` draws its `datasets` over a time axis, `height` pixels tall, 250 by default, or any
CSS length, such as `'20rem'`. Each data set is its points keyed by its name, or a definition with a
`color`, which takes the next from the palette when it has none, its `data`, and the Flot style `lines` and `points` options, such as
`'lines' => ['fill' => false]` for a line without the area under it.

Lines are smooth, without overshooting their points, and the area under them fades out toward the
bottom. Points show on hover, where the tooltip lists the value of every data set at that date.
`'lines' => ['smooth' => false]` draws straight lines, and `'points' => ['show' => true]` shows the
points always.

The points are values keyed by date, as above, or `[date, value]` pairs, as in the next example. A date
is a date string, a `DateTimeInterface`, such as a Carbon date, or a timestamp in milliseconds. Dates
show as they are written, whatever the visitor's timezone, so a sale at 9am on the 3rd shows at 9am on
the 3rd. `timeMode: 'weeks'` shades the weekends.

<<< @/../resources/views/reference/ui/examples/chart-line.blade.php{blade}

## Zooming {#chart-zoom}

`zoomable: true` lets the visitor drag across a line chart to zoom in on the range they cover.
`resetZoomLink` takes a selector for a link that shows while the chart is zoomed and returns it to the
whole range when clicked.

<<< @/../resources/views/reference/ui/examples/chart-zoom.blade.php{blade}

## Chart.js options {#chart-config}

`config` takes [Chart.js options](https://www.chartjs.org/docs/latest/configuration/), merged over
the chart's own, for anything the arguments do not cover, such as the axis range or the tick unit.
Every chart takes it.

<<< @/../resources/views/reference/ui/examples/chart-config.blade.php{blade}

## Events {#chart-events}

A chart dispatches `chart-pie:init`, `chart-bar:init` or `chart-line:init` before it draws, with the
Chart.js config in `event.detail.config`, which a listener may change, and `chart-pie:ready`,
`chart-bar:ready` or `chart-line:ready` after, with the Chart.js instance in `event.detail.chart`. The
instance is also on the control:

```js
jax.fetchControl(element, 'chart-line').chart
```

<<< @/../resources/views/reference/ui/examples/chart-events.blade.php{blade}

A line chart's control also has `addDataSet()`, which adds a data set and draws the chart again, and
`clearZoom()`, which returns it to the whole range.

## Markup {#chart-markup}

The components write the chart controls' markup, which you can also write by hand. A line chart reads
its data sets from `data-chart="dataset"` spans, whose `data-set-data` lists `[timestamp, value]`
points, and takes Flot style options in `data-chart-options`.

<<< @/../resources/views/reference/ui/examples/chart-markup.blade.php{blade}

`data-chart-options` reads these keys, and `data-chart-config` takes Chart.js options as JSON for
anything else:

Key | Description
------------- | -------------
**xaxis.mode** | `time`, the default, or any other value for a plain number axis.
**xaxis.minTickSize** | the smallest tick unit, such as `[1, 'day']`.
**xaxis.tickSize** | the tick unit and step, such as `[2, 'month']`.
**xaxis.timeformat** | the tick label format, a strftime pattern such as `%b %d`.
**xaxis.min**, **xaxis.max**, **yaxis.min**, **yaxis.max** | the axis range.
**xaxis.ticks**, **yaxis.ticks** | the most ticks to show.
**yaxis.tickDecimals** | the decimal places of the value ticks.
**legend.show** | `false` hides the legend.
**series.lines**, **series.points** | the `lines` and `points` options of every data set, such as `smooth` and `show`.
**grid.hoverable**, **tooltip** | `false` turns off the tooltip.
**colors** | the colors of data sets that set none.

A pie or bar chart reads its values from a list, each item a label with its value in a `span` and an
optional `data-color`:

```html
<div class="control-chart" data-control="chart-pie" data-size="160" data-center-text="114">
    <ul>
        <li data-color="#95b753">Published <span>84</span></li>
        <li data-color="#e5a91a">Drafts <span>12</span></li>
    </ul>
</div>
```

## Methods {#methods}

Method | Description
------------- | -------------
**Ui::pieChart($values, $size, $centerText, $centered, $wrapLegend, $config)** | a ring of values with a legend.
**Ui::barChart($values, $height, $fullWidth, $wrapLegend, $config)** | a bar per value with a legend.
**Ui::lineChart($datasets, $height, $timeMode, $zoomable, $resetZoomLink, $config)** | data sets over a time axis.

Any other named argument, such as `id` or `class`, is added to the chart's element.
