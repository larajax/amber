<?php

namespace App\Controllers\CategoriesController;

use App\Models\Category;
use Larajax\Ui\Widgets\ListStructure;
use Larajax\Ui\Widgets\Toolbar;

/**
 * CategoryList builds the category tree and its toolbar.
 */
trait CategoryList
{
    /**
     * prepareListVars builds the tree and its toolbar, bound together, for the index view.
     *
     * @return array{list: ListStructure, toolbar: Toolbar}
     */
    protected function prepareListVars(): array
    {
        $list = $this->makeListWidget();

        $toolbar = $this->makeToolbarWidget();
        $toolbar->bindToListWidget($list);

        return [
            'list' => $list,
            'toolbar' => $toolbar,
        ];
    }

    /**
     * makeListWidget builds the nested set tree, where dragging a row nests or reorders it three levels deep at most.
     */
    protected function makeListWidget(): ListStructure
    {
        return ListStructure::make(
            model: new Category,
            columns: '~/resources/ui/category/columns.yaml',
            recordUrl: 'categories/:id/edit',
            noRecordsMessage: 'There are no categories yet.',
            showTree: true,
            treeExpanded: true,
            maxDepth: 3,
            dragRow: false,
        );
    }

    /**
     * makeToolbarWidget builds the toolbar with its buttons and search, where searching shows the matches as a flat list.
     */
    protected function makeToolbarWidget(): Toolbar
    {
        return Toolbar::make(
            buttons: '~/resources/ui/category/buttons.yaml',
            search: ['prompt' => 'Search categories...'],
        );
    }
}
