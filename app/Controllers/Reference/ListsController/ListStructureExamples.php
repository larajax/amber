<?php

namespace App\Controllers\Reference\ListsController;

use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Str;
use Larajax\Classes\AjaxResponse;
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
        $checkboxTree = $this->makeCheckboxTree();

        return [
            'treeList' => $treeList,
            'treeToolbar' => $this->makeTreeToolbar($treeList),
            'checkboxTree' => $checkboxTree,
            'checkboxTreeToolbar' => $this->makeCheckboxTreeToolbar($checkboxTree),
            'browseTree' => $this->makeBrowseTree(),
            'reorderList' => $this->makeReorderList(),
        ];
    }

    // #region docs-tree
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
    // #endregion docs-tree

    // #region docs-checkboxes
    /**
     * makeCheckboxTree adds a checkbox to each row of the tree, which still reorders by dragging.
     */
    protected function makeCheckboxTree(): ListStructure
    {
        return ListStructure::make(
            alias: 'checkboxTree',
            model: new Category,
            columns: '~/resources/ui/reference/lists/list-structure/categories.yaml',
            showCheckboxes: true,
            maxDepth: 3,
        );
    }

    /**
     * makeCheckboxTreeToolbar has a button that sends the checked rows of the given tree.
     */
    protected function makeCheckboxTreeToolbar(ListStructure $list): Toolbar
    {
        $toolbar = Toolbar::make(
            alias: 'checkboxTreeToolbar',
            buttons: [
                'show' => [
                    'label' => 'Show checked',
                    'icon' => 'ph ph-list-checks',
                    'handler' => 'onShowCheckedCategories',
                    'checked' => true,
                    'attributes' => ['data-request-flash' => true],
                ],
            ],
        );

        $toolbar->bindToListWidget($list);

        return $toolbar;
    }

    /**
     * onShowCheckedCategories names the categories checked in the tree, as sent by the toolbar button.
     */
    public function onShowCheckedCategories(): AjaxResponse
    {
        $names = Category::whereIn('id', (array) request()->input('checked'))->orderBy('name')->pluck('name');

        return ajax()->flash('info', $names->count().' '.Str::plural('category', $names->count()).' checked: '.$names->implode(', ').'.');
    }
    // #endregion docs-checkboxes

    // #region docs-browse
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
    // #endregion docs-browse

    // #region docs-reorder
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
    // #endregion docs-reorder
}
