@extends('layouts.app')
@section('title', 'Date Picker')
@section('content')

@include('reference._header', [
    'intro' => 'The <code>datepicker</code> field picks a date, a time, or both, from a calendar and a time panel.',
])

<form data-request="onSaveExamples" data-request-flash>
    <section class="ui-section" id="default">
        @include('reference._heading', ['id' => 'default', 'title' => 'Date and time', 'caption' => 'The default <code>mode: datetime</code>, with time controls below the calendar.'])
        @include('reference._form_example', ['name' => 'default'])
    </section>

    <section class="ui-section" id="date">
        @include('reference._heading', ['id' => 'date', 'title' => 'Date only', 'caption' => '<code>mode: date</code> picks a day.'])
        @include('reference._form_example', ['name' => 'date'])
    </section>

    <section class="ui-section" id="time">
        @include('reference._heading', ['id' => 'time', 'title' => 'Time only', 'caption' => '<code>mode: time</code> opens the time panel on its own.'])
        @include('reference._form_example', ['name' => 'time'])
    </section>

    <section class="ui-section" id="twelve-hour">
        @include('reference._heading', ['id' => 'twelve-hour', 'title' => 'Twelve hour clock', 'caption' => '<code>twelveHour</code> adds an AM and PM switch.'])
        @include('reference._form_example', ['name' => 'twelve-hour'])
    </section>

    <section class="ui-section" id="hours-only">
        @include('reference._heading', ['id' => 'hours-only', 'title' => 'Hours only', 'caption' => '<code>hoursOnly</code> drops the minute control.'])
        @include('reference._form_example', ['name' => 'hours-only'])
    </section>

    <section class="ui-section" id="format">
        @include('reference._heading', ['id' => 'format', 'title' => 'Display format', 'caption' => '<code>format</code> in PHP date characters.'])
        @include('reference._form_example', ['name' => 'format'])
    </section>

    <section class="ui-section" id="date-range">
        @include('reference._heading', ['id' => 'date-range', 'title' => 'Earliest and latest dates', 'caption' => '<code>minDate</code> and <code>maxDate</code>.'])
        @include('reference._form_example', ['name' => 'date-range'])
    </section>

    <section class="ui-section" id="disabled-weekdays">
        @include('reference._heading', ['id' => 'disabled-weekdays', 'title' => 'Disabled weekdays', 'caption' => '<code>disableDays: [0, 6]</code> blocks weekends.'])
        @include('reference._form_example', ['name' => 'disabled-weekdays'])
    </section>

    <section class="ui-section" id="disabled-dates">
        @include('reference._heading', ['id' => 'disabled-dates', 'title' => 'Disabled dates', 'caption' => '<code>disableDays</code> naming <code>Sample::getHolidayDates</code>.'])
        @include('reference._form_example', ['name' => 'disabled-dates'])
    </section>

    <section class="ui-section" id="year-range">
        @include('reference._heading', ['id' => 'year-range', 'title' => 'Year range', 'caption' => '<code>yearRange: [2015, 2026]</code> in the year view.'])
        @include('reference._form_example', ['name' => 'year-range'])
    </section>

    <section class="ui-section" id="week-layout">
        @include('reference._heading', ['id' => 'week-layout', 'title' => 'Week layout', 'caption' => '<code>firstDay: 1</code> and <code>showWeekNumber</code>.'])
        @include('reference._form_example', ['name' => 'week-layout'])
    </section>

    <section class="ui-section" id="midnight">
        @include('reference._heading', ['id' => 'midnight', 'title' => 'Midnight by default', 'caption' => '<code>defaultTimeMidnight</code> fills in 00:00.'])
        @include('reference._form_example', ['name' => 'midnight'])
    </section>

    <section class="ui-section" id="timezone">
        @include('reference._heading', ['id' => 'timezone', 'title' => 'Timezone', 'caption' => '<code>useTimezone: false</code> shows and saves the stored time.'])
        @include('reference._form_example', ['name' => 'timezone'])
    </section>

    <section class="ui-section" id="read-only">
        @include('reference._heading', ['id' => 'read-only', 'title' => 'Read-only', 'caption' => '<code>readOnly</code> keeps the pickers closed and posts the value.'])
        @include('reference._form_example', ['name' => 'read-only'])
    </section>

    <section class="ui-section" id="disabled">
        @include('reference._heading', ['id' => 'disabled', 'title' => 'Disabled', 'caption' => '<code>disabled</code> keeps the pickers closed and saves nothing.'])
        @include('reference._form_example', ['name' => 'disabled'])
    </section>

    <section class="ui-section" id="preview">
        @include('reference._heading', ['id' => 'preview', 'title' => 'Preview', 'caption' => 'The long locale format in a <code>previewMode</code> form.'])
        @include('reference._form_example', ['name' => 'preview'])
    </section>

    <section class="ui-section" id="control">
        @include('reference._heading', ['id' => 'control', 'title' => 'The control on its own', 'caption' => 'The <code>datepicker</code> control in plain markup, without the form widget.'])
        @include('reference._example', ['view' => 'reference.form-widgets.examples.datepicker-control'])
    </section>

    @include('reference._save_bar')
</form>

@endsection
