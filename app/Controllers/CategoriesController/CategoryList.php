<?php

namespace App\Controllers\CategoriesController;

use App\Models\Category;
use Illuminate\Support\Str;
use Larajax\Classes\AjaxResponse;
use Larajax\Ui\Widgets\ListStructure;
use Larajax\Ui\Widgets\Toolbar;

/**
 * CategoryList builds the category tree and its toolbar, and handles deleting the checked categories.
 */
trait CategoryList
{
    /**
     * @var ListStructure|null listWidget is kept on the instance so the delete handler can refresh it.
     */
    public ?ListStructure $listWidget = null;

    /**
     * prepareListVars builds the tree and its toolbar, bound together, for the index view.
     *
     * @return array{list: ListStructure, toolbar: Toolbar}
     */
    protected function prepareListVars(): array
    {
        $list = $this->listWidget = $this->makeListWidget();

        $toolbar = $this->makeToolbarWidget();
        $toolbar->bindToListWidget($list);

        return [
            'list' => $list,
            'toolbar' => $toolbar,
        ];
    }

    /**
     * makeListWidget builds the category tree with a checkbox on each row, where dragging a row by its handle nests or reorders it three levels deep at most.
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
            showCheckboxes: true,
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

    /**
     * onDelete deletes the checked categories with the categories below them, keeping any whose branch holds a post, as the AJAX handler for the toolbar delete button.
     */
    public function onDelete(): AjaxResponse
    {
        $categories = Category::whereIn('id', (array) request()->input('checked'))->orderBy('name')->get();

        [$kept, $deletable] = $categories->partition(fn (Category $category) => $category->hasPostsInTree());

        // A checked category may already be gone with a checked parent, so each one is looked up again
        $deletable->each(fn (Category $category) => Category::find($category->id)?->delete());

        $response = ajax()->update($this->listWidget->onRefresh());

        if ($kept->isEmpty()) {
            return $response->flash('success', $deletable->count().' '.Str::plural('category', $deletable->count()).' deleted.');
        }

        $keptMessage = $kept->pluck('name')->join(', ', ' and ').' '.($kept->count() === 1 ? 'holds' : 'hold').' posts, so '.($kept->count() === 1 ? 'it stays' : 'they stay').'.';

        if ($deletable->isEmpty()) {
            return $response->flash('error', $keptMessage.' Move or delete the posts first.');
        }

        return $response->flash('warning', $deletable->count().' '.Str::plural('category', $deletable->count()).' deleted. '.$keptMessage);
    }
}
