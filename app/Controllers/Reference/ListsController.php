<?php

namespace App\Controllers\Reference;

use App\Classes\ReferenceControllerBase;
use App\Controllers\Reference\ListsController\ColumnsExamples;
use App\Controllers\Reference\ListsController\ListStructureExamples;
use App\Controllers\Reference\ListsController\OptionsExamples;
use App\Controllers\Reference\ListsController\SearchExamples;
use App\Controllers\Reference\ListsController\ToolbarExamples;
use Illuminate\Contracts\View\View;

/**
 * ListsController holds the Lists reference pages, with each page's widgets and handlers in its own examples trait.
 */
class ListsController extends ReferenceControllerBase
{
    use ColumnsExamples;
    use ListStructureExamples;
    use OptionsExamples;
    use SearchExamples;
    use ToolbarExamples;

    /**
     * columns displays the Columns page.
     */
    public function columns(): View
    {
        return view('reference.lists.columns', [
            'lists' => $this->makeColumnLists(),
        ]);
    }

    /**
     * options displays the List Options page.
     */
    public function options(): View
    {
        return view('reference.lists.options', $this->makeOptionWidgets());
    }

    /**
     * listStructure displays the List Structure page.
     */
    public function listStructure(): View
    {
        return view('reference.lists.list-structure', $this->makeStructureWidgets());
    }

    /**
     * toolbar displays the Toolbar page.
     */
    public function toolbar(): View
    {
        return view('reference.lists.toolbar', $this->makeToolbarWidgets());
    }

    /**
     * search displays the Search page.
     */
    public function search(): View
    {
        return view('reference.lists.search', $this->makeSearchWidgets());
    }
}
