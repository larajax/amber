<?php

namespace Tests\Feature;

use Larajax\Ui\Facades\Ui;
use Tests\TestCase;

class CalloutTest extends TestCase
{
    public function test_a_string_body_renders_in_the_content(): void
    {
        $html = Ui::callout('<p>Plain callout body</p>')->toHtml();

        $this->assertMatchesRegularExpression('/<div class="content">\s*<p>Plain callout body<\/p>\s*<\/div>/', $html);
    }

    public function test_a_closure_body_that_echoes_renders_in_the_content(): void
    {
        $html = Ui::callout(function () {
            echo '<p>Echoed body</p>';
        })->toHtml();

        $this->assertMatchesRegularExpression('/<div class="content">\s*<p>Echoed body<\/p>\s*<\/div>/', $html);
    }

    public function test_a_closure_body_can_return_a_view(): void
    {
        $html = Ui::callout(fn () => view('ui_elements._callout_checklist'))->label('Setup complete')->toHtml();

        $this->assertStringContainsString('Two teammates accepted their invites.', $html);
    }

    public function test_a_callout_without_a_body_renders_no_content(): void
    {
        $html = Ui::callout()->label('Heads up')->toHtml();

        $this->assertStringNotContainsString('class="content"', $html);
    }

    public function test_each_type_shows_its_icon_from_the_pack(): void
    {
        $callouts = [
            'info' => Ui::callout()->label('Info'),
            'success' => Ui::callout()->success()->label('Success'),
            'warning' => Ui::callout()->warning()->label('Warning'),
            'danger' => Ui::callout()->danger()->label('Danger'),
        ];

        foreach ($callouts as $type => $callout) {
            $html = $callout->toHtml();

            $this->assertStringContainsString('callout-'.$type, $html);
            $this->assertStringContainsString('<div class="custom-icon">', $html);
            $this->assertStringContainsString(Ui::icon('callout.'.$type), $html);
            $this->assertStringNotContainsString('no-icon', $html);
        }
    }

    public function test_tip_is_the_info_type(): void
    {
        $this->assertStringContainsString('callout-info', Ui::callout()->tip()->label('Tip')->toHtml());
    }

    public function test_a_custom_icon_replaces_the_type_icon(): void
    {
        $html = Ui::callout()->warning()->icon('ph ph-rocket-launch')->label('Launch')->toHtml();

        $this->assertStringContainsString('<i class="ph ph-rocket-launch" aria-hidden="true"></i>', $html);
        $this->assertStringContainsString('has-custom-icon', $html);
        $this->assertStringNotContainsString(Ui::icon('callout.warning'), $html);
    }

    public function test_the_icon_can_be_turned_off(): void
    {
        $html = Ui::callout()->icon(false)->label('Quiet')->toHtml();

        $this->assertStringNotContainsString('custom-icon', $html);
        $this->assertStringContainsString('no-icon', $html);
    }

    public function test_a_callout_with_only_a_body_has_no_header_or_icon(): void
    {
        $html = Ui::callout('<p>Body only</p>')->toHtml();

        $this->assertStringNotContainsString('class="header"', $html);
        $this->assertStringContainsString('no-icon', $html);
        $this->assertStringContainsString('no-title', $html);
    }

    public function test_an_action_renders_beside_the_header(): void
    {
        $html = Ui::callout()
            ->danger()
            ->label('Card expired')
            ->action(Ui::ajaxButton(label: 'Update', handler: 'onUpdate', danger: true))
            ->toHtml();

        $this->assertMatchesRegularExpression('/<div class="action">\s*<button class="btn-solid theme-danger" type="button" data-request="onUpdate">/', $html);
    }
}
