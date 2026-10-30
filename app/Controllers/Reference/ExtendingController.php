<?php

namespace App\Controllers\Reference;

use App\Classes\ReferenceControllerBase;
use App\Controllers\Reference\ExtendingController\IconRegistrations;
use App\Controllers\Reference\ExtendingController\SeamResolvers;
use App\Controllers\Reference\ExtendingController\SeamWidgets;
use Illuminate\Contracts\View\View;

/**
 * ExtendingController holds the Extending reference pages, where the icon and resolver setup lives in traits the pages print.
 */
class ExtendingController extends ReferenceControllerBase
{
    use IconRegistrations;
    use SeamResolvers;
    use SeamWidgets;

    /**
     * customWidgets displays the Custom Widgets page.
     */
    public function customWidgets(): View
    {
        $this->makeExampleForms('extending/custom-widgets', ['time-checker']);

        return view('reference.extending.custom-widgets', $this->exampleVars());
    }

    /**
     * icons displays the Icons page.
     */
    public function icons(): View
    {
        $this->registerExampleIcons();

        return view('reference.extending.icons');
    }

    /**
     * seams displays the URLs and Timezones page, with its resolvers set for this request only.
     */
    public function seams(): View
    {
        $this->registerSeamResolvers();

        app()->terminating(fn () => $this->forgetSeamResolvers());

        $this->makeExampleForms('extending/seams', ['page-link', 'published-at']);

        return view('reference.extending.seams', $this->exampleVars() + $this->makeSeamWidgets());
    }
}
