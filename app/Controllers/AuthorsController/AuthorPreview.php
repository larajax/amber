<?php

namespace App\Controllers\AuthorsController;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Larajax\Ui\Widgets\Form;
use Larajax\Ui\Widgets\Lists;
use Larajax\Ui\Widgets\Toolbar;

/**
 * AuthorPreview builds the widgets on an author's preview page: their details as a read-only form, and lists of their posts and the comments left on them, each with a toolbar.
 */
trait AuthorPreview
{
    /**
     * preparePreviewVars builds the preview form and both lists with their toolbars, for the preview view.
     *
     * @return array{previewForm: Form, postToolbar: Toolbar, postList: Lists, commentToolbar: Toolbar, commentList: Lists}
     */
    protected function preparePreviewVars(User $user): array
    {
        $postList = $this->makeAuthorPostList($user);

        $postToolbar = Toolbar::make(
            alias: 'postToolbar',
            buttons: '~/resources/ui/post/buttons_author.yaml',
            search: ['prompt' => 'Search their posts...'],
        );
        $postToolbar->bindToListWidget($postList);

        $commentList = $this->makeAuthorCommentList($user);

        $commentToolbar = Toolbar::make(
            alias: 'commentToolbar',
            search: ['prompt' => 'Search comments...'],
        );
        $commentToolbar->bindToListWidget($commentList);

        return [
            'previewForm' => $this->makePreviewFormWidget($user),
            'postToolbar' => $postToolbar,
            'postList' => $postList,
            'commentToolbar' => $commentToolbar,
            'commentList' => $commentList,
        ];
    }

    /**
     * makePreviewFormWidget builds the author's details as a form in preview mode, whose tabs the view prints side by side.
     */
    protected function makePreviewFormWidget(User $user): Form
    {
        return Form::make(
            alias: 'previewForm',
            context: 'preview',
            model: $user,
            fields: '~/resources/ui/user/fields_preview.yaml',
            previewMode: true,
        );
    }

    /**
     * makeAuthorPostList builds the list of the author's posts, newest first, where clicking a row opens the post.
     */
    protected function makeAuthorPostList(User $user): Lists
    {
        $list = Lists::make(
            alias: 'postList',
            model: new Post,
            columns: '~/resources/ui/post/columns_author.yaml',
            recordsPerPage: 10,
            recordUrl: 'posts/:id/edit',
            defaultSort: ['column' => 'created_at', 'direction' => 'desc'],
            noRecordsMessage: 'This author has not written any posts.',
        );

        $list->addFilter(fn ($query) => $query->where('author_id', $user->id));

        return $list;
    }

    /**
     * makeAuthorCommentList builds the list of the comments readers left on the author's posts, newest first.
     */
    protected function makeAuthorCommentList(User $user): Lists
    {
        $list = Lists::make(
            alias: 'commentList',
            model: new Comment,
            columns: '~/resources/ui/comment/columns.yaml',
            recordsPerPage: 10,
            defaultSort: ['column' => 'created_at', 'direction' => 'desc'],
            noRecordsMessage: 'No comments on this author\'s posts.',
        );

        $list->addFilter(fn ($query) => $query->whereIn('post_id', Post::query()->where('author_id', $user->id)->select('id')));

        return $list;
    }
}
