<?php

namespace App\Controllers;

use App\Classes\ControllerBase;
use App\Models\UserGroup;
use Larajax\Ui\Widgets\Form;
use Larajax\Ui\Widgets\Lists;
use Larajax\Ui\Widgets\Toolbar;
use ValidationException;

/**
 * User groups controller.
 *
 * A second, deliberately simpler resource — a plain list and form with no
 * filter — showing that the widgets are driven entirely by configuration and
 * are not specific to the User model.
 */
class UserGroupsController extends ControllerBase
{
    public ?Form $formWidget = null;

    /**
     * Display a list of user groups.
     */
    public function index()
    {
        $list = Lists::make(
            model: new UserGroup,
            columns: '~/resources/ui/user_group/columns.yaml',
            recordsPerPage: 10,
            recordUrl: 'user-groups/:id/edit',
        );

        $toolbar = Toolbar::make(
            buttons: '~/resources/views/user_groups/_list_toolbar.php',
            search: ['prompt' => 'Search groups...'],
        );

        $toolbar->bindToListWidget($list);

        return view('user_groups.index', [
            'list' => $list,
            'toolbar' => $toolbar,
        ]);
    }

    /**
     * Show the form for creating a new group.
     */
    public function create()
    {
        $this->formWidget = $this->makeForm(new UserGroup, 'create');

        return view('user_groups.create', [
            'formWidget' => $this->formWidget,
        ]);
    }

    /**
     * Persist a new group. AJAX handler for the create form.
     */
    public function onStore()
    {
        $this->formWidget = $this->makeForm(new UserGroup, 'create');

        $this->saveForm(new UserGroup);

        return ajax()
            ->flash('success', 'Group created.')
            ->redirect('user-groups');
    }

    /**
     * Show the form for editing an existing group.
     */
    public function edit(UserGroup $userGroup)
    {
        $this->formWidget = $this->makeForm($userGroup, 'update');

        return view('user_groups.edit', [
            'userGroup' => $userGroup,
            'formWidget' => $this->formWidget,
        ]);
    }

    /**
     * Persist changes to a group. AJAX handler for the edit form.
     */
    public function onUpdate(UserGroup $userGroup)
    {
        $this->formWidget = $this->makeForm($userGroup, 'update');

        $this->saveForm($userGroup);

        return ajax()->flash('success', 'Group updated.');
    }

    /**
     * Delete a group. AJAX handler for the edit form.
     */
    public function onDestroy(UserGroup $userGroup)
    {
        $userGroup->delete();

        return ajax()
            ->flash('success', 'Group deleted.')
            ->redirect('user-groups');
    }

    /**
     * Build the group form widget for a given context.
     */
    protected function makeForm(UserGroup $group, string $context): Form
    {
        return Form::make(
            context: $context,
            model: $group,
            fields: '~/resources/ui/user_group/fields.yaml',
        );
    }

    /**
     * Validate and persist form data onto the given group.
     */
    protected function saveForm(UserGroup $group): void
    {
        $data = $this->formWidget->getSaveData();

        if (empty($data['name'])) {
            throw new ValidationException(['name' => 'The name field is required.']);
        }

        $group->fill($data)->save();
    }
}
