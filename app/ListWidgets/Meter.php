<?php

namespace App\ListWidgets;

use Larajax\Ui\Classes\ListWidgetBase;

/**
 * Meter is a custom list widget that draws a number as a bar, filled by its share of a maximum.
 */
class Meter extends ListWidgetBase
{
    /**
     * @var int|float max is the value that fills the bar.
     */
    public $max = 100;

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
            'value' => $value,
            'percent' => $this->max > 0 ? (int) min(100, round($value / $this->max * 100)) : 0,
        ]);
    }
}
