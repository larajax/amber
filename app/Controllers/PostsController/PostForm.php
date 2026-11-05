<?php

namespace App\Controllers\PostsController;

use App\Models\Post;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Larajax\Classes\AjaxResponse;
use Larajax\Ui\Widgets\Form;

/**
 * PostForm builds the post form and handles saving and deleting a post.
 */
trait PostForm
{
    /**
     * @var Form|null formWidget is kept on the instance so the automatic handler routing reaches its AJAX handlers.
     */
    public ?Form $formWidget = null;

    /**
     * onStore persists a new post and opens it for editing, as the AJAX handler for the create form.
     */
    public function onStore(): AjaxResponse
    {
        $post = new Post;

        $this->formWidget = $this->makeFormWidget($post, 'create');

        $this->saveForm($post);

        return ajax()
            ->flash('success', 'Post created.')
            ->redirect(route('posts.edit', $post));
    }

    /**
     * onUpdate persists changes to a post, as the AJAX handler for the edit form.
     */
    public function onUpdate(Post $post): AjaxResponse
    {
        $this->formWidget = $this->makeFormWidget($post, 'update');

        $this->saveForm($post);

        return ajax()->flash('success', 'Post updated.');
    }

    /**
     * onDestroy deletes a post with its comments, tags and image, as the AJAX handler for the edit form.
     */
    public function onDestroy(Post $post): AjaxResponse
    {
        $post->delete();

        return ajax()
            ->flash('success', 'Post deleted.')
            ->redirect(route('posts.index'));
    }

    /**
     * makeFormWidget builds the post form for a given context.
     */
    protected function makeFormWidget(Post $post, string $context): Form
    {
        return Form::make(
            context: $context,
            model: $post,
            fields: '~/resources/ui/post/fields.yaml',
        );
    }

    /**
     * saveForm validates the form data and saves it onto the post, which also attaches an image the upload field holds until the post saves.
     */
    protected function saveForm(Post $post): void
    {
        $data = $this->formWidget->getSaveData();

        // #region docs-rules
        Validator::make($data, [
            'title' => 'required',
            'slug' => ['required', 'regex:/^[a-z0-9-]+$/', Rule::unique('posts')->ignore($post->id)],
            'category' => 'required',
            'status' => 'in:draft,review,approved,archived',
            'views' => 'nullable|integer|min:0',
        ])->validate();
        // #endregion docs-rules

        // #region docs-relation-save
        // The belongs-to fields post under their relation names, so they map onto the foreign keys
        foreach (['category' => 'category_id', 'author' => 'author_id'] as $relationName => $foreignKey) {
            if (array_key_exists($relationName, $data)) {
                $data[$foreignKey] = $data[$relationName] ?: null;
            }
        }

        // The tag ids sync once the post exists, and an empty selection clears them
        $hasTags = array_key_exists('tags', $data);
        $tags = ($data['tags'] ?? null) ?: [];
        unset($data['category'], $data['author'], $data['tags']);

        $post->fill($data)->save();

        if ($hasTags) {
            $post->tags()->sync($tags);
        }
        // #endregion docs-relation-save
    }
}
