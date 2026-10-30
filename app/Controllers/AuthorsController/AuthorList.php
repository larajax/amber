<?php

namespace App\Controllers\AuthorsController;

use App\Models\User;
use Illuminate\Support\Str;
use Larajax\Classes\AjaxResponse;
use Larajax\Ui\Widgets\Filter;
use Larajax\Ui\Widgets\Lists;
use Larajax\Ui\Widgets\Toolbar;

/**
 * AuthorList builds the authors list with its filter and toolbar, and handles deleting the checked authors.
 */
trait AuthorList
{
    // #region docs-list-page
    /**
     * prepareListVars builds the list, filter and toolbar, bound together, for the index view.
     *
     * @return array{list: Lists, filter: Filter, toolbar: Toolbar}
     */
    protected function prepareListVars(): array
    {
        $list = $this->makeListWidget();

        $filter = $this->makeFilterWidget();
        $filter->bindToListWidget($list);

        $toolbar = $this->makeToolbarWidget();
        $toolbar->bindToListWidget($list);

        return [
            'list' => $list,
            'filter' => $filter,
            'toolbar' => $toolbar,
        ];
    }

    /**
     * makeListWidget builds the paginated list of authors in name order, where clicking a row opens the author's form.
     */
    protected function makeListWidget(): Lists
    {
        return Lists::make(
            model: new User,
            columns: '~/resources/ui/user/columns.yaml',
            recordsPerPage: 10,
            showCheckboxes: true,
            recordUrl: 'authors/:id/edit',
            defaultSort: ['column' => 'name', 'direction' => 'asc'],
            noRecordsMessage: 'No authors match the search and filters.',
        );
    }
    // #endregion docs-list-page

    /**
     * makeFilterWidget builds the filter scopes for the authors list.
     */
    protected function makeFilterWidget(): Filter
    {
        return Filter::make(
            model: new User,
            scopes: '~/resources/ui/user/scopes.yaml',
        );
    }

    /**
     * makeToolbarWidget builds the list toolbar with its buttons and search.
     */
    protected function makeToolbarWidget(): Toolbar
    {
        return Toolbar::make(
            buttons: '~/resources/ui/user/buttons.yaml',
            search: ['prompt' => 'Search authors...'],
        );
    }

    /**
     * onDelete deletes the checked authors, whose posts stay without an author, as the AJAX handler for the toolbar delete button.
     */
    public function onDelete(): AjaxResponse
    {
        $authors = User::whereIn('id', (array) input('checked'))->get();

        $authors->each->delete();

        return ajax()
            ->flash('success', $authors->count().' '.Str::plural('author', $authors->count()).' deleted.')
            ->redirect(route('authors.index'));
    }
}
