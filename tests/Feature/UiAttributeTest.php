<?php

namespace Tests\Feature;

use Larajax\Ui\Facades\Ui;
use Tests\TestCase;

class UiAttributeTest extends TestCase
{
    public function test_a_true_data_attribute_renders_as_true(): void
    {
        $html = (string) Ui::ajaxButton(label: 'Save', handler: 'onSave', dataRequestFlash: true, dataChangeMonitorCommit: true);

        $this->assertStringContainsString('data-request-flash="true"', $html);
        $this->assertStringContainsString('data-change-monitor-commit="true"', $html);
        $this->assertStringNotContainsString('data-request-flash="data-request-flash"', $html);
    }

    public function test_a_true_data_attribute_renders_as_true_on_every_component(): void
    {
        $this->assertStringContainsString('data-backdrop="true"', (string) Ui::popupButton(label: 'Open', handler: 'onOpen', dataBackdrop: true));
        $this->assertStringContainsString('data-list-checked-trigger="true"', (string) Ui::button(label: 'Delete', dataListCheckedTrigger: true));
        $this->assertStringContainsString('data-request-flash="true"', (string) Ui::dropdownItem(label: 'Archive', handler: 'onArchive', dataRequestFlash: true));
    }

    public function test_string_and_false_data_attributes_are_unchanged(): void
    {
        $html = (string) Ui::ajaxButton(label: 'Save', handler: 'onSave', dataRequestFlash: 'success,error', dataRequestConfirm: 'Sure?', dataAttachLoading: false);

        $this->assertStringContainsString('data-request-flash="success,error"', $html);
        $this->assertStringContainsString('data-request-confirm="Sure?"', $html);
        $this->assertStringNotContainsString('data-attach-loading', $html);
    }

    public function test_a_true_attribute_outside_data_keeps_its_name(): void
    {
        $this->assertStringContainsString('disabled="disabled"', (string) Ui::button(label: 'Locked', disabled: true));
    }
}
