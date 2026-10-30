<?php

namespace Tests\Feature\Reference\FormWidgets;

use App\Controllers\Reference\FormWidgetsController;
use App\Models\Sample;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Larajax\Ui\Facades\Ui;
use Larajax\Ui\Widgets\Form;
use Tests\TestCase;

class DatePickerTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Ui::resolveTimezoneUsing(null);

        parent::tearDown();
    }

    /**
     * weekendDays is a disableDays callable used by the tests.
     */
    public static function weekendDays($model): array
    {
        return [0, 6];
    }

    protected function makeDateForm(array $config, array $formConfig = []): Form
    {
        $sample = Sample::factory()->create(['published_at' => '2026-10-20 14:30:00']);

        $form = Form::createIn(new FormWidgetsController, $formConfig + [
            'model' => $sample,
            'fields' => [
                'published_at' => $config + ['label' => 'Published', 'type' => 'datepicker'],
            ],
        ]);

        $form->bindToController();

        return $form;
    }

    protected function renderField(array $config, array $formConfig = []): string
    {
        return (string) $this->makeDateForm($config, $formConfig)->render();
    }

    protected function saveExamples(array $data): TestResponse
    {
        return $this->withHeaders([
            'X-AJAX-HANDLER' => 'onSaveExamples',
            'X-Requested-With' => 'XMLHttpRequest',
        ])->post(route('reference.form-widgets.date-picker'), $data);
    }

    /**
     * makeDatedSample creates the reference sample with a value in every date picker example.
     */
    protected function makeDatedSample(array $attributes = []): Sample
    {
        return Sample::factory()->create($attributes + [
            'published_at' => '2026-10-20 14:30:00',
            'starts_on' => '2026-11-02',
            'opens_at' => '09:00:00',
            'closes_at' => '17:30:00',
            'restock_on' => '2026-11-02',
            'webinar_at' => '2026-11-18 19:00:00',
            'sold_at' => '2026-10-29 11:20:00',
        ]);
    }

    public function test_the_page_renders_every_example_beside_its_yaml(): void
    {
        $this->makeDatedSample();

        $html = $this->get(route('reference.form-widgets.date-picker'))->assertOk()->getContent();

        $examples = [
            'default', 'date', 'time', 'twelve-hour', 'hours-only', 'format', 'date-range', 'disabled-weekdays',
            'disabled-dates', 'year-range', 'week-layout', 'midnight', 'timezone', 'read-only', 'disabled', 'preview',
        ];

        foreach ($examples as $example) {
            $this->assertStringContainsString("resources/ui/reference/form-widgets/date-picker/{$example}.yaml", $html);
        }

        // Fifteen widgets plus the control on its own, while the preview example shows formatted text
        $this->assertSame(16, substr_count($html, 'data-control="datepicker"'));
        $this->assertStringContainsString('Thursday, October 29, 2026 11:20 AM', $html);
        $this->assertSame(3, substr_count($html, 'data-mode="time"'));
        $this->assertSame(9, substr_count($html, 'data-mode="date"'));

        // The date and time facades show their icon inside an adorned field, after the input
        $this->assertSame(12, preg_match_all('/<label class="form-control form-adorn form-adorn-end">\s*<span class="form-adorn-icon" aria-hidden="true">\s*<i class="ph ph-calendar-blank" aria-hidden="true"><\/i>\s*<\/span>\s*<input[^>]*class="form-ghost"[^>]*data-datepicker \/>\s*<\/label>/', $html));
        $this->assertSame(3, preg_match_all('/<label class="form-control form-adorn form-adorn-end">\s*<span class="form-adorn-icon" aria-hidden="true">\s*<i class="ph ph-clock" aria-hidden="true"><\/i>\s*<\/span>\s*<input[^>]*class="form-ghost"[^>]*data-timepicker \/>\s*<\/label>/', $html));

        // Each example's options reach the control
        $this->assertStringContainsString('value="2026-10-20 14:30:00"', $html);
        $this->assertStringContainsString('data-twelve-hour="1"', $html);
        $this->assertStringContainsString('data-hours-only="1"', $html);
        $this->assertStringContainsString('data-format="ddd, D MMM YYYY"', $html);
        $this->assertStringContainsString('data-min-date="2026-01-01 00:00:00"', $html);
        $this->assertStringContainsString('data-max-date="2027-06-30 00:00:00"', $html);
        $this->assertStringContainsString('data-disable-days="[0,6]"', $html);
        $this->assertStringContainsString('data-disable-days="[&quot;'.date('Y').'-12-25&quot;,&quot;'.date('Y').'-12-26&quot;', $html);
        $this->assertStringContainsString('data-year-range="[2015,2026]"', $html);
        $this->assertStringContainsString('data-first-day="1"', $html);
        $this->assertSame(2, substr_count($html, 'data-show-week-number="true"'));
        $this->assertStringContainsString('data-default-time-midnight="1"', $html);
        $this->assertMatchesRegularExpression('/<input[^>]*readonly="readonly"[^>]*data-datepicker \/>/', $html);
        $this->assertMatchesRegularExpression('/<input[^>]*disabled="disabled"[^>]*data-datepicker \/>/', $html);
        $this->assertStringContainsString('data-request="onSaveExamples"', $html);
    }

    public function test_the_control_on_its_own_renders_beside_its_blade(): void
    {
        $html = $this->get(route('reference.form-widgets.date-picker'))->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/data-control="datepicker"\s+data-mode="date"\s+data-min-date="2026-01-01"/', $html);
        $this->assertMatchesRegularExpression('/<input type="hidden" name="meeting_on" value="2026-11-12" data-datetime-value \/>/', $html);
        $this->assertMatchesRegularExpression('/&lt;div\s+class=&quot;field-datepicker&quot;\s+data-control=&quot;datepicker&quot;/', $html);
    }

    public function test_the_timezone_example_keeps_the_stored_time_while_the_default_converts(): void
    {
        $this->makeDatedSample();
        Ui::resolveTimezoneUsing(fn () => 'Australia/Sydney');

        $html = $this->get(route('reference.form-widgets.date-picker'))->assertOk()->getContent();

        $this->assertStringContainsString('value="2026-10-21 01:30:00"', $html);
        $this->assertStringContainsString('value="2026-11-18 19:00:00"', $html);
        $this->assertSame(3, substr_count($html, 'data-timezone="Australia/Sydney"'));
    }

    public function test_saving_the_page_stores_every_example_on_the_reference_sample(): void
    {
        $sample = $this->makeDatedSample(['inspected_at' => '2026-10-28 15:45:00', 'discontinued_on' => '2027-12-31']);

        $this->saveExamples([
            'published_at' => '2026-10-21 09:15:00',
            'starts_on' => '2026-11-03',
            'opens_at' => '10:30:00',
            'closes_at' => '18:00:00',
            'reminder_at' => '07:00:00',
            'restock_on' => '2026-11-09',
            'launch_on' => '2026-04-01',
            'delivery_on' => '2026-11-05',
            'ends_on' => '2026-11-30',
            'manufactured_on' => '2024-02-01',
            'review_on' => '2026-12-14',
            'expires_at' => '2027-01-31 00:00:00',
            'webinar_at' => '2026-11-18 20:00:00',
            'inspected_at' => '2026-10-28 15:45:00',
            'discontinued_on' => '2030-01-01',
        ])->assertOk();

        $sample->refresh();
        $this->assertSame('2026-10-21 09:15:00', $sample->published_at);
        $this->assertSame('2026-11-03', $sample->starts_on);
        $this->assertSame('10:30:00', $sample->opens_at);
        $this->assertSame('18:00:00', $sample->closes_at);
        $this->assertSame('07:00:00', $sample->reminder_at);
        $this->assertSame('2026-11-09', $sample->restock_on);
        $this->assertSame('2026-04-01', $sample->launch_on);
        $this->assertSame('2026-11-05', $sample->delivery_on);
        $this->assertSame('2026-11-30', $sample->ends_on);
        $this->assertSame('2024-02-01', $sample->manufactured_on);
        $this->assertSame('2026-12-14', $sample->review_on);
        $this->assertSame('2027-01-31 00:00:00', $sample->expires_at);
        $this->assertSame('2026-11-18 20:00:00', $sample->webinar_at);
        $this->assertSame('2026-10-28 15:45:00', $sample->inspected_at);

        // The disabled example is never saved, and the preview example posts nothing
        $this->assertSame('2027-12-31', $sample->discontinued_on);
        $this->assertSame('2026-10-29 11:20:00', $sample->sold_at);
    }

    public function test_an_unchanged_date_or_time_saves_its_part_alone(): void
    {
        $sample = $this->makeDatedSample();

        // The lockers render with both parts, as an untouched field posts them back
        $this->saveExamples([
            'starts_on' => '2026-11-02 00:00:00',
            'opens_at' => '2026-10-30 09:00:00',
            'closes_at' => '2026-10-30 17:30:00',
        ])->assertOk();

        $sample->refresh();
        $this->assertSame('2026-11-02', $sample->starts_on);
        $this->assertSame('09:00:00', $sample->opens_at);
        $this->assertSame('17:30:00', $sample->closes_at);
    }

    public function test_an_emptied_field_saves_as_null(): void
    {
        $sample = $this->makeDatedSample();

        $this->saveExamples(['published_at' => '', 'starts_on' => '', 'opens_at' => ''])->assertOk();

        $sample->refresh();
        $this->assertNull($sample->published_at);
        $this->assertNull($sample->starts_on);
        $this->assertNull($sample->opens_at);
    }

    public function test_the_default_field_renders_a_date_and_time_picker(): void
    {
        $html = $this->renderField([]);

        $this->assertStringContainsString('data-control="datepicker"', $html);
        $this->assertStringContainsString('data-mode="datetime"', $html);
        $this->assertStringContainsString('data-datepicker', $html);
        $this->assertStringNotContainsString('data-timepicker', $html);
        $this->assertStringContainsString('value="2026-10-20 14:30:00"', $html);
    }

    public function test_options_render_as_control_attributes(): void
    {
        $html = $this->renderField([
            'mode' => 'date',
            'format' => 'd/m/Y',
            'minDate' => '2026-01-01',
            'yearRange' => [1990, 2030],
            'disableDays' => self::class.'::weekendDays',
            'firstDay' => 1,
            'hoursOnly' => true,
            'defaultTimeMidnight' => true,
        ]);

        $this->assertStringContainsString('data-format="DD/MM/YYYY"', $html);
        $this->assertStringContainsString('data-min-date="2026-01-01 00:00:00"', $html);
        $this->assertStringContainsString('data-year-range="[1990,2030]"', $html);
        $this->assertStringContainsString('data-disable-days="[0,6]"', $html);
        $this->assertStringContainsString('data-first-day="1"', $html);
        $this->assertStringContainsString('data-hours-only="1"', $html);
        $this->assertStringContainsString('data-default-time-midnight="1"', $html);
        $this->assertStringContainsString('data-ignore-timezone', $html);
        $this->assertStringContainsString('data-datepicker', $html);
        $this->assertStringNotContainsString('data-timepicker', $html);
    }

    public function test_a_pair_of_disabled_weekdays_is_a_list_and_not_a_callable(): void
    {
        $html = $this->renderField(['mode' => 'date', 'disableDays' => [0, 6]]);

        $this->assertStringContainsString('data-disable-days="[0,6]"', $html);

        $html = $this->renderField(['mode' => 'date', 'disableDays' => [self::class, 'weekendDays']]);

        $this->assertStringContainsString('data-disable-days="[0,6]"', $html);
    }

    public function test_first_day_renders_only_when_configured_so_the_locale_applies_otherwise(): void
    {
        $this->assertStringNotContainsString('data-first-day', $this->renderField([]));
        $this->assertStringContainsString('data-first-day="0"', $this->renderField(['firstDay' => 0]));
    }

    public function test_time_mode_renders_only_the_time_picker(): void
    {
        $html = $this->renderField(['mode' => 'time', 'twelveHour' => true]);

        $this->assertStringContainsString('data-mode="time"', $html);
        $this->assertStringContainsString('data-timepicker', $html);
        $this->assertStringNotContainsString('data-datepicker', $html);
        $this->assertStringContainsString('data-twelve-hour="1"', $html);
    }

    public function test_the_facade_sits_in_an_adorned_field_with_the_icon_at_the_end(): void
    {
        $date = $this->renderField(['mode' => 'datetime']);
        $this->assertMatchesRegularExpression('/<label class="form-control form-adorn form-adorn-end">\s*<span class="form-adorn-icon" aria-hidden="true">\s*<i class="ph ph-calendar-blank" aria-hidden="true"><\/i>\s*<\/span>\s*<input\s+type="text"\s+id="[^"]+-date-published_at"\s+class="form-ghost"\s+autocomplete="off"\s+data-datepicker \/>\s*<\/label>/', $date);
        $this->assertStringNotContainsString('input-with-icon', $date);

        $time = $this->renderField(['mode' => 'time']);
        $this->assertMatchesRegularExpression('/<label class="form-control form-adorn form-adorn-end">\s*<span class="form-adorn-icon" aria-hidden="true">\s*<i class="ph ph-clock" aria-hidden="true"><\/i>\s*<\/span>\s*<input\s+type="text"\s+id="[^"]+-time-published_at"\s+class="form-ghost"\s+autocomplete="off"\s+data-timepicker \/>\s*<\/label>/', $time);
    }

    public function test_a_readonly_or_disabled_facade_keeps_its_state_on_the_input_inside_the_adorned_field(): void
    {
        $this->assertMatchesRegularExpression('/<label class="form-control form-adorn form-adorn-end">\s*<span class="form-adorn-icon"[^>]*>.*?<\/span>\s*<input[^>]*class="form-ghost"[^>]*readonly="readonly"[^>]*data-datepicker \/>\s*<\/label>/s', $this->renderField(['readOnly' => true]));
        $this->assertMatchesRegularExpression('/<label class="form-control form-adorn form-adorn-end">\s*<span class="form-adorn-icon"[^>]*>.*?<\/span>\s*<input[^>]*class="form-ghost"[^>]*disabled="disabled"[^>]*data-datepicker \/>\s*<\/label>/s', $this->renderField(['disabled' => true]));
    }

    public function test_preview_mode_formats_the_value_on_the_server(): void
    {
        $html = $this->renderField([], ['previewMode' => true]);
        $this->assertStringContainsString('Tuesday, October 20, 2026 2:30 PM', $html);
        $this->assertStringNotContainsString('data-control="datepicker"', $html);

        $this->assertStringContainsString('October 20, 2026', $this->renderField(['mode' => 'date'], ['previewMode' => true]));
        $this->assertStringContainsString('2:30 PM', $this->renderField(['mode' => 'time'], ['previewMode' => true]));
        $this->assertStringContainsString('20/10/2026', $this->renderField(['format' => 'd/m/Y'], ['previewMode' => true]));
    }

    public function test_date_and_time_modes_save_their_part_alone(): void
    {
        $date = $this->makeDateForm(['mode' => 'date'])->getFormWidget('published_at');
        $this->assertSame('2026-11-02', $date->getSaveValue('2026-11-02 00:00:00'));
        $this->assertSame('2026-11-02', $date->getSaveValue('2026-11-02'));

        $time = $this->makeDateForm(['mode' => 'time'])->getFormWidget('published_at');
        $this->assertSame('09:00:00', $time->getSaveValue('2026-10-30 09:00:00'));
        $this->assertSame('09:00:00', $time->getSaveValue('09:00'));

        $this->assertSame('not a date', $time->getSaveValue('not a date'));
        $this->assertNull($time->getSaveValue(''));
    }
}
