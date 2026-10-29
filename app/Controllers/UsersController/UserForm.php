<?php

namespace App\Controllers\UsersController;

use App\Models\User;
use Larajax\Ui\Widgets\Form;
use ValidationException;

/**
 * UserForm builds the user form and handles saving and deleting a user.
 */
trait UserForm
{
    /**
     * @var Form|null formWidget is kept on the instance so the automatic handler routing reaches its AJAX handlers.
     */
    public ?Form $formWidget = null;

    /**
     * onStore persists a new user, as the AJAX handler for the create form.
     */
    public function onStore()
    {
        $this->formWidget = $this->makeFormWidget(new User, 'create');

        $this->saveForm(new User);

        return ajax()
            ->flash('success', 'User created.')
            ->redirect(route('users.index'));
    }

    /**
     * onUpdate persists changes to a user, as the AJAX handler for the edit form.
     */
    public function onUpdate(User $user)
    {
        $this->formWidget = $this->makeFormWidget($user, 'update');

        $this->saveForm($user);

        return ajax()->flash('success', 'User updated.');
    }

    /**
     * onDestroy deletes a user, as the AJAX handler for the edit form.
     */
    public function onDestroy(User $user)
    {
        $user->delete();

        return ajax()
            ->flash('success', 'User deleted.')
            ->redirect(route('users.index'));
    }

    /**
     * makeFormWidget builds the user form for a given context.
     */
    protected function makeFormWidget(User $user, string $context): Form
    {
        return Form::make(
            context: $context,
            model: $user,
            fields: '~/resources/ui/user/fields.yaml',
        );
    }

    /**
     * saveForm validates the form data and persists it onto the given user.
     */
    protected function saveForm(User $user): void
    {
        $data = $this->formWidget->getSaveData();

        if (empty($data['name'])) {
            throw new ValidationException(['name' => 'The name field is required.']);
        }

        // A blank password keeps the existing one, and casts hash a new one on the model
        if (empty($data['password'])) {
            unset($data['password'], $data['password_confirmation']);
        }
        elseif (($data['password'] ?? null) !== ($data['password_confirmation'] ?? null)) {
            throw new ValidationException(['password_confirmation' => 'The passwords do not match.']);
        }

        // The belongs-to field submits under its relation name, so map it onto the foreign key
        if (array_key_exists('primary_group', $data)) {
            $data['primary_group_id'] = $data['primary_group'] ?: null;
        }

        // The many-to-many ids sync once the record exists, and an empty selection clears them
        $hasSecondaryGroups = array_key_exists('groups', $data);
        $secondaryGroups = ($data['groups'] ?? null) ?: [];
        unset($data['groups'], $data['primary_group'], $data['password_confirmation'], $data['send_invite']);

        $user->fill($data)->save();

        if ($hasSecondaryGroups) {
            $user->groups()->sync($secondaryGroups);
        }
    }
}
