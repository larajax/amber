<?php

namespace App\ListWidgets;

use Larajax\Classes\AjaxResponse;
use Larajax\Ui\Classes\ListWidgetBase;

/**
 * Meter is a custom list widget that draws a number as a bar, filled by its share of a maximum, and names the number when the bar is clicked.
 */
class Meter extends ListWidgetBase
{
    /**
     * @var int|float max is the value that fills the bar.
     */
    public $max = 100;

    /**
     * columnDefaults keeps the row link from taking clicks meant for the bar.
     */
    public static function columnDefaults(): array
    {
        return ['clickable' => false];
    }

    /**
     * init reads the maximum from the column definition.
     */
    public function init(): void
    {
        $this->fillFromConfig(['max']);
    }

    /**
     * loadAssets adds the widget stylesheet, served from the public folder.
     */
    protected function loadAssets(): void
    {
        $this->addCss('css/widgets/meter.css');
    }

    /**
     * renderValue draws the bar for a record's value, which the list passes in.
     */
    public function renderValue($record, $value): string
    {
        $value = (float) $value;

        return $this->makePartial('meter', [
            'key' => $record->getKey(),
            'value' => $value,
            'percent' => $this->getPercent($value),
        ]);
    }

    /**
     * onShowValue names the value of the clicked bar, finding its record through the list, so only a row the list shows answers.
     */
    public function onShowValue(): AjaxResponse
    {
        $record = $this->findRecord(request()->input('key'));

        if (!$record) {
            return ajax()->flash('error', 'That record is not in the list.');
        }

        $value = (float) $record->{$this->columnName};

        return ajax()->flash('info', $this->getColumn()->label.': '.number_format($value).', '.$this->getPercent($value).'% of '.number_format($this->max).'.');
    }

    /**
     * getPercent returns a value's share of the maximum, capped at 100.
     */
    protected function getPercent(float $value): int
    {
        return $this->max > 0 ? (int) min(100, round($value / $this->max * 100)) : 0;
    }
}
