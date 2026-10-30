<?php

namespace App\Controllers\AuthorsController;

use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Larajax\Classes\AjaxResponse;
use Larajax\Ui\Widgets\Form;
use ValidationException;

/**
 * AuthorForm builds the author form and handles saving and deleting an author.
 */
trait AuthorForm
{
    /**
     * @var Form|null formWidget is kept on the instance so the automatic handler routing reaches its AJAX handlers.
     */
    public ?Form $formWidget = null;

    /**
     * onStore persists a new author and returns to the list, as the AJAX handler for the create form.
     */
    public function onStore(): AjaxResponse
    {
        $this->formWidget = $this->makeFormWidget(new User, 'create');

        $this->saveForm(new User);

        return ajax()
            ->flash('success', 'Author created.')
            ->redirect(route('authors.index'));
    }

    /**
     * onUpdate persists changes to an author, as the AJAX handler for the edit form.
     */
    public function onUpdate(User $user): AjaxResponse
    {
        $this->formWidget = $this->makeFormWidget($user, 'update');

        $this->saveForm($user);

        return ajax()->flash('success', 'Author updated.');
    }

    /**
     * onDestroy deletes an author, whose posts stay without an author, as the AJAX handler for the edit form.
     */
    public function onDestroy(User $user): AjaxResponse
    {
        $user->delete();

        return ajax()
            ->flash('success', 'Author deleted.')
            ->redirect(route('authors.index'));
    }

    /**
     * makeFormWidget builds the author form for a given context.
     */
    protected function makeFormWidget(User $user, string $context): Form
    {
        return Form::make(
            context: $context,
            model: $user,
            fields: '~/resources/ui/user/fields.yaml',
        );
    }

    // #region docs-save-form
    /**
     * saveForm validates the form data here, since the Eloquent model has no rules of its own, and saves it onto the given author.
     */
    protected function saveForm(User $user): void
    {
        $data = $this->formWidget->getSaveData();

        $validator = Validator::make($data, [
            'name' => 'required',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => [$user->exists ? 'nullable' : 'required', 'confirmed'],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        // A blank password keeps the current one, and the model casts hash a new one
        if (empty($data['password'])) {
            unset($data['password']);
        }

        // #region docs-eloquent-relation-save
        // The belongs-to field posts under its relation name, so it maps onto the foreign key
        if (array_key_exists('primary_group', $data)) {
            $data['primary_group_id'] = $data['primary_group'] ?: null;
        }

        // The many-to-many ids sync once the record exists, and an empty selection clears them
        $hasOtherGroups = array_key_exists('groups', $data);
        $otherGroups = ($data['groups'] ?? null) ?: [];
        unset($data['groups'], $data['primary_group'], $data['password_confirmation']);

        $user->fill($data)->save();

        if ($hasOtherGroups) {
            $user->groups()->sync($otherGroups);
        }
        // #endregion docs-eloquent-relation-save
    }
    // #endregion docs-save-form
}
