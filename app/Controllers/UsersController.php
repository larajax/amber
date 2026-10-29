<?php

namespace App\Controllers;

use App\Classes\ControllerBase;
use App\Controllers\UsersController\UserForm;
use App\Controllers\UsersController\UserList;
use App\Models\User;

/**
 * UsersController holds the user pages, with the list and form widgets and their AJAX handlers in the UserList and UserForm traits.
 */
class UsersController extends ControllerBase
{
    use UserList;
    use UserForm;

    /**
     * index displays a filtered, searchable, paginated list of users.
     */
    public function index()
    {
        return view('users.index', $this->prepareListVars());
    }

    /**
     * structure displays the users as a drag-to-reorder structure.
     */
    public function structure()
    {
        return view('users.structure', [
            'widget' => $this->makeStructureWidget(),
        ]);
    }

    /**
     * create shows the form for a new user.
     */
    public function create()
    {
        $this->formWidget = $this->makeFormWidget(new User, 'create');

        return view('users.create', [
            'formWidget' => $this->formWidget,
        ]);
    }

    /**
     * edit shows the form for an existing user.
     */
    public function edit(User $user)
    {
        $this->formWidget = $this->makeFormWidget($user, 'update');

        return view('users.edit', [
            'user' => $user,
            'formWidget' => $this->formWidget,
        ]);
    }
}
