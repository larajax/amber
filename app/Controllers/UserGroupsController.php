<?php

namespace App\Controllers;

use App\Classes\ControllerBase;
use App\Controllers\UserGroupsController\GroupForm;
use App\Controllers\UserGroupsController\GroupList;
use App\Models\UserGroup;

/**
 * UserGroupsController holds the group pages, with the list and form widgets and their AJAX handlers in the GroupList and GroupForm traits.
 */
class UserGroupsController extends ControllerBase
{
    use GroupList;
    use GroupForm;

    /**
     * index displays the list of groups, where clicking a row edits the group in a popup.
     */
    public function index()
    {
        return view('user_groups.index', $this->prepareListVars());
    }

    /**
     * create shows the form for a new group.
     */
    public function create()
    {
        $this->formWidget = $this->makeFormWidget(new UserGroup, 'create');

        return view('user_groups.create', [
            'formWidget' => $this->formWidget,
        ]);
    }

    /**
     * edit shows the form for an existing group.
     */
    public function edit(UserGroup $userGroup)
    {
        $this->formWidget = $this->makeFormWidget($userGroup, 'update');

        return view('user_groups.edit', [
            'userGroup' => $userGroup,
            'formWidget' => $this->formWidget,
        ]);
    }
}
