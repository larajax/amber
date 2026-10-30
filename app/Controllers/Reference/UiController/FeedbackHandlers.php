<?php

namespace App\Controllers\Reference\UiController;

use Illuminate\Support\Sleep;
use Larajax\Classes\AjaxResponse;

/**
 * FeedbackHandlers holds the AJAX handlers behind the Feedback page.
 */
trait FeedbackHandlers
{
    /**
     * onCalloutAction handles the buttons inside the callouts.
     */
    public function onCalloutAction(): AjaxResponse
    {
        return ajax()->flash('info', 'Payment details are not part of this demo.');
    }

    /**
     * onSlowRequest sleeps briefly so the loader container and the loading button have time to show.
     */
    public function onSlowRequest(): AjaxResponse
    {
        Sleep::for(1500)->milliseconds();

        return ajax()->flash('success', 'The slow request finished.');
    }

    /**
     * onSaveProfile stands in for a save, as the handler for the change monitor form.
     */
    public function onSaveProfile(): AjaxResponse
    {
        return ajax()->flash('success', 'Profile saved.');
    }
}
