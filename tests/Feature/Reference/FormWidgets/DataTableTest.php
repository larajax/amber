<?php

namespace Tests\Feature\Reference\FormWidgets;

use App\Controllers\Reference\FormWidgetsController;
use App\Models\Sample;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;
use Illuminate\Testing\TestResponse;
use Larajax\Ui\Widgets\Form;
use Tests\TestCase;

class DataTableTest extends TestCase
{
    use RefreshDatabase;

    /**
     * makeTableForm builds a form with one data table field named stockists, whose city options come from the sample.
     */
    protected function makeTableForm(Model $sample, array $config = []): Form
    {
        $form = Form::createIn(new FormWidgetsController, [
            'model' => $sample,
            'fields' => [
                'stockists' => $config + [
                    'label' => 'Stockists',
                    'type' => 'datatable',
                    'columns' => [
                        'name' => ['title' => 'Name'],
                        'country' => ['title' => 'Country', 'type' => 'dropdown', 'options' => ['au' => 'Australia', 'nz' => 'New Zealand']],
                        'city' => ['title' => 'City', 'type' => 'dropdown', 'dependsOn' => 'country'],
                    ],
                ],
            ],
        ]);

        $form->bindToController();

        return $form;
    }

    /**
     * postRequest swaps in a request carrying the given post data.
     */
    protected function postRequest(array $data): void
    {
        $this->app->instance('request', Request::create('/reference/form-widgets/data-table', 'POST', $data));
        Facade::clearResolvedInstance('request');
    }

    /**
     * attributeJson decodes the JSON of a data attribute rendered on the field.
     */
    protected function attributeJson(string $html, string $attribute): mixed
    {
        $this->assertMatchesRegularExpression("/{$attribute}='([^']*)'/", $html);
        preg_match("/{$attribute}='([^']*)'/", $html, $match);

        return json_decode(html_entity_decode($match[1], ENT_QUOTES), true);
    }

    /**
     * exampleHtml returns the markup of one example section on the page.
     */
    protected function exampleHtml(string $html, string $example): string
    {
        $this->assertMatchesRegularExpression('/<section class="ui-section" id="'.$example.'">(.*?)<\/section>/s', $html);
        preg_match('/<section class="ui-section" id="'.$example.'">(.*?)<\/section>/s', $html, $match);

        return $match[1];
    }

    /**
     * columns returns the column definitions of a rendered table, keyed by column name.
     */
    protected function columns(string $html): array
    {
        return collect($this->attributeJson($html, 'data-columns'))->keyBy('data')->all();
    }

    /**
     * ajax posts an AJAX handler to the data table page.
     */
    protected function ajax(string $handler, array $data = []): TestResponse
    {
        return $this->withHeaders([
            'X-AJAX-HANDLER' => $handler,
            'X-Requested-With' => 'XMLHttpRequest',
        ])->post(route('reference.form-widgets.data-table'), $data);
    }

    public function test_the_page_renders_every_example_beside_its_yaml(): void
    {
        Sample::factory()->create([
            'line_items' => [['item' => 'Lamp body', 'quantity' => 1, 'price' => 60, 'taxable' => 1]],
            'stockists' => [['name' => 'Lumen Store', 'country' => 'au', 'city' => 'sydney', 'channel' => 'retail']],
        ]);

        $html = $this->get(route('reference.form-widgets.data-table'))->assertOk()->getContent();

        $examples = [
            'default', 'dates', 'dropdowns', 'model-options', 'validation', 'fixed-rows', 'no-toolbar',
            'search-and-csv', 'sorting', 'height', 'empty-state', 'column-options', 'read-only', 'preview',
        ];

        foreach ($examples as $example) {
            $this->assertStringContainsString("resources/ui/reference/form-widgets/data-table/{$example}.yaml", $html);
        }

        $this->assertSame(14, substr_count($html, 'data-control="datatable"'));
        $this->assertSame(14, substr_count($html, '<div data-hot-container></div>'));
        $this->assertStringNotContainsString('handsontable', $html);
        $this->assertStringContainsString('data-request="onSaveExamples"', $html);
    }

    public function test_the_columns_example_renders_the_grid_with_its_toolbar_and_saved_rows(): void
    {
        Sample::factory()->create(['line_items' => [['item' => 'Lamp body', 'quantity' => 1, 'price' => 60, 'taxable' => 1]]]);

        $html = $this->exampleHtml($this->get(route('reference.form-widgets.data-table'))->getContent(), 'default');

        $this->assertStringContainsString('data-alias="exampleDefaultLineItems"', $html);
        $this->assertMatchesRegularExpression('/data-table-add>\s*<i class="ph ph-list-plus" aria-hidden="true"><\/i>\s*<span class="button-label">Insert Row<\/span>/', $html);
        $this->assertStringContainsString('<span class="button-label">Delete Row</span>', $html);
        $this->assertStringNotContainsString('data-table-export', $html);
        $this->assertSame('Remove row', $this->attributeJson($html, 'data-lang')['removeRow']);

        $columns = $this->columns($html);
        $this->assertSame(['item', 'quantity', 'price', 'taxable'], array_keys($columns));
        $this->assertSame('text', $columns['item']['type']);
        $this->assertSame(['width' => 90, 'type' => 'numeric'], array_intersect_key($columns['quantity'], array_flip(['type', 'width'])));
        $this->assertSame(['type' => 'checkbox', 'checkedTemplate' => 1, 'uncheckedTemplate' => 0], array_intersect_key($columns['taxable'], array_flip(['type', 'checkedTemplate', 'uncheckedTemplate'])));
        $this->assertSame(90, $columns['taxable']['width']);

        $this->assertMatchesRegularExpression('/name="line_items" value="\[\{&quot;item&quot;:&quot;Lamp body&quot;,&quot;quantity&quot;:1,&quot;price&quot;:60,&quot;taxable&quot;:1\}\]"/', $html);
    }

    public function test_the_examples_map_their_column_types_and_options(): void
    {
        Sample::factory()->create();

        $page = $this->get(route('reference.form-widgets.data-table'))->getContent();

        $dates = $this->columns($this->exampleHtml($page, 'dates'));
        $this->assertSame(['date', 'YYYY-MM-DD', true], [$dates['date']['type'], $dates['date']['dateFormat'], $dates['date']['correctFormat']]);
        $this->assertSame(['time', 'HH:mm'], [$dates['from']['type'], $dates['from']['timeFormat']]);
        $this->assertSame('h:mm A', $dates['until']['timeFormat']);

        $dropdowns = $this->columns($this->exampleHtml($page, 'dropdowns'));
        $this->assertSame([['key' => 'brass', 'value' => 'Brushed Brass'], ['key' => 'chrome', 'value' => 'Polished Chrome'], ['key' => 'matte', 'value' => 'Matte Black']], $dropdowns['finish']['source']);
        $this->assertSame([true, false], [$dropdowns['finish']['strict'], $dropdowns['finish']['filter']]);
        $this->assertSame(['autocomplete', false, true], [$dropdowns['color']['type'], $dropdowns['color']['strict'], $dropdowns['color']['filter']]);

        $validation = $this->exampleHtml($page, 'validation');
        $this->assertStringContainsString('data-require-valid="true"', $validation);
        $rules = $this->columns($validation);
        $this->assertSame(['required' => ['message' => 'Name the part.'], 'length' => ['max' => ['value' => 40, 'message' => 'Keep the name under 40 characters.']]], $rules['name']['_validation']);
        $this->assertSame(['regex' => ['pattern' => '^[A-Z]{2}-\d{3}$', 'message' => 'Use two capitals, a dash and three digits, such as LP-104.']], $rules['sku']['_validation']);
        $this->assertSame(['integer' => ['min' => ['value' => 1], 'max' => ['value' => 99]]], $rules['quantity']['_validation']);
        $this->assertSame(['float' => ['allowNegative' => false]], $rules['weight']['_validation']);
        $this->assertSame(['required' => ['requiredWith' => 'weight', 'message' => 'A weighed part needs a supplier.']], $rules['supplier']['_validation']);

        $specs = $this->columns($this->exampleHtml($page, 'column-options'));
        $this->assertSame(['readOnly' => true, 'width' => '30%'], array_intersect_key($specs['label'], array_flip(['readOnly', 'width'])));
        $this->assertSame([true, false], [$specs['value']['wordWrap'], $specs['value']['textEllipsis']]);
        $this->assertSame([true, 160], [$specs['note']['textEllipsis'], $specs['note']['width']]);
    }

    public function test_the_model_options_example_loads_its_options_from_the_sample(): void
    {
        Sample::factory()->create([
            'stockists' => [
                ['name' => 'Lumen Store', 'country' => 'au', 'city' => 'sydney', 'channel' => 'retail'],
                ['name' => 'Kiwi Lighting', 'country' => 'nz', 'city' => 'wellington', 'channel' => 'online'],
            ],
        ]);

        $html = $this->exampleHtml($this->get(route('reference.form-widgets.data-table'))->getContent(), 'model-options');

        $this->assertStringContainsString('data-alias="exampleModelOptionsStockists"', $html);
        $this->assertSame(['city', 'channel'], $this->attributeJson($html, 'data-ajax-columns'));
        $this->assertSame(['city' => 'country'], $this->attributeJson($html, 'data-column-dependencies'));
        $this->assertArrayNotHasKey('source', $this->columns($html)['city']);

        $preloaded = $this->attributeJson($html, 'data-preloaded-options');
        $this->assertSame(['values' => ['country' => 'au'], 'options' => ['sydney' => 'Sydney', 'newcastle' => 'Newcastle', 'melbourne' => 'Melbourne', 'geelong' => 'Geelong']], $preloaded['city'][0]);
        $this->assertSame([['values' => [], 'options' => ['retail' => 'Retail', 'online' => 'Online', 'wholesale' => 'Wholesale']]], $preloaded['channel']);
    }

    public function test_the_toolbar_examples_follow_their_options(): void
    {
        Sample::factory()->create();

        $page = $this->get(route('reference.form-widgets.data-table'))->getContent();

        $fixed = $this->exampleHtml($page, 'fixed-rows');
        $this->assertStringContainsString('data-adding="false"', $fixed);
        $this->assertStringContainsString('data-deleting="false"', $fixed);
        $this->assertStringNotContainsString('data-table-toolbar', $fixed);

        $bare = $this->exampleHtml($page, 'no-toolbar');
        $this->assertStringContainsString('data-adding="true"', $bare);
        $this->assertStringNotContainsString('data-table-toolbar', $bare);

        $search = $this->exampleHtml($page, 'search-and-csv');
        $this->assertStringContainsString('<span class="button-label">Export</span>', $search);
        $this->assertStringContainsString('<span class="button-label">Import</span>', $search);
        $this->assertStringContainsString('data-table-import-file', $search);
        $this->assertMatchesRegularExpression('/<div class="toolbar-search">\s*<label class="form-control form-adorn">\s*<span class="form-adorn-icon" aria-hidden="true">\s*<i class="ph ph-magnifying-glass" aria-hidden="true"><\/i>\s*<\/span>\s*<input type="text" class="form-ghost" aria-label="Search..." placeholder="Search..." data-table-search>\s*<\/label>/', $search);
        $this->assertTrue($this->attributeJson($search, 'data-hot-options')['search']);
    }

    public function test_the_grid_examples_render_their_settings(): void
    {
        Sample::factory()->create();

        $page = $this->get(route('reference.form-widgets.data-table'))->getContent();

        $sorting = $this->attributeJson($this->exampleHtml($page, 'sorting'), 'data-hot-options');
        $this->assertTrue($sorting['columnSorting']);
        $this->assertTrue($sorting['rowHeaders']);
        $this->assertTrue($sorting['manualRowMove']);
        $this->assertTrue($sorting['manualColumnMove']);
        $this->assertSame('single', $sorting['selectionMode']);

        $this->assertSame(220, $this->attributeJson($this->exampleHtml($page, 'height'), 'data-hot-options')['height']);
        $this->assertSame('auto', $this->attributeJson($this->exampleHtml($page, 'default'), 'data-hot-options')['height']);

        $empty = $this->attributeJson($this->exampleHtml($page, 'empty-state'), 'data-hot-options');
        $this->assertSame(['message' => ['title' => 'No reviews yet. Insert a row to add one.']], $empty['emptyDataState']);
        $this->assertArrayNotHasKey('minRows', $empty);
    }

    public function test_the_read_only_and_preview_examples_render_a_read_only_grid(): void
    {
        Sample::factory()->create();

        $page = $this->get(route('reference.form-widgets.data-table'))->getContent();

        $readOnly = $this->exampleHtml($page, 'read-only');
        $this->assertStringContainsString('data-read-only="true"', $readOnly);
        $this->assertStringContainsString('data-table-export', $readOnly);
        $this->assertStringContainsString('data-table-search', $readOnly);
        $this->assertStringNotContainsString('data-table-add', $readOnly);
        $this->assertMatchesRegularExpression('/name="certifications" value="\[\]"\s+\/>/', $readOnly);

        $preview = $this->exampleHtml($page, 'preview');
        $this->assertStringContainsString('data-read-only="true"', $preview);
        $this->assertStringNotContainsString('data-table-toolbar', $preview);
        $this->assertStringContainsString('name="awards" value="[]" disabled />', $preview);
    }

    public function test_saving_the_page_stores_the_rows_in_their_posted_shape(): void
    {
        $sample = Sample::factory()->create(['awards' => [['award' => 'Kept', 'year' => 2025]]]);

        $stockists = [
            ['name' => 'Lumen Store', 'country' => 'au', 'city' => 'sydney', 'channel' => 'retail'],
            ['name' => 'Kiwi Lighting', 'country' => 'nz', 'city' => null, 'channel' => 'Pop-up'],
        ];
        $lineItems = [['item' => 'Lamp body', 'quantity' => 1, 'price' => 60, 'taxable' => 1]];
        $deliveries = [['date' => '2026-11-04', 'from' => '09:00', 'until' => '1:00 PM', 'carrier' => 'Harbour Freight']];

        $this->ajax('onSaveExamples', [
            'line_items' => json_encode($lineItems),
            'deliveries' => json_encode($deliveries),
            'stockists' => json_encode($stockists),
            'reviews' => '[]',
            'parts' => 'not json',
        ])->assertOk();

        $sample->refresh();
        $this->assertSame($lineItems, $sample->line_items);
        $this->assertSame($deliveries, $sample->deliveries);
        $this->assertSame($stockists, $sample->stockists);
        $this->assertSame($stockists, json_decode($sample->getRawOriginal('stockists'), true));
        $this->assertSame([], $sample->reviews);
        $this->assertSame([], $sample->parts);

        // The preview example posts nothing, so its rows stay
        $this->assertSame([['award' => 'Kept', 'year' => 2025]], $sample->awards);
    }

    public function test_dropdown_options_come_from_the_model_for_the_row(): void
    {
        Sample::factory()->create();

        $response = $this->ajax('exampleModelOptionsStockists::onGetDropdownOptions', [
            'column' => 'city',
            'rowData' => ['name' => 'Kiwi Lighting', 'country' => 'nz', 'city' => ''],
        ]);

        $response->assertOk();
        $this->assertSame(['auckland' => 'Auckland', 'manukau' => 'Manukau', 'wellington' => 'Wellington', 'porirua' => 'Porirua'], $response->json('options'));
    }

    public function test_csv_export_downloads_the_posted_rows_under_the_column_titles(): void
    {
        Sample::factory()->create();

        $response = $this->ajax('exampleSearchAndCsvSuppliers::onExportCsv', [
            'tableData' => json_encode([
                ['name' => 'Lumen Works', 'contact' => 'Mia Lumen', 'email' => 'mia@example.com', 'lead_days' => 14],
                ['name' => '=HYPERLINK("x")', 'contact' => '-1', 'lead_days' => 0],
            ]),
        ]);

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=utf-8');
        $response->assertHeader('Content-Disposition', 'attachment; filename="suppliers.csv"');

        $lines = explode("\n", trim($response->getContent()));
        $this->assertSame("\xEF\xBB\xBFName,Contact,Email,\"Lead Days\"", $lines[0]);
        $this->assertSame('"Lumen Works","Mia Lumen",mia@example.com,14', $lines[1]);
        $this->assertSame('"\'=HYPERLINK(""x"")",\'-1,,0', $lines[2]);
    }

    public function test_csv_import_matches_header_titles_and_names_and_skips_empty_rows(): void
    {
        Sample::factory()->create();

        $csv = implode("\n", [
            'NAME;email;Lead Days;Unknown',
            'Lumen Works;mia@example.com;14;ignored',
            ';;;',
            'Bright Glass;ana@example.com;;',
        ]);

        $response = $this->ajax('exampleSearchAndCsvSuppliers::onImportCsv', ['csvData' => $csv]);

        $response->assertOk();
        $this->assertSame([
            ['name' => 'Lumen Works', 'email' => 'mia@example.com', 'lead_days' => '14'],
            ['name' => 'Bright Glass', 'email' => 'ana@example.com', 'lead_days' => ''],
        ], $response->json('rows'));
    }

    public function test_an_empty_value_renders_an_empty_list(): void
    {
        $html = (string) $this->makeTableForm(Sample::factory()->create())->render();

        $this->assertStringContainsString('name="stockists" value="[]"', $html);
        $this->assertSame(1, $this->attributeJson($html, 'data-hot-options')['minRows']);
    }

    public function test_options_render_as_grid_settings(): void
    {
        $html = (string) $this->makeTableForm(Sample::factory()->create(), [
            'placeholder' => 'No stockists yet',
            'height' => 300,
            'sorting' => true,
            'reorderRows' => true,
            'reorderColumns' => true,
            'multiSelect' => false,
        ])->render();

        $options = $this->attributeJson($html, 'data-hot-options');
        $this->assertSame(['message' => ['title' => 'No stockists yet']], $options['emptyDataState']);
        $this->assertArrayNotHasKey('minRows', $options);
        $this->assertSame(300, $options['height']);
        $this->assertTrue($options['columnSorting']);
        $this->assertTrue($options['rowHeaders']);
        $this->assertTrue($options['manualRowMove']);
        $this->assertTrue($options['manualColumnMove']);
        $this->assertTrue($options['manualColumnFreeze']);
        $this->assertSame('single', $options['selectionMode']);
    }

    public function test_the_toolbar_follows_the_adding_deleting_and_toolbar_options(): void
    {
        $sample = Sample::factory()->create();

        $locked = (string) $this->makeTableForm($sample, ['adding' => false, 'deleting' => false])->render();
        $this->assertStringContainsString('data-adding="false"', $locked);
        $this->assertStringContainsString('data-deleting="false"', $locked);
        $this->assertStringNotContainsString('data-table-add', $locked);
        $this->assertStringNotContainsString('data-table-delete', $locked);
        $this->assertStringNotContainsString('data-table-export', $locked);
        $this->assertStringNotContainsString('data-table-search', $locked);

        $bare = (string) $this->makeTableForm($sample, ['toolbar' => false])->render();
        $this->assertStringNotContainsString('data-table-toolbar', $bare);
        $this->assertStringContainsString('data-adding="true"', $bare);
    }

    public function test_columns_map_types_widths_and_text_options(): void
    {
        $html = (string) $this->makeTableForm(Sample::factory()->create(), [
            'columns' => [
                'qty' => ['title' => 'Qty', 'type' => 'numeric', 'width' => '15%', 'readOnly' => true],
                'note' => ['title' => 'Note', 'wrap' => true, 'width' => '200px'],
                'tag' => ['title' => 'Tag', 'type' => 'autocomplete', 'options' => ['a' => 'Alpha']],
                'at' => ['title' => 'At', 'type' => 'time', 'timeFormat' => 'h:mm A'],
                'source' => ['type' => 'autocomplete', 'options' => 'getSources'],
            ],
        ])->render();

        $columns = $this->columns($html);
        $this->assertSame(['data' => 'qty', 'title' => 'Qty', 'readOnly' => true, 'width' => '15%', 'type' => 'numeric'], $columns['qty']);
        $this->assertSame(200, $columns['note']['width']);
        $this->assertTrue($columns['note']['wordWrap']);
        $this->assertSame(['autocomplete', false, true], [$columns['tag']['type'], $columns['tag']['strict'], $columns['tag']['filter']]);
        $this->assertSame('h:mm A', $columns['at']['timeFormat']);
        $this->assertSame('source', $columns['source']['title']);
        $this->assertSame(['source'], $this->attributeJson($html, 'data-ajax-columns'));
    }

    public function test_an_empty_or_invalid_post_saves_an_empty_list(): void
    {
        $widget = $this->makeTableForm(Sample::factory()->create())->getFormWidget('stockists');

        $this->assertSame([], $widget->getSaveValue('[]'));
        $this->assertSame([], $widget->getSaveValue(''));
        $this->assertSame([], $widget->getSaveValue('not json'));
    }

    public function test_a_field_specific_options_method_wins_over_the_generic_one(): void
    {
        $sample = new class extends Sample {
            protected $table = 'samples';

            public function getDataTableOptions($fieldName, $column, $rowData): array
            {
                return ['generic' => 'Generic'];
            }
        };

        $widget = $this->makeTableForm($sample)->getFormWidget('stockists');

        $this->postRequest(['column' => 'city', 'rowData' => ['country' => 'au']]);
        $this->assertSame(['options' => ['sydney' => 'Sydney', 'newcastle' => 'Newcastle', 'melbourne' => 'Melbourne', 'geelong' => 'Geelong']], $widget->onGetDropdownOptions());
    }

    public function test_the_generic_options_method_answers_other_fields(): void
    {
        $sample = new class extends Sample {
            protected $table = 'samples';

            public function getDataTableOptions($fieldName, $column, $rowData): array
            {
                return ['x' => $fieldName.':'.$column.':'.($rowData['country'] ?? '')];
            }
        };

        $form = Form::createIn(new FormWidgetsController, [
            'model' => $sample,
            'fields' => ['contacts' => ['type' => 'datatable', 'columns' => ['city' => ['type' => 'dropdown']]]],
        ]);
        $form->bindToController();

        $this->postRequest(['column' => 'city', 'rowData' => ['country' => 'nz']]);
        $this->assertSame(['options' => ['x' => 'contacts:city:nz']], $form->getFormWidget('contacts')->onGetDropdownOptions());
    }

    public function test_a_model_without_options_methods_returns_no_options(): void
    {
        $model = new class extends Model {
            protected $table = 'samples';
        };

        $form = Form::createIn(new FormWidgetsController, [
            'model' => $model,
            'fields' => ['stockists' => ['type' => 'datatable', 'columns' => ['city' => ['type' => 'dropdown']]]],
        ]);
        $form->bindToController();

        $this->postRequest(['column' => 'city', 'rowData' => []]);
        $this->assertSame(['options' => []], $form->getFormWidget('stockists')->onGetDropdownOptions());
    }

    public function test_saved_dropdown_values_render_with_their_options_resolved_once_per_dependency_value(): void
    {
        $sample = new class extends Sample {
            protected $table = 'samples';

            public array $lookups = [];

            public function getStockistsDataTableOptions(?string $column, array $rowData = []): array
            {
                $this->lookups[] = [$column, $rowData];

                return parent::getStockistsDataTableOptions($column, $rowData);
            }
        };

        $sample->stockists = [
            ['name' => 'Ada', 'country' => 'us', 'city' => 'new-york'],
            ['name' => 'Bob', 'country' => 'au', 'city' => 'sydney'],
            ['name' => 'Cy', 'country' => 'us', 'city' => 'buffalo'],
            ['name' => 'Di', 'country' => 'nz'],
            ['name' => 'Ed'],
        ];

        $html = (string) $this->makeTableForm($sample)->render();

        $this->assertSame(['city' => [
            ['values' => ['country' => 'us'], 'options' => ['los-angeles' => 'Los Angeles', 'san-francisco' => 'San Francisco', 'new-york' => 'New York City', 'buffalo' => 'Buffalo']],
            ['values' => ['country' => 'au'], 'options' => ['sydney' => 'Sydney', 'newcastle' => 'Newcastle', 'melbourne' => 'Melbourne', 'geelong' => 'Geelong']],
            ['values' => ['country' => 'nz'], 'options' => ['auckland' => 'Auckland', 'manukau' => 'Manukau', 'wellington' => 'Wellington', 'porirua' => 'Porirua']],
        ]], $this->attributeJson($html, 'data-preloaded-options'));

        $this->assertSame([
            ['city', ['name' => 'Ada', 'country' => 'us', 'city' => 'new-york']],
            ['city', ['name' => 'Bob', 'country' => 'au', 'city' => 'sydney']],
            ['city', ['name' => 'Di', 'country' => 'nz', 'city' => null]],
        ], $sample->lookups);
    }

    public function test_a_column_without_dependencies_preloads_its_options_once_and_empty_tables_preload_nothing(): void
    {
        $sample = new class extends Sample {
            protected $table = 'samples';

            public int $lookups = 0;

            public function getStockistsDataTableOptions(?string $column, array $rowData = []): array
            {
                $this->lookups++;

                return ['web' => 'Website', 'event' => 'Event'];
            }
        };

        $columns = ['columns' => ['name' => ['title' => 'Name'], 'source' => ['title' => 'Source', 'type' => 'autocomplete']]];

        $html = (string) $this->makeTableForm($sample, $columns)->render();
        $this->assertStringContainsString("data-preloaded-options='{}'", $html);
        $this->assertSame(0, $sample->lookups);

        $sample->stockists = [['name' => 'Ada', 'source' => 'web'], ['name' => 'Bob', 'source' => 'event'], ['name' => 'Cy']];

        $html = (string) $this->makeTableForm($sample, $columns)->render();
        $this->assertSame(['source' => [['values' => [], 'options' => ['web' => 'Website', 'event' => 'Event']]]], $this->attributeJson($html, 'data-preloaded-options'));
        $this->assertSame(1, $sample->lookups);
    }

    public function test_preview_read_only_and_disabled_render_a_read_only_grid_without_row_buttons(): void
    {
        $sample = Sample::factory()->create();
        $config = ['searching' => true, 'csvExport' => true, 'csvImport' => true, 'requireValid' => true];

        $editable = (string) $this->makeTableForm($sample, $config)->render();
        $this->assertStringContainsString('data-read-only="false"', $editable);
        $this->assertStringContainsString('data-table-add', $editable);
        $this->assertStringContainsString('data-table-import-file', $editable);
        $this->assertStringContainsString('data-require-valid="true"', $editable);
        $this->assertMatchesRegularExpression('/name="stockists" value="\[\]"\s+\/>/', $editable);

        $modes = [
            'preview' => [(string) $this->makeTableForm($sample, $config)->render(['preview' => true]), true],
            'readOnly' => [(string) $this->makeTableForm($sample, $config + ['readOnly' => true])->render(), false],
            'disabled' => [(string) $this->makeTableForm($sample, $config + ['disabled' => true])->render(), true],
        ];

        foreach ($modes as $mode => [$html, $inputDisabled]) {
            $this->assertStringContainsString('data-read-only="true"', $html, $mode);
            $this->assertStringContainsString('data-adding="false"', $html, $mode);
            $this->assertStringContainsString('data-deleting="false"', $html, $mode);
            $this->assertStringContainsString('data-require-valid="false"', $html, $mode);
            $this->assertStringNotContainsString('data-table-add', $html, $mode);
            $this->assertStringNotContainsString('data-table-delete', $html, $mode);
            $this->assertStringNotContainsString('data-table-import', $html, $mode);
            $this->assertStringContainsString('data-table-export', $html, $mode);
            $this->assertStringContainsString('data-table-search', $html, $mode);
            $this->assertSame($inputDisabled, (bool) preg_match('/name="stockists" value="\[\]" disabled \/>/', $html), $mode);
        }

        $bare = (string) $this->makeTableForm($sample, ['readOnly' => true])->render();
        $this->assertStringNotContainsString('data-table-toolbar', $bare);
    }

    public function test_the_validation_messages_pass_through_the_translator(): void
    {
        $html = (string) $this->makeTableForm(Sample::factory()->create())->render();
        $lang = $this->attributeJson($html, 'data-lang');

        $this->assertSame('The value should not be empty.', $lang['ruleRequired']);
        $this->assertSame('The value should not be more than :max', $lang['ruleMax']);
        $this->assertSame('Invalid value format.', $lang['ruleRegex']);
    }

    public function test_csv_import_without_a_header_uses_the_column_order(): void
    {
        $widget = $this->makeTableForm(Sample::factory()->create())->getFormWidget('stockists');

        $this->postRequest(['csvData' => "Ada\tau\tsydney\nBob\tnz"]);

        $this->assertSame(['rows' => [
            ['name' => 'Ada', 'country' => 'au', 'city' => 'sydney'],
            ['name' => 'Bob', 'country' => 'nz', 'city' => null],
        ]], $widget->onImportCsv());
    }
}
