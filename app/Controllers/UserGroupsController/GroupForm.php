<?php

namespace App\Controllers\UserGroupsController;

use App\Models\UserGroup;
use Larajax\Ui\Widgets\Form;
use ValidationException;

/**
 * GroupForm builds the group form and handles the popup, saving and deleting a group.
 */
trait GroupForm
{
    /**
     * @var Form|null formWidget is kept on the instance so the automatic handler routing reaches its AJAX handlers.
     */
    public ?Form $formWidget = null;

    /**
     * onLoadPopupForm renders a group's form in a popup, as the AJAX handler for the list's popup record click.
     */
    public function onLoadPopupForm()
    {
        $group = UserGroup::findOrFail(input('form_record_id'));

        $this->formWidget = $this->makeFormWidget($group, 'update');

        return view('user_groups._popup_form', [
            'group' => $group,
            'formWidget' => $this->formWidget,
        ])->render();
    }

    /**
     * onSavePopupForm saves a group from the popup form and refreshes the list.
     */
    public function onSavePopupForm()
    {
        $group = UserGroup::findOrFail(input('form_record_id'));

        $this->formWidget = $this->makeFormWidget($group, 'update');

        $this->saveForm($group);

        return ajax()
            ->flash('success', 'Group updated.')
            ->update($this->listWidget->onRefresh());
    }

    /**
     * onStore persists a new group, as the AJAX handler for the create form.
     */
    public function onStore()
    {
        $this->formWidget = $this->makeFormWidget(new UserGroup, 'create');

        $this->saveForm(new UserGroup);

        return ajax()
            ->flash('success', 'Group created.')
            ->redirect(route('user-groups.index'));
    }

    /**
     * onUpdate persists changes to a group, as the AJAX handler for the edit form.
     */
    public function onUpdate(UserGroup $userGroup)
    {
        $this->formWidget = $this->makeFormWidget($userGroup, 'update');

        $this->saveForm($userGroup);

        return ajax()->flash('success', 'Group updated.');
    }

    /**
     * onDestroy deletes a group, as the AJAX handler for the edit form.
     */
    public function onDestroy(UserGroup $userGroup)
    {
        $userGroup->delete();

        return ajax()
            ->flash('success', 'Group deleted.')
            ->redirect(route('user-groups.index'));
    }

    /**
     * makeFormWidget builds the group form for a given context.
     */
    protected function makeFormWidget(UserGroup $group, string $context): Form
    {
        return Form::make(
            context: $context,
            model: $group,
            fields: '~/resources/ui/user_group/fields.yaml',
        );
    }

    /**
     * saveForm validates the form data and persists it onto the given group.
     */
    protected function saveForm(UserGroup $group): void
    {
        $data = $this->formWidget->getSaveData();

        if (empty($data['name'])) {
            throw new ValidationException(['name' => 'The name field is required.']);
        }

        // The members field submits related user ids, synced once the group exists
        $hasMembers = array_key_exists('users', $data);
        $members = ($data['users'] ?? null) ?: [];
        unset($data['users']);

        // The session key commits the file changes the upload fields deferred, such as a removed document
        $group->fill($data)->save(null, $this->formWidget->getSessionKey());

        if ($hasMembers) {
            $group->users()->sync($members);
        }
    }
}
