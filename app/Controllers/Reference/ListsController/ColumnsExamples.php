<?php

namespace App\Controllers\Reference\ListsController;

use App\Models\Post;
use App\Models\Sample;
use Illuminate\Support\Str;
use Larajax\Ui\Widgets\Lists;

/**
 * ColumnsExamples builds the small lists on the Columns page, one for each column example.
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
}
