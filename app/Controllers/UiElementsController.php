<?php

namespace App\Controllers;

use App\Classes\ControllerBase;
use App\Controllers\UiElementsController\UiElementHandlers;
use Illuminate\Contracts\View\View;

/**
 * UiElementsController holds the UI element pages, which use the Ui factory and the controls on their own, with the AJAX handlers in the UiElementHandlers trait.
 */
class UiElementsController extends ControllerBase
{
    use UiElementHandlers;

    /**
     * buttons displays every button the Ui factory makes.
     */
    public function buttons(): View
    {
        return view('ui_elements.buttons');
    }

    /**
     * feedback displays callouts, the search input, the scoreboard, tabs and the loading and change states.
     */
    public function feedback(): View
    {
        return view('ui_elements.feedback');
    }

    /**
     * controls displays the controls used directly from markup, such as the popover, input presets and triggers.
     */
    public function controls(): View
    {
        return view('ui_elements.controls');
    }
}
