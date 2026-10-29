<?php

namespace App\FormWidgets;

use Carbon\Carbon;
use Larajax\Ui\Classes\FormWidgetBase;

/**
 * TimeChecker is a custom form widget that compares the server clock with the browser clock and stamps the checked server time into its field.
 */
class TimeChecker extends FormWidgetBase
{
    /**
     * @var string format used to display the server times.
     */
    public $format = 'D j M Y, H:i:s T';

    /**
     * @var string defaultAlias to identify this widget.
     */
    protected $defaultAlias = 'timechecker';

    /**
     * init reads the display format from the field configuration.
     */
    public function init(): void
    {
        $this->fillFromConfig(['format']);
    }

    /**
     * loadAssets adds the widget stylesheet and script, served from the public folder.
     */
    protected function loadAssets(): void
    {
        $this->addCss('css/widgets/timechecker.css');
        $this->addJs('js/widgets/timechecker.js', ['defer' => true]);
    }

    /**
     * render the widget with its saved stamp.
     */
    public function render(): string
    {
        $this->prepareVars();

        return $this->makePartial('timechecker');
    }

    /**
     * prepareVars for the widget partials, where a checked server time replaces the saved stamp.
     */
    protected function prepareVars(?Carbon $serverTime = null): void
    {
        $stamp = $serverTime ?: $this->makeCarbon($this->getLoadValue());

        $this->vars['name'] = $this->getFieldName();
        $this->vars['value'] = $stamp ? $stamp->toDateTimeString() : '';
        $this->vars['stampLabel'] = $stamp ? $stamp->format($this->format) : null;
        $this->vars['serverTimeMs'] = $serverTime ? $serverTime->getTimestampMs() : null;
        $this->vars['isChecked'] = $serverTime !== null;
    }

    /**
     * onCheckTime reads the server clock and updates the widget, stamping the new time into the field.
     *
     * @return array<string, string>
     */
    public function onCheckTime(): array
    {
        $this->prepareVars(Carbon::now());

        return ['#'.$this->getId() => $this->makePartial('clock')];
    }

    /**
     * getSaveValue stores the stamped time, or null when the field holds none.
     */
    public function getSaveValue($value): ?string
    {
        return strlen((string) $value) ? (string) $value : null;
    }

    /**
     * makeCarbon converts a stored value into a date, ignoring values that do not parse.
     */
    protected function makeCarbon(mixed $value): ?Carbon
    {
        if (!$value) {
            return null;
        }

        try {
            return Carbon::parse($value);
        }
        catch (\Throwable $ex) {
            return null;
        }
    }
}
