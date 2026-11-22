<?php

namespace App\Controllers\Reference\ListsController;

use App\Models\Post;
use App\Models\Sample;
use Illuminate\Support\Str;
use Larajax\Classes\AjaxResponse;
use Larajax\Ui\Classes\ToolbarButton;
use Larajax\Ui\Widgets\Lists;

/**
 * ColumnsExamples builds the small lists on the Columns page, one for each column example, and handles the row actions example.
 */
trait ColumnsExamples
{
    /**
     * makeColumnLists builds a list of posts or samples for each example, with the columns from the example's YAML file.
     *
     * @return array<string, Lists>
     */
    protected function makeColumnLists(): array
    {
        $examples = [
            'text' => Post::class,
            'number' => Post::class,
            'switch' => Post::class,
            'colorpicker' => Post::class,
            'selectable' => Sample::class,
            'dates' => Post::class,
            'relative' => Post::class,
            'summary' => Post::class,
            'image' => Post::class,
            'files' => Sample::class,
            'linkage' => Post::class,
            'partial' => Post::class,
            'actions' => Post::class,
            'relations' => Post::class,
            'headers' => Post::class,
        ];

        $lists = [];

        foreach ($examples as $name => $modelClass) {
            $lists[$name] = Lists::make(
                alias: Str::camel($name).'List',
                model: new $modelClass,
                columns: "~/resources/ui/reference/lists/columns/{$name}.yaml",
                recordsPerPage: 4,
                customPageName: false,
            );
        }

        return $lists;
    }

    // #region docs-row-actions
    /**
     * isRowPostDraft shows a row action only on posts that are not published.
     */
    public function isRowPostDraft(ToolbarButton $button, Post $post): bool
    {
        return !$post->published;
    }

    /**
     * isRowPostPublished shows a row action only on published posts.
     */
    public function isRowPostPublished(ToolbarButton $button, Post $post): bool
    {
        return $post->published;
    }

    /**
     * onPostRowAction names the action and the post of the row it came from, leaving the post as it is.
     */
    public function onPostRowAction(): AjaxResponse
    {
        $post = Post::find(request()->input('id'));

        return ajax()->flash('info', Str::headline((string) request()->input('action')).' would apply to "'.($post?->title ?? 'no post').'". The reference pages leave the posts in place.');
    }
    // #endregion docs-row-actions
}
