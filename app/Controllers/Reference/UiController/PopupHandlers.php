<?php

namespace App\Controllers\Reference\UiController;

use Illuminate\Support\Sleep;
use Larajax\Classes\AjaxResponse;

/**
 * PopupHandlers holds the AJAX handlers behind the Popups page.
 */
trait PopupHandlers
{
    /**
     * onLoadDialog renders the demo dialog, titled with the size the popup button asked for.
     */
    public function onLoadDialog(): string
    {
        $size = (string) input('size');

        if (ctype_digit($size)) {
            $title = "{$size}px dialog";
        }
        elseif (in_array($size, $this->getDialogSizes(), true)) {
            $title = ucfirst($size).' dialog';
        }
        else {
            $title = 'Default dialog';
        }

        return view('reference.ui._dialog', ['title' => $title])->render();
    }

    /**
     * onSubmitDialog handles the dialog's submit button, which closes the popup when it succeeds.
     */
    public function onSubmitDialog(): AjaxResponse
    {
        Sleep::for(500)->milliseconds();

        return ajax()->flash('success', 'The dialog was submitted.');
    }

    /**
     * getDialogSizes returns the named popup sizes the dialog demo accepts.
     *
     * @return array<int, string>
     */
    protected function getDialogSizes(): array
    {
        return ['tiny', 'small', 'large', 'huge', 'giant', 'adaptive', 'auto', 'fullscreen'];
    }
}
