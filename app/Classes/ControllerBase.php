<?php

namespace App\Classes;

use Larajax\LarajaxController;
use Larajax\Ui\Traits\ViewMaker;

/**
 * ControllerBase is the base for pages that render Larajax UI widgets, where LarajaxController routes each widget's AJAX handlers back to the controller that built it and ViewMaker adds the view helpers the widgets use.
 */
abstract class ControllerBase extends LarajaxController
{
    use ViewMaker;
}
