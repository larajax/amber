<?php

namespace App\Controllers;

use App\Classes\ControllerBase;
use App\Controllers\CategoriesController\CategoryForm;
use App\Controllers\CategoriesController\CategoryList;
use App\Models\Category;
use Illuminate\Contracts\View\View;

/**
 * CategoriesController holds the category pages, with the tree list and form widgets and their AJAX handlers in the CategoryList and CategoryForm traits.
 */
class CategoriesController extends ControllerBase
{
    use CategoryList;
    use CategoryForm;

    /**
     * index displays the categories as a tree that can be expanded, nested and reordered by dragging.
     */
    public function index(): View
    {
        return view('categories.index', $this->prepareListVars());
    }

    /**
     * create shows the form for a new category.
     */
    public function create(): View
    {
        $this->formWidget = $this->makeFormWidget(new Category, 'create');

        return view('categories.create', [
            'formWidget' => $this->formWidget,
        ]);
    }

    /**
     * edit shows the form for an existing category.
     */
    public function edit(Category $category): View
    {
        $this->formWidget = $this->makeFormWidget($category, 'update');

        return view('categories.edit', [
            'category' => $category,
            'formWidget' => $this->formWidget,
        ]);
    }
}
