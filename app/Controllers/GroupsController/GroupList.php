<?php

namespace App\Controllers\GroupsController;

use App\Models\UserGroup;
use Larajax\Ui\Widgets\Lists;
use Larajax\Ui\Widgets\Toolbar;

/**
 * GroupList builds the groups list and its toolbar.
 */
trait GroupList
{
    /**
     * @var Lists|null listWidget is kept on the instance so the popup save handler can refresh it.
     */
    public ?Lists $listWidget = null;

    /**
     * prepareListVars builds the list and its toolbar, bound together, for the index view.
     *
     * @return array{list: Lists, toolbar: Toolbar}
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
     * makeListWidget builds the list of groups with their member counts, where clicking a row edits the group in a popup.
     */
    protected function makeListWidget(): Lists
    {
        return Lists::make(
            model: new UserGroup,
            columns: '~/resources/ui/user_group/columns.yaml',
            recordsPerPage: 10,
            recordOnClick: 'popup',
            defaultSort: ['column' => 'name', 'direction' => 'asc'],
        );
    }

    /**
     * makeToolbarWidget builds the list toolbar with its buttons and search.
     */
    protected function makeToolbarWidget(): Toolbar
    {
        return Toolbar::make(
            buttons: '~/resources/views/groups/_list_toolbar.php',
            search: ['prompt' => 'Search groups...'],
        );
    }
}
