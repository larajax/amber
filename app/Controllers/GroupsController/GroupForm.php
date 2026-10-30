<?php

namespace App\Controllers\GroupsController;

use App\Models\UserGroup;
use Larajax\Classes\AjaxResponse;
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
    public function onLoadPopupForm(): string
    {
        $group = UserGroup::findOrFail(input('form_record_id'));

        $this->formWidget = $this->makeFormWidget($group, 'update');

        return view('groups._popup_form', [
            'group' => $group,
            'formWidget' => $this->formWidget,
        ])->render();
    }

    /**
     * onSavePopupForm saves a group from the popup form and refreshes the list.
     */
    public function onSavePopupForm(): AjaxResponse
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
    public function onStore(): AjaxResponse
    {
        $this->formWidget = $this->makeFormWidget(new UserGroup, 'create');

        $this->saveForm(new UserGroup);

        return ajax()
            ->flash('success', 'Group created.')
            ->redirect(route('groups.index'));
    }

    /**
     * onUpdate persists changes to a group, as the AJAX handler for the edit form.
     */
    public function onUpdate(UserGroup $userGroup): AjaxResponse
    {
        $this->formWidget = $this->makeFormWidget($userGroup, 'update');

        $this->saveForm($userGroup);

        return ajax()->flash('success', 'Group updated.');
    }

    /**
     * onDestroy deletes a group, as the AJAX handler for the edit form.
     */
    public function onDestroy(UserGroup $userGroup): AjaxResponse
    {
        $userGroup->delete();

        return ajax()
            ->flash('success', 'Group deleted.')
            ->redirect(route('groups.index'));
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

        // The members field posts related user ids, synced once the group exists
        $hasMembers = array_key_exists('users', $data);
        $members = ($data['users'] ?? null) ?: [];
        unset($data['users']);

        $group->fill($data)->save();

        if ($hasMembers) {
            $group->users()->sync($members);
        }
    }
}
