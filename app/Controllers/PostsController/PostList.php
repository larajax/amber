<?php

namespace App\Controllers\PostsController;

use App\Models\Post;
use Illuminate\Support\Str;
use Larajax\Classes\AjaxResponse;
use Larajax\Ui\Widgets\Filter;
use Larajax\Ui\Widgets\Lists;
use Larajax\Ui\Widgets\Toolbar;

/**
 * PostList builds the posts list, its filter and toolbar, and handles the bulk actions on the checked rows.
 */
trait PostList
{
    /**
     * @var Lists|null listWidget is kept on the instance so the bulk action and popup handlers can refresh it.
     */
    public ?Lists $listWidget = null;

    /**
     * prepareListVars builds the list, filter and toolbar, bound together, for the index view.
     *
     * @return array{list: Lists, filter: Filter, toolbar: Toolbar}
     */
    protected function prepareListVars(): array
    {
        $list = $this->listWidget = $this->makeListWidget();

        $filter = $this->makeFilterWidget();
        $filter->bindToListWidget($list);

        $toolbar = $this->makeToolbarWidget($list);

        // The top bar search submits here as ?q=
        if (request()->filled('q')) {
            $toolbar->getSearchWidget()->setActiveTerm(request('q'));
        }

        $toolbar->bindToListWidget($list);

        return [
            'list' => $list,
            'filter' => $filter,
            'toolbar' => $toolbar,
        ];
    }

    /**
     * makeListWidget builds the paginated list of posts, newest first, with a setup popup for columns and page size.
     */
    protected function makeListWidget(): Lists
    {
        return Lists::make(
            model: new Post,
            columns: '~/resources/ui/post/columns.yaml',
            recordsPerPage: 10,
            perPageOptions: [10, 20, 50],
            showCheckboxes: true,
            showSetup: true,
            recordUrl: 'posts/:id/edit',
            defaultSort: ['column' => 'published_at', 'direction' => 'desc'],
            noRecordsMessage: 'No posts match the search and filters.',
        );
    }

    /**
     * makeFilterWidget builds the filter scopes for the posts list.
     */
    protected function makeFilterWidget(): Filter
    {
        return Filter::make(
            model: new Post,
            scopes: '~/resources/ui/post/scopes.yaml',
        );
    }

    /**
     * makeToolbarWidget builds the toolbar from a partial, with a search that runs through a model scope and the list setup icon.
     */
    protected function makeToolbarWidget(Lists $list): Toolbar
    {
        return Toolbar::make(
            buttons: '~/resources/views/posts/_list_toolbar.php',
            search: [
                'prompt' => 'Search posts...',
                'scope' => 'applySearch',
            ],
            setupHandler: $list->getEventHandler('onLoadSetup'),
        );
    }

    /**
     * onBulkPublish publishes the checked posts, stamping a publish date on those without one.
     */
    public function onBulkPublish(): AjaxResponse
    {
        $posts = Post::whereIn('id', $this->getCheckedPostIds());

        $count = (clone $posts)->update(['published' => true]);
        (clone $posts)->whereNull('published_at')->update(['published_at' => now()]);

        return ajax()
            ->flash('success', $count.' '.Str::plural('post', $count).' published.')
            ->update($this->listWidget->onRefresh());
    }

    /**
     * onBulkUnpublish takes the checked posts offline and keeps their publish dates.
     */
    public function onBulkUnpublish(): AjaxResponse
    {
        $count = Post::whereIn('id', $this->getCheckedPostIds())->update(['published' => false]);

        return ajax()
            ->flash('success', $count.' '.Str::plural('post', $count).' unpublished.')
            ->update($this->listWidget->onRefresh());
    }

    /**
     * onBulkDelete deletes the checked posts one by one, so their comments, tags and images go with them.
     */
    public function onBulkDelete(): AjaxResponse
    {
        $posts = Post::whereIn('id', $this->getCheckedPostIds())->get();

        $posts->each->delete();

        return ajax()
            ->flash('success', $posts->count().' '.Str::plural('post', $posts->count()).' deleted.')
            ->update($this->listWidget->onRefresh());
    }

    /**
     * getCheckedPostIds returns the ids of the checked rows posted by a bulk action.
     *
     * @return array<int, int>
     */
    protected function getCheckedPostIds(): array
    {
        return array_map('intval', array_filter((array) input('checked'), 'is_numeric'));
    }
}
