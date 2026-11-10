<?php

namespace App\Models;

use Database\Factories\SampleFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Larajax\Ui\Classes\FormField;
use Larajax\Ui\Element\ElementHolder;

/**
 * Sample is the record the reference pages edit, holding a value for every field type, form widget and form option they demonstrate.
 */
class Sample extends Model
{
    /** @use HasFactory<SampleFactory> */
    use HasFactory;

    /**
     * @var array<int, string> guarded attributes, so the reference pages fill every column their forms post, while a name that is no column is left out.
     */
    protected $guarded = ['id'];

    // #region docs-casts
    /**
     * casts stores the structured fields as JSON through array casts, and reads the switch and checkbox columns as booleans.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'features' => 'array',
            'permissions' => 'array',
            'interests' => 'array',
            'tags' => 'array',
            'topics' => 'array',
            'hashtags' => 'array',
            'content' => 'array',
            'steps' => 'array',
            'address' => 'array',
            'line_items' => 'array',
            'meta' => 'array',
            'shipping' => 'array',
            'contact' => 'array',
            'faqs' => 'array',
            'highlights' => 'array',
            'milestones' => 'array',
            'dimensions' => 'array',
            'sections' => 'array',
            'specs' => 'array',
            'itinerary' => 'array',
            'languages' => 'array',
            'deliveries' => 'array',
            'variants' => 'array',
            'stockists' => 'array',
            'parts' => 'array',
            'price_breaks' => 'array',
            'bundle_items' => 'array',
            'suppliers' => 'array',
            'components' => 'array',
            'stock_levels' => 'array',
            'reviews' => 'array',
            'spec_sheet' => 'array',
            'certifications' => 'array',
            'awards' => 'array',
            'seo' => 'array',
            'packaging' => 'array',
            'is_featured' => 'boolean',
            'has_discount' => 'boolean',
        ];
    }
    // #endregion docs-casts

    // #region docs-belongs-to
    /**
     * user is the owner, picked by the relation dropdown and the record finder.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * reviewer is a second user, picked by the record finder.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * primary_group is the group picked by the relation dropdown and edited in place by the nested form.
     */
    public function primary_group(): BelongsTo
    {
        return $this->belongsTo(UserGroup::class);
    }

    /**
     * primary_category is a category of the tree, picked by the tree dropdown and the record finder.
     */
    public function primary_category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    // #endregion docs-belongs-to

    // #region docs-belongs-to-many
    /**
     * groups are the groups picked by the checkbox list and the tag list.
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(UserGroup::class, 'sample_user_group');
    }

    /**
     * categories are the categories picked by the tree checkbox list.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_sample');
    }

    /**
     * labels are the tags picked or created by the tag list.
     */
    public function labels(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'sample_tag');
    }
    // #endregion docs-belongs-to-many

    // #region docs-has-many
    /**
     * links are the link records the repeater edits, in the order it saves them.
     */
    public function links(): HasMany
    {
        return $this->hasMany(SampleLink::class)->orderBy('sort_order');
    }
    // #endregion docs-has-many

    // #region docs-attachments
    /**
     * cover is a single image, a file record named by its field.
     */
    public function cover(): MorphOne
    {
        return $this->morphOne(File::class, 'attachment')->withAttributes(['field' => 'cover']);
    }

    /**
     * brochure is a single file.
     */
    public function brochure(): MorphOne
    {
        return $this->morphOne(File::class, 'attachment')->withAttributes(['field' => 'brochure']);
    }

    /**
     * contract is a single file stored privately, since the relation makes its files with is_public turned off.
     */
    public function contract(): MorphOne
    {
        return $this->morphOne(File::class, 'attachment')->withAttributes(['field' => 'contract', 'is_public' => false]);
    }

    /**
     * gallery holds many images, in their sort order.
     */
    public function gallery(): MorphMany
    {
        return $this->morphMany(File::class, 'attachment')->withAttributes(['field' => 'gallery'])->orderBy('sort_order');
    }

    /**
     * attachments holds many files, in their sort order.
     */
    public function attachments(): MorphMany
    {
        return $this->morphMany(File::class, 'attachment')->withAttributes(['field' => 'attachments'])->orderBy('sort_order');
    }

    /**
     * documents holds many files, in their sort order.
     */
    public function documents(): MorphMany
    {
        return $this->morphMany(File::class, 'attachment')->withAttributes(['field' => 'documents'])->orderBy('sort_order');
    }
    // #endregion docs-attachments

    // #region docs-filter-fields
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
    // #endregion docs-filter-fields

    // #region docs-category-options
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
    // #endregion docs-category-options

    // #region docs-level-options
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
    // #endregion docs-level-options

    // #region docs-dependent-options
    // #region docs-country-options
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
    // #endregion docs-country-options

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
    // #endregion docs-dependent-options

    // #region docs-list-currencies
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
    // #endregion docs-list-currencies

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

    // #region docs-holiday-dates
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
    // #endregion docs-holiday-dates

    // #region docs-brand-colors
    /**
     * getBrandColors returns the swatches of the brand color picker, named by its availableColors option.
     *
     * @return array<int, string>
     */
    public function getBrandColors(): array
    {
        return ['#0f766e', '#14b8a6', '#f59e0b', '#be123c'];
    }
    // #endregion docs-brand-colors

    // #region docs-stockists-options
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
    // #endregion docs-stockists-options

    // #region docs-owner-scope
    /**
     * applyOwnerScope narrows the owner record finder to verified users, as a static scope method.
     */
    public static function applyOwnerScope(Builder $query, self $sample): void
    {
        $query->whereNotNull('email_verified_at');
    }
    // #endregion docs-owner-scope
}
