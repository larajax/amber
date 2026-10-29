<?php

namespace Tests\Feature;

use App\Controllers\KitchenSinkController;
use App\FormWidgets\TimeChecker;
use App\Models\Sample;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Larajax\Ui\Classes\WidgetManager;
use Larajax\Ui\Widgets\Form;
use Tests\TestCase;

class TimeCheckerWidgetTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    /**
     * makeForm builds a form holding the time checker for the given sample.
     */
    protected function makeForm(Sample $sample, array $config = []): Form
    {
        $form = Form::createIn(new KitchenSinkController, [
            'model' => $sample,
            'fields' => [
                'checked_at' => $config + ['label' => 'Server Time Check', 'type' => 'timechecker'],
            ],
        ]);

        $form->bindToController();

        return $form;
    }

    public function test_the_app_registers_the_widget_under_its_code(): void
    {
        $this->assertSame(TimeChecker::class, WidgetManager::instance()->resolveFormWidget('timechecker'));
    }

    public function test_the_widget_renders_the_saved_stamp_and_its_check_button(): void
    {
        $sample = Sample::factory()->create(['checked_at' => '2026-10-30 09:00:00']);

        $html = (string) $this->makeForm($sample, ['format' => 'j M Y H:i'])->render();

        $this->assertStringContainsString('data-control="timechecker"', $html);
        $this->assertMatchesRegularExpression('/name="checked_at"\s+value="2026-10-30 09:00:00"/', $html);
        $this->assertStringContainsString('Saved stamp', $html);
        $this->assertStringContainsString('30 Oct 2026 09:00', $html);
        $this->assertStringContainsString('data-request="formCheckedAt::onCheckTime"', $html);
        $this->assertStringNotContainsString('data-server-time', $html);
    }

    public function test_an_empty_field_says_it_has_not_been_checked(): void
    {
        $html = (string) $this->makeForm(Sample::factory()->create())->render();

        $this->assertStringContainsString('Not checked yet', $html);
        $this->assertMatchesRegularExpression('/name="checked_at"\s+value=""/', $html);
    }

    public function test_the_widget_adds_its_own_stylesheet_and_script(): void
    {
        $widget = $this->makeForm(Sample::factory()->create())->getFormWidget('checked_at');

        $this->assertSame([
            'js' => [asset('js/widgets/timechecker.js')],
            'css' => [asset('css/widgets/timechecker.css')],
        ], $widget->getAssetPaths());
    }

    public function test_checking_the_time_stamps_the_server_time_into_the_field(): void
    {
        Carbon::setTestNow('2026-10-30 21:15:42');
        $sample = Sample::factory()->create();

        $resp = $this->withHeaders([
            'X-AJAX-HANDLER' => 'formCheckedAt::onCheckTime',
            'X-Requested-With' => 'XMLHttpRequest',
        ])->post("/kitchen-sink/{$sample->id}/edit");

        $resp->assertOk();
        $op = $resp->json('__ajax.ops.0');
        $this->assertStringEndsWith('-checked_at', $op['selector']);
        $this->assertMatchesRegularExpression('/name="checked_at"\s+value="2026-10-30 21:15:42"/', $op['html']);
        $this->assertStringContainsString('data-server-time="'.Carbon::now()->getTimestampMs().'"', $op['html']);
        $this->assertStringContainsString('Server clock', $op['html']);

        // Checking only stamps the field, and saving the form stores it
        $this->assertNull($sample->fresh()->checked_at);
    }

    public function test_the_save_value_is_the_stamp_or_null(): void
    {
        $widget = $this->makeForm(Sample::factory()->create())->getFormWidget('checked_at');

        $this->assertSame('2026-10-30 21:15:42', $widget->getSaveValue('2026-10-30 21:15:42'));
        $this->assertNull($widget->getSaveValue(''));
    }
}
