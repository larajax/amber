<?php

namespace App\Controllers;

use App\Classes\ControllerBase;
use App\Controllers\PostsController\PostForm;
use App\Controllers\PostsController\PostList;
use App\Controllers\PostsController\QuickPostForm;
use App\Models\Post;

/**
 * PostsController holds the post pages, with the list, filter, toolbar and form widgets and their AJAX handlers in the PostList, PostForm and QuickPostForm traits.
 */
class PostsController extends ControllerBase
{
    use PostList;
    use PostForm;
    use QuickPostForm;

    /**
     * index displays a filtered, searchable, configurable list of posts with bulk actions and a quick post popup.
     */
    public function index()
    {
        return view('posts.index', $this->prepareListVars());
    }

    /**
     * create shows the form for a new post.
     */
    public function create()
    {
        $this->formWidget = $this->makeFormWidget(new Post, 'create');

        return view('posts.create', [
            'formWidget' => $this->formWidget,
        ]);
    }

    /**
     * edit shows the form for an existing post.
     */
    public function edit(Post $post)
    {
        $this->formWidget = $this->makeFormWidget($post, 'update');

        return view('posts.edit', [
            'post' => $post,
            'formWidget' => $this->formWidget,
        ]);
    }
}
