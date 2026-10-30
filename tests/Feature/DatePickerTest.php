<?php

namespace Tests\Feature;

use App\Controllers\UsersController;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Larajax\Ui\Widgets\Form;
use Tests\TestCase;

class DatePickerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * weekendDays is a disableDays callable used by the tests.
     */
    public static function weekendDays($model): array
    {
        return [0, 6];
    }

    protected function renderField(array $config, array $formConfig = []): string
    {
        $user = User::factory()->create(['email_verified_at' => '2026-10-20 14:30:00']);

        $form = Form::createIn(new UsersController, $formConfig + [
            'model' => $user,
            'fields' => [
                'email_verified_at' => $config + ['label' => 'Verified', 'type' => 'datepicker'],
            ],
        ]);

        $form->bindToController();

        return (string) $form->render();
    }

    protected function ajax(string $handler, User $user, array $data = [])
    {
        return $this->withHeaders([
            'X-AJAX-HANDLER' => $handler,
            'X-Requested-With' => 'XMLHttpRequest',
        ])->post("/users/{$user->id}/edit", $data + [
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }

    public function test_the_edit_page_renders_a_date_and_time_picker(): void
    {
        $user = User::factory()->create(['email_verified_at' => '2026-10-20 14:30:00']);

        $this->get("/users/{$user->id}/edit")
            ->assertOk()
            ->assertSee('data-control="datepicker"', false)
            ->assertSee('data-mode="datetime"', false)
            ->assertSee('data-datepicker', false)
            ->assertDontSee('data-timepicker', false)
            ->assertSee('value="2026-10-20 14:30:00"', false);
    }

    public function test_saving_stores_the_value_and_an_empty_value_as_null(): void
    {
        $user = User::factory()->create(['email_verified_at' => null]);

        $this->ajax('onUpdate', $user, ['email_verified_at' => '2026-10-21 09:15:00'])->assertOk();
        $this->assertSame('2026-10-21 09:15:00', $user->refresh()->email_verified_at->toDateTimeString());

        $this->ajax('onUpdate', $user, ['email_verified_at' => ''])->assertOk();
        $this->assertNull($user->refresh()->email_verified_at);
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
        $this->assertMatchesRegularExpression('/<label class="form-control form-adorn form-adorn-end">\s*<span class="form-adorn-icon" aria-hidden="true">\s*<i class="ph ph-calendar-blank" aria-hidden="true"><\/i>\s*<\/span>\s*<input\s+type="text"\s+id="[^"]+-date-email_verified_at"\s+class="form-ghost"\s+autocomplete="off"\s+data-datepicker \/>\s*<\/label>/', $date);
        $this->assertStringNotContainsString('input-with-icon', $date);

        $time = $this->renderField(['mode' => 'time']);
        $this->assertMatchesRegularExpression('/<label class="form-control form-adorn form-adorn-end">\s*<span class="form-adorn-icon" aria-hidden="true">\s*<i class="ph ph-clock" aria-hidden="true"><\/i>\s*<\/span>\s*<input\s+type="text"\s+id="[^"]+-time-email_verified_at"\s+class="form-ghost"\s+autocomplete="off"\s+data-timepicker \/>\s*<\/label>/', $time);
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
}
