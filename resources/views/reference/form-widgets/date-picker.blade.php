@extends('layouts.app')
@section('title', 'Date Picker')
@section('content')

@include('reference._header', [
    'intro' => 'The <code>datepicker</code> field picks a date, a time, or both, from a calendar and a time panel. It shows values in the page locale, and a picked value saves as <code>2026-11-02 09:30:00</code>, or as the date or time part alone.',
])

<form data-request="onSaveExamples" data-request-flash>
    <section class="ui-section" id="default">
        <h2>
            Date and time
        </h2>
        <p class="fg-3">
            <code>mode: datetime</code> is the default. The calendar has the time controls below it, and typing in the field works too. An empty field saves as null.
        </p>
        @include('reference._form_example', ['name' => 'default'])
    </section>

    <section class="ui-section" id="date">
        <h2>
            Date only
        </h2>
        <p class="fg-3">
            <code>mode: date</code> picks a day, and a picked day saves as the date part alone.
        </p>
        @include('reference._form_example', ['name' => 'date'])
    </section>

    <section class="ui-section" id="time">
        <h2>
            Time only
        </h2>
        <p class="fg-3">
            <code>mode: time</code> swaps the calendar for a time panel, and a picked time saves as the time part alone.
        </p>
        @include('reference._form_example', ['name' => 'time'])
    </section>

    <section class="ui-section" id="twelve-hour">
        <h2>
            Twelve hour clock
        </h2>
        <p class="fg-3">
            <code>twelveHour</code> adds an AM and PM switch to the time controls, in time and datetime modes.
        </p>
        @include('reference._form_example', ['name' => 'twelve-hour'])
    </section>

    <section class="ui-section" id="hours-only">
        <h2>
            Hours only
        </h2>
        <p class="fg-3">
            <code>hoursOnly</code> drops the minute control, for values on the hour.
        </p>
        @include('reference._form_example', ['name' => 'hours-only'])
    </section>

    <section class="ui-section" id="format">
        <h2>
            Display format
        </h2>
        <p class="fg-3">
            <code>format</code> takes PHP date characters and changes only how the date is shown and typed. Without it the field uses the numeric date of the page locale.
        </p>
        @include('reference._form_example', ['name' => 'format'])
    </section>

    <section class="ui-section" id="date-range">
        <h2>
            Earliest and latest dates
        </h2>
        <p class="fg-3">
            <code>minDate</code> and <code>maxDate</code> disable the days outside the range. They take a date string or a timestamp.
        </p>
        @include('reference._form_example', ['name' => 'date-range'])
    </section>

    <section class="ui-section" id="disabled-weekdays">
        <h2>
            Disabled weekdays
        </h2>
        <p class="fg-3">
            <code>disableDays</code> lists weekday numbers, from 0 for Sunday to 6 for Saturday, that cannot be picked.
        </p>
        @include('reference._form_example', ['name' => 'disabled-weekdays'])
    </section>

    <section class="ui-section" id="disabled-dates">
        <h2>
            Disabled dates
        </h2>
        <p class="fg-3">
            <code>disableDays</code> also takes dates such as <code>2026-12-25</code>, or a static method, written <code>Class::method</code>, that returns weekdays and dates. Here <code>Sample::getHolidayDates</code> returns the end of year holidays.
        </p>
        @include('reference._form_example', ['name' => 'disabled-dates'])
    </section>

    <section class="ui-section" id="year-range">
        <h2>
            Year range
        </h2>
        <p class="fg-3">
            <code>yearRange</code> sets the years in the year view, as a number of years either side of today (10 by default) or a <code>[from, to]</code> pair.
        </p>
        @include('reference._form_example', ['name' => 'year-range'])
    </section>

    <section class="ui-section" id="week-layout">
        <h2>
            Week layout
        </h2>
        <p class="fg-3">
            <code>firstDay</code> starts the week on another day, 1 for Monday, where the page locale decides when it is left out. <code>showWeekNumber</code> adds the week numbers.
        </p>
        @include('reference._form_example', ['name' => 'week-layout'])
    </section>

    <section class="ui-section" id="midnight">
        <h2>
            Midnight by default
        </h2>
        <p class="fg-3">
            In datetime mode, picking a day on an empty field fills in the current time. <code>defaultTimeMidnight</code> fills in 00:00 instead.
        </p>
        @include('reference._form_example', ['name' => 'midnight'])
    </section>

    <section class="ui-section" id="timezone">
        <h2>
            Timezone
        </h2>
        <p class="fg-3">
            Datetime values show in the viewer's timezone, set with <code>Ui::resolveTimezoneUsing()</code>, and save back in the app timezone. <code>useTimezone: false</code> shows and saves them unconverted, for times that belong to a place. Date and time modes never convert.
        </p>
        @include('reference._form_example', ['name' => 'timezone'])
    </section>

    <section class="ui-section" id="read-only">
        <h2>
            Read-only
        </h2>
        <p class="fg-3">
            <code>readOnly</code> shows the value in a field whose pickers do not open, and posts the value unchanged.
        </p>
        @include('reference._form_example', ['name' => 'read-only'])
    </section>

    <section class="ui-section" id="disabled">
        <h2>
            Disabled
        </h2>
        <p class="fg-3">
            <code>disabled</code> also keeps the pickers closed, and the value is not saved.
        </p>
        @include('reference._form_example', ['name' => 'disabled'])
    </section>

    <section class="ui-section" id="preview">
        <h2>
            Preview
        </h2>
        <p class="fg-3">
            In preview mode the server formats the value with <code>format</code>, or the long locale format of the mode. This example's form is made with <code>previewMode</code> on.
        </p>
        @include('reference._form_example', ['name' => 'preview'])
    </section>

    <section class="ui-section" id="control">
        <h2>
            The control on its own
        </h2>
        <p class="fg-3">
            The <code>datepicker</code> control works without the form widget: a wrapper with the options as <code>data-*</code> attributes, a visible facade input marked <code>data-datepicker</code> or <code>data-timepicker</code>, and a hidden <code>data-datetime-value</code> input that holds the saved value.
        </p>
        @include('reference._example', ['view' => 'reference.form-widgets.examples.datepicker-control'])
    </section>

    @include('reference._save_bar')
</form>

@endsection
