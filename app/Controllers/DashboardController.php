<?php

namespace App\Controllers;

use App\Classes\ControllerBase;
use App\Controllers\DashboardController\DashboardFigures;
use App\Controllers\DashboardController\PopularPostList;
use Illuminate\Contracts\View\View;

/**
 * DashboardController holds the blog dashboard, with the figures behind its scoreboard and charts in the DashboardFigures trait and its list of the most read posts in PopularPostList.
 */
class DashboardController extends ControllerBase
{
    use DashboardFigures;
    use PopularPostList;

    /**
     * index displays a scoreboard of the blog's figures, charts of its views and sections, the posts that need attention and the most read posts.
     */
    public function index(): View
    {
        return view('dashboard.index', [
            ...$this->prepareFigureVars(),
            'popularPosts' => $this->makePopularPostList(),
        ]);
    }
}
