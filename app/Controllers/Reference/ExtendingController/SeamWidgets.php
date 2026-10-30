<?php

namespace App\Controllers\Reference\ExtendingController;

use App\Models\Sample;
use Larajax\Ui\Widgets\Filter;
use Larajax\Ui\Widgets\Lists;
use Larajax\Ui\Widgets\Toolbar;

/**
 * SeamWidgets builds the lists, toolbars and filter on the URLs and Timezones page.
 */
trait SeamWidgets
{
    /**
     * makeSeamWidgets builds every widget on the page, keyed by the view variable that prints it.
     *
     * @return array<string, Lists|Toolbar|Filter>
     */
    protected function makeSeamWidgets(): array
    {
        $folder = '~/resources/ui/reference/extending/seams';

        $timezoneList = Lists::make(
            alias: 'timezoneList',
            model: new Sample,
            columns: "{$folder}/timezone-columns.yaml",
            recordsPerPage: 5,
        );

        $timezoneFilter = Filter::make(
            alias: 'timezoneFilter',
            model: new Sample,
            scopes: "{$folder}/timezone-scopes.yaml",
        );

        $timezoneFilter->bindToListWidget($timezoneList);

        return [
            // #region docs-path-widgets
            'pathToolbar' => Toolbar::make(alias: 'pathToolbar', buttons: "{$folder}/path-buttons.yaml"),
            'pathList' => Lists::make(
                alias: 'pathList',
                model: new Sample,
                columns: "{$folder}/path-columns.yaml",
                recordUrl: 'samples/:id/edit',
                recordsPerPage: 5,
            ),
            // #endregion docs-path-widgets
            'linkToolbar' => Toolbar::make(alias: 'linkToolbar', buttons: "{$folder}/link-buttons.yaml"),
            'linkList' => Lists::make(
                alias: 'linkList',
                model: new Sample,
                columns: "{$folder}/link-columns.yaml",
                recordsPerPage: 5,
            ),
            'timezoneList' => $timezoneList,
            'timezoneFilter' => $timezoneFilter,
        ];
    }
}
