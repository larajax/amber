---
subtitle: Pick a date, a time, or both, from a calendar and a time panel.
demo: /reference/form-widgets/date-picker
---
# Date Picker

The `datepicker` field picks a date, a time, or both, from a calendar that Bootstrap 6 styles as its
Datepicker, and a time panel. It shows values in the page language, and saves them as
`2026-11-02 09:30:00`, or as the date (`2026-11-02`) or time (`09:30:00`) part alone.

The page language (`<html lang>`) sets the default date format, the first day of the week and the month
and weekday names. An English page keeps the defaults: `YYYY-MM-DD` dates and weeks that start on
Sunday.

## Date and time {#default}

`mode: datetime` is the default. One input holds the date and the time, and the calendar has hour and
minute controls below the days. Typing in the input works too. An empty field saves as `null`.

<<< @/../resources/ui/reference/form-widgets/date-picker/default.yaml

## Date only {#date}

`mode: date` picks a day, and saves the date part alone.

<<< @/../resources/ui/reference/form-widgets/date-picker/date.yaml

## Time only {#time}

`mode: time` swaps the calendar for a time panel with the hour and minute controls on their own, and
saves the time part alone.

<<< @/../resources/ui/reference/form-widgets/date-picker/time.yaml

## Twelve hour clock {#twelve-hour}

`twelveHour` adds an AM and PM switch to the time controls, in time and datetime modes, and shows the
time with AM or PM. The value still saves on a 24 hour clock.

<<< @/../resources/ui/reference/form-widgets/date-picker/twelve-hour.yaml

## Hours only {#hours-only}

`hoursOnly` drops the minute control, for values on the hour. The minutes save as `00`.

<<< @/../resources/ui/reference/form-widgets/date-picker/hours-only.yaml

## Display format {#format}

`format` takes PHP date characters for the day, weekday, month and year, such as `d/m/Y` or `D, j M Y`,
and changes only how the date is shown and typed: the value saves as `2026-11-02` either way. Without it
the field uses the numeric date of the page language.

<<< @/../resources/ui/reference/form-widgets/date-picker/format.yaml

## Earliest and latest dates {#date-range}

`minDate` and `maxDate` disable the days outside the range. They take a date string, such as
`2026-01-01`, or a Unix timestamp.

<<< @/../resources/ui/reference/form-widgets/date-picker/date-range.yaml

## Disabled weekdays {#disabled-weekdays}

`disableDays` lists weekday numbers that cannot be picked, from `0` for Sunday to `6` for Saturday.

<<< @/../resources/ui/reference/form-widgets/date-picker/disabled-weekdays.yaml

## Disabled dates {#disabled-dates}

`disableDays` also takes dates, such as `2026-12-25`, mixed with weekday numbers if you like. For a list
that changes, name a static method as `Class::method`. It receives the form's model and returns the
weekday numbers and dates, here the end of year holidays.

<<< @/../resources/ui/reference/form-widgets/date-picker/disabled-dates.yaml

<<< @/../app/Models/Sample.php#docs-holiday-dates{php}

## Year range {#year-range}

`yearRange` sets the years offered in the calendar's year view, as a number of years either side of the
shown year (10 by default) or a `[from, to]` pair.

<<< @/../resources/ui/reference/form-widgets/date-picker/year-range.yaml

## Week layout {#week-layout}

`firstDay` starts the week on another day, from `0` for Sunday to `6` for Saturday, where the page
language decides when it is left out. `showWeekNumber` adds the week number at the start of each row.

<<< @/../resources/ui/reference/form-widgets/date-picker/week-layout.yaml

## Midnight by default {#midnight}

In datetime mode, picking a day on an empty field fills in the current time. `defaultTimeMidnight`
fills in `00:00` instead.

<<< @/../resources/ui/reference/form-widgets/date-picker/midnight.yaml

## Timezone {#timezone}

Dates are stored in the application timezone. Once a timezone resolver is set with
`Ui::resolveTimezoneUsing()` (see [URLs and Timezones](/extending/seams)), datetime values show in the
viewer's timezone and save back in the application timezone.

`useTimezone: false` shows and saves the value as stored, for times that belong to a place rather than a
viewer, such as an event in its own city. Date and time modes never convert.

<<< @/../resources/ui/reference/form-widgets/date-picker/timezone.yaml

## Read-only {#read-only}

A `readOnly` field shows the value in an input whose pickers do not open, and posts the value unchanged.

<<< @/../resources/ui/reference/form-widgets/date-picker/read-only.yaml

## Disabled {#disabled}

A `disabled` field also keeps the pickers closed, and its value is not saved.

<<< @/../resources/ui/reference/form-widgets/date-picker/disabled.yaml

## Preview {#preview}

In preview mode the server formats the value with `format` when set, or else the long locale format of
the mode: `October 20, 2026` for a date, `2:30 PM` for a time, and the weekday, date and time for
datetime. This example's form is made with `previewMode` on.

<<< @/../resources/ui/reference/form-widgets/date-picker/preview.yaml

## The control on its own {#control}

The `datepicker` control works without the form widget, in any markup. Wrap the inputs in an element
with `data-control="datepicker"` and the options as `data-*` attributes, put a visible text input
marked `data-datepicker` (or `data-timepicker` for a time) inside it, and add a hidden input marked
`data-datetime-value` that holds the value and carries the `name` that posts.

<<< @/../resources/views/reference/form-widgets/examples/datepicker-control.blade.php{blade}

The wrapper takes these attributes:

Attribute | Description
------------- | -------------
**data-mode** | `date`, `datetime` or `time`. Default: from the inputs found.
**data-format** | the display format in `YYYY`, `YY`, `MMMM`, `MMM`, `MM`, `M`, `dddd`, `ddd`, `DD`, `Do` and `D` tokens, with `[text]` for literal text.
**data-min-date**, **data-max-date** | the earliest and latest dates that can be picked, as `YYYY-MM-DD`.
**data-year-range** | years either side in the year view, or a JSON `[from, to]` pair.
**data-disable-days** | a JSON list of weekday numbers and `YYYY-MM-DD` dates that cannot be picked.
**data-first-day** | the first day of the week, `0` for Sunday.
**data-show-week-number** | `true` shows the week numbers.
**data-twelve-hour**, **data-hours-only** | `1` turns on the matching option.
**data-default-time-midnight** | present to fill in `00:00` rather than the current time.
**data-timezone** | the timezone of the shown value, used when filling in the current date and time.

## Options

Option | Description
------------- | -------------
**mode** | `datetime`, `date` or `time`. Default: `datetime`.
**format** | the date display format, in PHP date characters. Default: the numeric date of the page language.
**minDate** | the earliest date that can be picked, as a date string or timestamp.
**maxDate** | the latest date that can be picked, as a date string or timestamp.
**disableDays** | weekday numbers (`0` is Sunday) and `Y-m-d` dates that cannot be picked, or a `Class::method` that returns them.
**yearRange** | years either side of the shown year in the year view, or a `[from, to]` pair. Default: `10`.
**firstDay** | the first day of the week, `0` for Sunday to `6` for Saturday. Default: from the page language.
**showWeekNumber** | shows the week numbers. Default: `false`.
**twelveHour** | picks and shows times with AM and PM. Default: `false`.
**hoursOnly** | picks the hour only, and saves the minutes as `00`. Default: `false`.
**defaultTimeMidnight** | fills in `00:00` rather than the current time when a day is picked. Default: `false`.
**useTimezone** | converts datetime values between the viewer's and the application timezone. Default: `true`.

The field also takes the common [field properties](/forms/fields#field-properties), such as `readOnly`
and `disabled`.
