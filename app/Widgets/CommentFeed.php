<?php

namespace App\Widgets;

use App\Models\Post;
use Larajax\Ui\Classes\WidgetBase;

/**
 * CommentFeed is a custom widget that shows the newest comments on a post, a few at a time, and loads more through its own handler.
 */
class CommentFeed extends WidgetBase
{
    /**
     * @var Post|null post whose comments are shown.
     */
    public $post;

    /**
     * @var int pageSize is the number of comments shown at first, and the number each "Show more" adds.
     */
    public $pageSize = 3;

    /**
     * init reads the post and the page size from the options passed to make().
     */
    public function init(): void
    {
        $this->fillFromConfig(['post', 'pageSize']);
    }

    /**
     * loadAssets adds the widget stylesheet, served from the public folder.
     */
    protected function loadAssets(): void
    {
        $this->addCss('css/widgets/commentfeed.css');
    }

    /**
     * render the widget with its first page of comments.
     */
    public function render(): string
    {
        $this->prepareVars($this->pageSize);

        return $this->makePartial('commentfeed');
    }

    /**
     * prepareVars for the widget partials, with the given number of comments, newest first.
     */
    protected function prepareVars(int $shown): void
    {
        $total = $this->post->comments()->count();

        $this->vars['comments'] = $this->post->comments()->latest('id')->take($shown)->get();
        $this->vars['remaining'] = max(0, $total - $shown);
    }

    /**
     * onLoadMore adds a page to the comments already shown and updates the widget.
     *
     * @return array<string, string>
     */
    public function onLoadMore(): array
    {
        $this->prepareVars(max(0, request()->integer('shown')) + $this->pageSize);

        return ['#'.$this->getId() => $this->makePartial('comments')];
    }
}
