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
            'comfortable' => 'Switched to the comfortable view.',
            'compact' => 'Switched to the compact view.',
            'refresh' => 'Refreshed the project.',
            'copy' => 'Copied the link.',
            'pdf' => 'Exported the project as PDF.',
            'csv' => 'Exported the project as CSV.',
            'save' => 'Saved the project.',
            'save-close' => 'Saved and closed the project.',
            'draft' => 'Saved the project as a draft.',
        ];

        return ajax()->flash('success', $actions[request()->input('action')] ?? 'Nothing to do.');
    }
}
