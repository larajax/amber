<?php

namespace App\Controllers\PostsController;

use App\Models\Post;
use Larajax\Ui\Widgets\Form;

/**
 * QuickPostForm builds the short form shown in the quick post popup and creates a post from it.
 */
trait QuickPostForm
{
    /**
     * onLoadQuickPost renders the quick post form in a popup, as the AJAX handler for the toolbar popup button.
     */
    public function onLoadQuickPost()
    {
        $this->formWidget = $this->makeQuickPostFormWidget(new Post);

        return view('posts._quick_post_form', [
            'formWidget' => $this->formWidget,
        ])->render();
    }

    /**
     * onCreateQuickPost creates a post from the popup form and refreshes the list.
     */
    public function onCreateQuickPost()
    {
        $post = new Post;

        $this->formWidget = $this->makeQuickPostFormWidget($post);

        $post->fill($this->formWidget->getSaveData())->save();

        return ajax()
            ->flash('success', 'Post created.')
            ->update($this->listWidget->onRefresh());
    }

    /**
     * makeQuickPostFormWidget builds the quick post form, with its own alias so its fields stay apart from the main post form.
     */
    protected function makeQuickPostFormWidget(Post $post): Form
    {
        return Form::make(
            alias: 'quickPostForm',
            context: 'create',
            model: $post,
            fields: '~/resources/ui/post/fields_quick.yaml',
        );
    }
}
