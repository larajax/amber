<?php

namespace App\Controllers\KitchenSinkController;

use App\Models\Sample;
use Larajax\Ui\Widgets\Lists;
use Larajax\Ui\Widgets\Toolbar;

/**
 * SampleList builds the samples list and its toolbar.
 */
trait SampleList
{
    /**
     * prepareListVars builds the list and its toolbar, bound together, for the index view.
     *
     * @return array{list: Lists, toolbar: Toolbar}
     */
    protected function prepareListVars(): array
    {
        $list = $this->makeListWidget();

        $toolbar = $this->makeToolbarWidget();
        $toolbar->bindToListWidget($list);

        return [
            'list' => $list,
            'toolbar' => $toolbar,
        ];
    }

    /**
     * makeListWidget builds the list of samples, where clicking a row opens the showcase form.
     */
    protected function makeListWidget(): Lists
    {
        return Lists::make(
            model: new Sample,
            columns: '~/resources/ui/sample/columns.yaml',
            recordsPerPage: 10,
            recordUrl: 'kitchen-sink/:id/edit',
            defaultSort: ['column' => 'name', 'direction' => 'asc'],
        );
    }

    /**
     * makeToolbarWidget builds the list toolbar with its buttons and search.
     */
    protected function makeToolbarWidget(): Toolbar
    {
        return Toolbar::make(
            buttons: '~/resources/ui/sample/buttons.yaml',
            search: ['prompt' => 'Search samples...'],
        );
    }
}
