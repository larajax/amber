<?php

namespace App\Controllers;

use App\Classes\ControllerBase;
use App\Controllers\GroupsController\GroupForm;
use App\Controllers\GroupsController\GroupList;
use App\Models\UserGroup;
use Illuminate\Contracts\View\View;

/**
 * GroupsController holds the group pages, with the list and form widgets and their AJAX handlers in the GroupList and GroupForm traits.
 */
class GroupsController extends ControllerBase
{
    use GroupList;
    use GroupForm;

    /**
     * index displays the list of groups, where clicking a row edits the group in a popup.
     */
    public function index(): View
    {
        return view('groups.index', $this->prepareListVars());
    }

    /**
     * create shows the form for a new group.
     */
    public function create(): View
    {
        $this->formWidget = $this->makeFormWidget(new UserGroup, 'create');

        return view('groups.create', [
            'formWidget' => $this->formWidget,
        ]);
    }

    /**
     * edit shows the form for an existing group on its own page.
     */
    public function edit(UserGroup $userGroup): View
    {
        $this->formWidget = $this->makeFormWidget($userGroup, 'update');

        return view('groups.edit', [
            'userGroup' => $userGroup,
            'formWidget' => $this->formWidget,
        ]);
    }
}
