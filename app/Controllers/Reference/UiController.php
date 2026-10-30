<?php

namespace App\Controllers\Reference;

use App\Classes\ReferenceControllerBase;
use App\Controllers\Reference\UiController\ButtonHandlers;
use App\Controllers\Reference\UiController\DropdownHandlers;
use App\Controllers\Reference\UiController\FeedbackHandlers;
use App\Controllers\Reference\UiController\InputHandlers;
use App\Controllers\Reference\UiController\PopupHandlers;
use Illuminate\Contracts\View\View;

/**
 * UiController holds the UI reference pages, which use the Ui factory and the controls on their own, with each page's AJAX handlers in its own trait.
 */
class UiController extends ReferenceControllerBase
{
    use ButtonHandlers;
    use DropdownHandlers;
    use FeedbackHandlers;
    use InputHandlers;
    use PopupHandlers;

    /**
     * buttons displays the Buttons page.
     */
    public function buttons(): View
    {
        return view('reference.ui.buttons');
    }

    /**
     * dropdowns displays the Dropdowns page.
     */
    public function dropdowns(): View
    {
        return view('reference.ui.dropdowns');
    }

    /**
     * popups displays the Popups page.
     */
    public function popups(): View
    {
        return view('reference.ui.popups');
    }

    /**
     * tabs displays the Tabs page.
     */
    public function tabs(): View
    {
        return view('reference.ui.tabs');
    }

    /**
     * feedback displays the Feedback page.
     */
    public function feedback(): View
    {
        return view('reference.ui.feedback');
    }

    /**
     * inputs displays the Inputs page.
     */
    public function inputs(): View
    {
        return view('reference.ui.inputs');
    }
}
