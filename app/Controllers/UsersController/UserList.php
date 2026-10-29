<?php

namespace App\Controllers\UsersController;

use App\Models\User;
use Larajax\Ui\Widgets\Filter;
use Larajax\Ui\Widgets\Lists;
use Larajax\Ui\Widgets\ListStructure;
use Larajax\Ui\Widgets\Toolbar;

/**
 * UserList builds the users list, its filter and toolbar, and the reorderable structure.
 */
trait UserList
{
    /**
     * prepareListVars builds the list, filter and toolbar, bound together, for the index view.
     */
    protected function prepareListVars(): array
    {
        $list = $this->makeListWidget();

        $filter = $this->makeFilterWidget();
        $filter->bindToListWidget($list);

        $toolbar = $this->makeToolbarWidget();

        // The top bar search submits here as ?q=
        if (request()->filled('q')) {
            $toolbar->getSearchWidget()->setActiveTerm(request('q'));
        }

        $toolbar->bindToListWidget($list);

        return [
            'list' => $list,
            'filter' => $filter,
            'toolbar' => $toolbar,
        ];
    }

    /**
     * makeListWidget builds the paginated list of users.
     */
    protected function makeListWidget(): Lists
    {
        return Lists::make(
            model: new User,
            columns: '~/resources/ui/user/columns.yaml',
            recordsPerPage: 10,
            showCheckboxes: true,
            recordUrl: 'users/:id/edit'
        );
    }

    /**
     * makeFilterWidget builds the filter scopes for the users list.
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
            search: ['prompt' => 'Search users...'],
        );
    }

    /**
     * makeStructureWidget builds the users as a flat reorderable structure, since the model has no tree.
     */
    protected function makeStructureWidget(): ListStructure
    {
        return ListStructure::make(
            model: new User,
            columns: '~/resources/ui/user/columns.yaml',
            showTree: false,
        );
    }

    /**
     * onDelete deletes the checked users, as the AJAX handler for the toolbar delete button.
     */
    public function onDelete()
    {
        $checkedIds = (array) input('checked');

        User::whereIn('id', $checkedIds)->get()->each->delete();

        return ajax()
            ->flash('success', 'Users deleted.')
            ->redirect('users');
    }
}
