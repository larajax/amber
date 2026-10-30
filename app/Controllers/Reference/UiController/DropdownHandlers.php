<?php

namespace App\Controllers\Reference\UiController;

use Larajax\Classes\AjaxResponse;

/**
 * DropdownHandlers holds the AJAX handlers behind the Dropdowns page.
 */
trait DropdownHandlers
{
    /**
     * onDropdownAction runs the dropdown item named in the request data.
     */
    public function onDropdownAction(): AjaxResponse
    {
        $actions = [
            'duplicate' => 'Duplicated the project.',
            'archive' => 'Archived the project.',
            'rename' => 'Renamed the project.',
            'delete' => 'Deleted the project.',
        ];

        return ajax()->flash('success', $actions[input('action')] ?? 'Nothing to do.');
    }
}
