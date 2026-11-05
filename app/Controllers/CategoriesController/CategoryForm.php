<?php

namespace App\Controllers\CategoriesController;

use App\Models\Category;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Larajax\Classes\AjaxResponse;
use Larajax\Ui\Widgets\Form;

/**
 * CategoryForm builds the category form and handles saving and deleting a category.
 */
trait CategoryForm
{
    /**
     * @var Form|null formWidget is kept on the instance so the automatic handler routing reaches its AJAX handlers.
     */
    public ?Form $formWidget = null;

    /**
     * onStore persists a new category, as the AJAX handler for the create form.
     */
    public function onStore(): AjaxResponse
    {
        $category = new Category;

        $this->formWidget = $this->makeFormWidget($category, 'create');

        $this->saveForm($category);

        return ajax()
            ->flash('success', 'Category created.')
            ->redirect(route('categories.index'));
    }

    /**
     * onUpdate persists changes to a category, as the AJAX handler for the edit form.
     */
    public function onUpdate(Category $category): AjaxResponse
    {
        $this->formWidget = $this->makeFormWidget($category, 'update');

        $this->saveForm($category);

        return ajax()->flash('success', 'Category updated.');
    }

    /**
     * onDestroy deletes a category with the categories below it, refusing while any of them holds a post.
     */
    public function onDestroy(Category $category): AjaxResponse
    {
        if ($category->hasPostsInTree()) {
            throw ValidationException::withMessages(['name' => 'Move or delete the posts in this category and the categories below it first.']);
        }

        $category->delete();

        return ajax()
            ->flash('success', 'Category deleted.')
            ->redirect(route('categories.index'));
    }

    /**
     * makeFormWidget builds the category form for a given context.
     */
    protected function makeFormWidget(Category $category, string $context): Form
    {
        return Form::make(
            context: $context,
            model: $category,
            fields: '~/resources/ui/category/fields.yaml',
        );
    }

    /**
     * saveForm validates the form data and saves it onto the category, where the parent field posts under its relation name and maps onto the parent_id column.
     */
    protected function saveForm(Category $category): void
    {
        $data = $this->formWidget->getSaveData();

        Validator::make($data, [
            'name' => 'required',
            'slug' => ['required', 'regex:/^[a-z0-9-]+$/', Rule::unique('categories')->ignore($category->id)],
        ])->validate();

        // Nesting a category below one of its own descendants would break the tree
        $parentId = ($data['parent'] ?? null) ?: null;
        if ($parentId && $category->exists && $category->getAllChildren()->contains('id', (int) $parentId)) {
            throw ValidationException::withMessages(['parent' => 'A category cannot be nested below one of its own subcategories.']);
        }

        if (array_key_exists('parent', $data)) {
            $data['parent_id'] = $parentId;
        }

        unset($data['parent']);

        $category->fill($data)->save();
    }
}
