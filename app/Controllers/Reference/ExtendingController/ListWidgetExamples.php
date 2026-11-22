<?php

namespace App\Controllers\Reference\ExtendingController;

use App\Models\Post;
use Larajax\Ui\Widgets\Lists;

/**
 * ListWidgetExamples builds the list that shows the app's own list widget on the Custom Widgets page.
 */
trait ListWidgetExamples
{
    /**
     * makeMeterList builds a list of posts whose views column is the app's meter list widget.
     */
    protected function makeMeterList(): Lists
    {
        return Lists::make(
            alias: 'meterList',
            model: new Post,
            columns: '~/resources/ui/reference/extending/custom-widgets/meter.yaml',
            recordsPerPage: 5,
            defaultSort: ['column' => 'views', 'direction' => 'desc'],
            customPageName: false,
        );
    }
}
