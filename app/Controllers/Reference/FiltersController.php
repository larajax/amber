<?php

namespace App\Controllers\Reference;

use App\Classes\ReferenceControllerBase;
use App\Controllers\Reference\FiltersController\OptionsExamples;
use App\Controllers\Reference\FiltersController\ScopesExamples;
use Illuminate\Contracts\View\View;

/**
 * FiltersController holds the Filters reference pages, with each page's widgets in its own examples trait.
 */
class FiltersController extends ReferenceControllerBase
{
    use OptionsExamples;
    use ScopesExamples;

    /**
     * scopes displays the Scope Types page.
     */
    public function scopes(): View
    {
        return view('reference.filters.scopes', [
            'examples' => $this->makeScopeExamples(),
        ]);
    }

    /**
     * options displays the Filter Options page.
     */
    public function options(): View
    {
        return view('reference.filters.options', $this->makeFilterOptionExamples());
    }
}
