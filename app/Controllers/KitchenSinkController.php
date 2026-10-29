<?php

namespace App\Controllers;

use App\Classes\ControllerBase;
use App\Controllers\KitchenSinkController\SampleForm;
use App\Controllers\KitchenSinkController\SampleList;
use App\Models\Sample;
use Illuminate\Contracts\View\View;

/**
 * KitchenSinkController holds the kitchen sink pages, with the list and form widgets and their AJAX handlers in the SampleList and SampleForm traits.
 */
class KitchenSinkController extends ControllerBase
{
    use SampleList;
    use SampleForm;

    /**
     * index displays the list of samples.
     */
    public function index(): View
    {
        return view('kitchen_sink.index', $this->prepareListVars());
    }

    /**
     * create shows the showcase form for a new sample.
     */
    public function create(): View
    {
        $this->formWidget = $this->makeFormWidget(new Sample, 'create');

        return view('kitchen_sink.create', [
            'formWidget' => $this->formWidget,
        ]);
    }

    /**
     * edit shows the showcase form for an existing sample.
     */
    public function edit(Sample $sample): View
    {
        $this->formWidget = $this->makeFormWidget($sample, 'update');

        return view('kitchen_sink.edit', [
            'sample' => $sample,
            'formWidget' => $this->formWidget,
        ]);
    }
}
