<?php

namespace App\Controllers;

use App\Classes\ControllerBase;
use App\Controllers\AuthorsController\AuthorForm;
use App\Controllers\AuthorsController\AuthorList;
use App\Models\User;
use Illuminate\Contracts\View\View;

/**
 * AuthorsController holds the author pages, with the list and form widgets and their AJAX handlers in the AuthorList and AuthorForm traits.
 */
class AuthorsController extends ControllerBase
{
    use AuthorList;
    use AuthorForm;

    /**
     * index displays a filtered, searchable list of the authors with the number of posts each one wrote.
     */
    public function index(): View
    {
        return view('authors.index', $this->prepareListVars());
    }

    /**
     * create shows the form for a new author.
     */
    public function create(): View
    {
        $this->formWidget = $this->makeFormWidget(new User, 'create');

        return view('authors.create', [
            'formWidget' => $this->formWidget,
        ]);
    }

    /**
     * edit shows the form for an existing author.
     */
    public function edit(User $user): View
    {
        $this->formWidget = $this->makeFormWidget($user, 'update');

        return view('authors.edit', [
            'user' => $user,
            'formWidget' => $this->formWidget,
        ]);
    }
}
