<?php

namespace App\Controllers\Reference\UiController;

use Larajax\Classes\AjaxResponse;

/**
 * ButtonHandlers holds the AJAX handlers behind the Buttons page.
 */
trait ButtonHandlers
{
    /**
     * onFlashMessage shows a flash message, as the handler for the plain AJAX button.
     */
    public function onFlashMessage(): AjaxResponse
    {
        return ajax()->flash('success', 'Hello from the server.');
    }

    /**
     * onConfirmedAction runs after the visitor accepts the confirmation prompt.
     */
    public function onConfirmedAction(): AjaxResponse
    {
        return ajax()->flash('success', 'The action ran after you confirmed it.');
    }

    /**
     * onArchiveItem reads the item number sent as request data.
     */
    public function onArchiveItem(): AjaxResponse
    {
        $itemNumber = (int) input('item');

        return ajax()->flash('success', "Item {$itemNumber} archived.");
    }

    /**
     * onRefreshData stands in for a refresh, as the handler for the icon button and the hotkeys.
     */
    public function onRefreshData(): AjaxResponse
    {
        return ajax()->flash('info', 'The data was refreshed.');
    }

    /**
     * onSaveDraft stands in for a save, as the handler for the hotkey and grouped buttons.
     */
    public function onSaveDraft(): AjaxResponse
    {
        return ajax()->flash('success', 'Draft saved.');
    }
}
