<?php

namespace App\Controllers\CategoriesController;

use App\Models\Category;
use Larajax\Ui\Widgets\Form;
use ValidationException;

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
    public function onStore()
    {
        $this->formWidget = $this->makeFormWidget(new Category, 'create');

        $this->saveForm(new Category);

        return ajax()
            ->flash('success', 'Category created.')
            ->redirect('categories');
    }

    /**
     * onUpdate persists changes to a category, as the AJAX handler for the edit form.
     */
    public function onUpdate(Category $category)
    {
        $this->formWidget = $this->makeFormWidget($category, 'update');

        $this->saveForm($category);

        return ajax()->flash('success', 'Category updated.');
    }

    /**
     * onDestroy deletes a category with the categories below it, refusing while any of them holds a post.
     */
    public function onDestroy(Category $category)
    {
        if ($category->hasPostsInTree()) {
            throw new ValidationException(['name' => 'Move or delete the posts in this category and the categories below it first.']);
        }

        $category->delete();

        return ajax()
            ->flash('success', 'Category deleted.')
            ->redirect('categories');
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
     * saveForm fills the category from the form data and saves it, where the model rules validate it and a parent change moves it in the tree.
     */
    protected function saveForm(Category $category): void
    {
        $data = $this->formWidget->getSaveData();

        // Nesting a category below one of its own descendants would break the tree
        $parentId = $data['parent'] ?? null;
        if ($parentId && $category->exists && $category->getAllChildren()->contains('id', (int) $parentId)) {
            throw new ValidationException(['parent' => 'A category cannot be nested below one of its own subcategories.']);
        }

        $category->fill($data)->save();
    }
}
