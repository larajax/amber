<?php

namespace App\Controllers\Reference\ListsController;

use App\Models\Category;
use App\Models\User;
use Larajax\Ui\Widgets\ListStructure;
use Larajax\Ui\Widgets\Toolbar;

/**
 * ListStructureExamples builds the trees and the reorderable list on the List Structure page.
 */
trait ListStructureExamples
{
    /**
     * makeStructureWidgets builds every structure on the page, with the toolbar that searches the tree.
     *
     * @return array<string, ListStructure|Toolbar>
     */
    protected function makeStructureWidgets(): array
    {
        $treeList = $this->makeTreeList();

        return [
            'treeList' => $treeList,
            'treeToolbar' => $this->makeTreeToolbar($treeList),
            'browseTree' => $this->makeBrowseTree(),
            'reorderList' => $this->makeReorderList(),
        ];
    }

    /**
     * makeTreeList shows the categories as a tree that drags three levels deep at most.
     */
    protected function makeTreeList(): ListStructure
    {
        return ListStructure::make(
            alias: 'treeList',
            model: new Category,
            columns: '~/resources/ui/reference/lists/list-structure/categories.yaml',
            maxDepth: 3,
        );
    }

    /**
     * makeTreeToolbar has the search box that searches the given tree.
     */
    protected function makeTreeToolbar(ListStructure $list): Toolbar
    {
        $toolbar = Toolbar::make(
            alias: 'treeToolbar',
            search: ['prompt' => 'Search categories...'],
        );

        $toolbar->bindToListWidget($list);

        return $toolbar;
    }

    /**
     * makeBrowseTree starts with the branches closed and offers no reordering.
     */
    protected function makeBrowseTree(): ListStructure
    {
        return ListStructure::make(
            alias: 'browseTree',
            model: new Category,
            columns: '~/resources/ui/reference/lists/list-structure/categories.yaml',
            treeExpanded: false,
            showReorder: false,
        );
    }

    /**
     * makeReorderList shows the users as a flat list, dragged by the handle into a new order.
     */
    protected function makeReorderList(): ListStructure
    {
        return ListStructure::make(
            alias: 'reorderList',
            model: new User,
            columns: '~/resources/ui/reference/lists/list-structure/users.yaml',
            showTree: false,
            dragRow: false,
        );
    }
}
