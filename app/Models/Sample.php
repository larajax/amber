<?php

namespace App\Models;

use Database\Factories\SampleFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Larajax\Ui\Classes\FormField;
use October\Rain\Database\Model;
use October\Rain\Database\Traits\Validation;
use October\Rain\Element\ElementHolder;

/**
 * Sample is the record the reference pages edit, holding a value for every field type, form widget and form option they demonstrate.
 */
class Sample extends Model
{
    /** @use HasFactory<SampleFactory> */
    use HasFactory;
    use Validation;

    /**
     * @var array<int, string> guarded attributes, since the form saver sets each defined field explicitly.
     */
    protected $guarded = ['id'];

    /**
     * @var array<string, mixed> rules for validation, which also mark the matching form fields as required.
     */
    public $rules = [
        'name' => 'required',
        'slug' => 'nullable|alpha_dash',
        'email' => 'nullable|email',
        'price' => 'nullable|numeric|min:0',
        'quantity' => 'nullable|integer|between:0,999',
        'discount_percent' => 'nullable|integer|between:1,90',
    ];

    /**
     * @var array<int, string> jsonable attributes stored as JSON for the structured fields.
     */
    protected $jsonable = [
        'features',
        'permissions',
        'interests',
        'tags',
        'topics',
        'content',
        'steps',
        'address',
        'line_items',
        'meta',
        'shipping',
        'contact',
        'faqs',
        'highlights',
        'milestones',
        'dimensions',
        'sections',
        'specs',
        'itinerary',
        'languages',
        'deliveries',
        'variants',
        'stockists',
        'parts',
        'price_breaks',
        'bundle_items',
        'suppliers',
        'components',
        'stock_levels',
        'reviews',
        'spec_sheet',
        'certifications',
        'awards',
        'seo',
        'packaging',
    ];

    /**
     * @var array<string, mixed> belongsTo relations, the owner, reviewer, group and category picked by the relation widgets.
     */
    public $belongsTo = [
        'user' => User::class,
        'reviewer' => User::class,
        'primary_group' => UserGroup::class,
        'primary_category' => Category::class,
    ];

    /**
     * @var array<string, mixed> belongsToMany relations, the groups, categories and labels picked by checkbox and tag lists.
     */
    public $belongsToMany = [
        'groups' => [UserGroup::class, 'table' => 'sample_user_group'],
        'categories' => [Category::class, 'table' => 'category_sample'],
        'labels' => [Tag::class, 'table' => 'sample_tag'],
    ];

    /**
     * @var array<string, mixed> hasMany relations, the links the repeater edits as records.
     */
    public $hasMany = [
        'links' => [SampleLink::class, 'delete' => true],
    ];

    /**
     * @var array<string, mixed> attachOne relations, where the contract is stored privately.
     */
    public $attachOne = [
        'cover' => File::class,
        'brochure' => File::class,
        'contract' => [File::class, 'public' => false],
    ];

    /**
     * @var array<string, mixed> attachMany relations
     */
    public $attachMany = [
        'gallery' => File::class,
        'attachments' => File::class,
        'documents' => File::class,
    ];

    /**
     * casts returns the attribute casts for the switch and checkbox columns.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'has_discount' => 'boolean',
        ];
    }

    /**
     * filterFields adapts the postcode field to the picked country whenever the form renders or refreshes.
     */
    public function filterFields(ElementHolder $fields, ?string $context = null): void
    {
        if ($fields->postcode) {
            $fields->postcode->hidden = !$this->country;
            $fields->postcode->label = $this->country === 'us' ? 'ZIP Code' : 'Postcode';
            $fields->postcode->placeholder = $this->country === 'us' ? '10001' : '2000';
        }
    }

    /**
     * getCategoryOptions returns the category choices grouped under option group headings.
     *
     * @return array<string, array{optgroup: bool, children: array<string, string>}>
     */
    public function getCategoryOptions(): array
    {
        return [
            'Furniture' => [
                'optgroup' => true,
                'children' => [
                    'chairs' => 'Chairs',
                    'desks' => 'Desks',
                    'shelving' => 'Shelving',
                ],
            ],
            'Lighting' => [
                'optgroup' => true,
                'children' => [
                    'ceiling' => 'Ceiling Lights',
                    'lamps' => 'Lamps',
                ],
            ],
        ];
    }

    /**
     * getLevelOptions returns options with icons, colors, comments, a disabled entry and child options.
     *
     * @return array<string, array<string, mixed>>
     */
    public function getLevelOptions(): array
    {
        return [
            'basic' => [
                'label' => 'Basic',
                'comment' => 'Included with every plan',
                'icon' => 'ph ph-cube',
            ],
            'pro' => [
                'label' => 'Professional',
                'comment' => 'Adds priority support',
                'color' => '#3498db',
                'children' => [
                    'pro-monthly' => 'Professional, billed monthly',
                    'pro-yearly' => 'Professional, billed yearly',
                ],
            ],
            'legacy' => [
                'label' => 'Legacy',
                'comment' => 'No longer offered',
                'icon' => 'ph ph-archive',
                'disabled' => true,
            ],
        ];
    }

    /**
     * getCountryOptions returns the countries that start the dependent dropdown chain.
     *
     * @return array<string, string>
     */
    public function getCountryOptions(): array
    {
        return [
            'au' => 'Australia',
            'nz' => 'New Zealand',
            'us' => 'United States',
        ];
    }

    /**
     * getStateOptions returns the states of the picked country, refreshed through dependsOn.
     *
     * @return array<string, string>
     */
    public function getStateOptions(): array
    {
        return array_map(
            fn (array $state) => $state['name'],
            static::listRegions()[$this->country] ?? []
        );
    }

    /**
     * getCityOptions returns the cities of the picked state, refreshed through dependsOn.
     *
     * @return array<string, string>
     */
    public function getCityOptions(): array
    {
        return static::listRegions()[$this->country][$this->state]['cities'] ?? [];
    }

    /**
     * listCurrencies returns the currency choices, as a static options method named by optionsMethod.
     *
     * @return array<string, string>
     */
    public static function listCurrencies(self $sample, FormField $field): array
    {
        return [
            'aud' => 'Australian Dollar',
            'nzd' => 'New Zealand Dollar',
            'usd' => 'US Dollar',
        ];
    }

    /**
     * listRegions returns the states and cities for each country of the dependent dropdowns.
     *
     * @return array<string, array<string, array{name: string, cities: array<string, string>}>>
     */
    public static function listRegions(): array
    {
        return [
            'au' => [
                'nsw' => ['name' => 'New South Wales', 'cities' => ['sydney' => 'Sydney', 'newcastle' => 'Newcastle']],
                'vic' => ['name' => 'Victoria', 'cities' => ['melbourne' => 'Melbourne', 'geelong' => 'Geelong']],
            ],
            'nz' => [
                'auk' => ['name' => 'Auckland', 'cities' => ['auckland' => 'Auckland', 'manukau' => 'Manukau']],
                'wgn' => ['name' => 'Wellington', 'cities' => ['wellington' => 'Wellington', 'porirua' => 'Porirua']],
            ],
            'us' => [
                'ca' => ['name' => 'California', 'cities' => ['los-angeles' => 'Los Angeles', 'san-francisco' => 'San Francisco']],
                'ny' => ['name' => 'New York', 'cities' => ['new-york' => 'New York City', 'buffalo' => 'Buffalo']],
            ],
        ];
    }

    /**
     * getHolidayDates returns the public holidays the end date picker disables, as a static disableDays method.
     *
     * @return array<int, string>
     */
    public static function getHolidayDates(): array
    {
        $year = (int) date('Y');

        return [
            "{$year}-12-25",
            "{$year}-12-26",
            ($year + 1).'-01-01',
        ];
    }

    /**
     * getBrandColors returns the swatches of the brand color picker, named by its availableColors option.
     *
     * @return array<int, string>
     */
    public function getBrandColors(): array
    {
        return ['#0f766e', '#14b8a6', '#f59e0b', '#be123c'];
    }

    /**
     * getStockistsDataTableOptions returns the options of the stockists table's AJAX columns, where the cities follow the row's country.
     *
     * @param  array<string, mixed>  $rowData
     * @return array<string, string>
     */
    public function getStockistsDataTableOptions(?string $column, array $rowData = []): array
    {
        if ($column === 'channel') {
            return ['retail' => 'Retail', 'online' => 'Online', 'wholesale' => 'Wholesale'];
        }

        if ($column === 'city') {
            return array_merge([], ...array_column(static::listRegions()[$rowData['country'] ?? ''] ?? [], 'cities'));
        }

        return [];
    }

    /**
     * applyOwnerScope narrows the owner record finder to verified users, as a static scope method.
     */
    public static function applyOwnerScope(Builder $query, self $sample): void
    {
        $query->whereNotNull('email_verified_at');
    }
}
