<?php

namespace App\Controllers\PostsController;

use App\Models\Post;
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
    public function onStore()
    {
        $this->formWidget = $this->makeFormWidget(new Post, 'create');

        $post = new Post;

        $this->saveForm($post);

        return ajax()
            ->flash('success', 'Post created.')
            ->redirect("posts/{$post->id}/edit");
    }

    /**
     * onUpdate persists changes to a post, as the AJAX handler for the edit form.
     */
    public function onUpdate(Post $post)
    {
        $this->formWidget = $this->makeFormWidget($post, 'update');

        $this->saveForm($post);

        return ajax()->flash('success', 'Post updated.');
    }

    /**
     * onDestroy deletes a post with its comments, tags and image, as the AJAX handler for the edit form.
     */
    public function onDestroy(Post $post)
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
     * saveForm fills the post from the form data and saves it, where the model rules validate it and the session key commits the uploaded image.
     */
    protected function saveForm(Post $post): void
    {
        $data = $this->formWidget->getSaveData();

        // The relation names fill through the model, so the category associates and the tags sync on save
        $post->fill($data)->save(null, $this->formWidget->getSessionKey());
    }
}
