<?php

namespace App\Controllers\Reference;

use App\Classes\ControllerBase;
use Illuminate\Contracts\View\View;

/**
 * IndexController holds the Reference overview, which links every reference page by group.
 */
class IndexController extends ControllerBase
{
    /**
     * index displays every reference group and its pages.
     */
    public function index(): View
    {
        return view('reference.index', [
            'groups' => config('reference.groups'),
        ]);
    }
}
