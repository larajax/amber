<?php

namespace App\Models;

use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use October\Rain\Database\Model;
use October\Rain\Database\Traits\Validation;

/**
 * Post is a blog post that drives the posts list, filter, toolbar and form demos, with its relations declared in the array style the widgets inspect.
 */
#[Fillable([
    'title',
    'slug',
    'excerpt',
    'content',
    'notes',
    'keywords',
    'published',
    'published_at',
    'status',
    'featured_color',
    'views',
    'review_on',
    'share_time',
    'category',
    'author',
    'tags',
])]
class Post extends Model
{
    /** @use HasFactory<PostFactory> */
    use HasFactory;
    use Validation;

    /**
     * @var array<string, mixed> rules for validation, where the category rule reads the relation value.
     */
    public $rules = [
        'title' => 'required',
        'slug' => ['required', 'regex:/^[a-z0-9-]+$/', 'unique:posts'],
        'category' => 'required',
        'status' => 'in:draft,review,approved,archived',
        'views' => 'nullable|integer|min:0',
    ];

    /**
     * @var array<string, mixed> attributes holds the defaults of a new post.
     */
    public $attributes = [
        'status' => 'draft',
        'published' => false,
        'views' => 0,
    ];

    /**
     * @var array<int, string> jsonable attributes stored as JSON, filled by the keywords tag list in array mode.
     */
    protected $jsonable = ['keywords'];

    /**
     * @var array<string, mixed> belongsTo relations, where the author is a plain Eloquent model.
     */
    public $belongsTo = [
        'category' => Category::class,
        'author' => User::class,
    ];

    /**
     * @var array<string, mixed> belongsToMany relations
     */
    public $belongsToMany = [
        'tags' => [Tag::class, 'table' => 'post_tag'],
    ];

    /**
     * @var array<string, mixed> hasMany relations, counted by the comments list column.
     */
    public $hasMany = [
        'comments' => [Comment::class, 'delete' => true],
    ];

    /**
     * @var array<string, mixed> attachOne relations
     */
    public $attachOne = [
        'featured_image' => File::class,
    ];

    /**
     * casts returns the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'published' => 'boolean',
            'published_at' => 'datetime',
            'review_on' => 'date',
            'views' => 'integer',
        ];
    }

    /**
     * listStatusOptions returns the workflow statuses as a label and icon pair, shared by the form, list and filter.
     *
     * @return array<string, array{0: string, 1: string}>
     */
    public static function listStatusOptions(): array
    {
        return [
            'draft' => ['Draft', 'ph ph-pencil-simple'],
            'review' => ['In Review', 'ph ph-eye'],
            'approved' => ['Approved', 'ph ph-seal-check'],
            'archived' => ['Archived', 'ph ph-archive'],
        ];
    }

    /**
     * getStatusOptions supplies the status dropdown and the selectable status column by naming convention.
     *
     * @return array<string, array{0: string, 1: string}>
     */
    public function getStatusOptions(): array
    {
        return static::listStatusOptions();
    }

    /**
     * beforeSave stamps the publish date of a post published without one and keeps an emptied view count at zero.
     */
    public function beforeSave(): void
    {
        if ($this->published && !$this->published_at) {
            $this->published_at = $this->freshTimestamp();
        }

        if ($this->views === null) {
            $this->views = 0;
        }
    }

    /**
     * getPermalink returns the public address of the post, built from the category path and the slug.
     */
    public function getPermalink(): string
    {
        $categorySlugs = $this->category
            ? $this->category->getParentsAndSelf()->pluck('slug')->all()
            : [];

        return '/'.implode('/', array_filter(['blog', ...$categorySlugs, $this->slug]));
    }

    /**
     * filterFields fills the read-only permalink field, which the form refreshes when the category or slug changes.
     */
    public function filterFields($fields, $context = null): void
    {
        if (isset($fields->_permalink)) {
            $fields->_permalink->value = $this->getPermalink();
        }
    }

    /**
     * scopeApplySearch is the list search scope, matching any word in the searchable columns, the category name or a tag name, or the term in the author name.
     *
     * @param  array<int, mixed>  $columns
     */
    public function scopeApplySearch($query, string $term, array $columns)
    {
        return $query->where(function ($query) use ($term, $columns) {
            $query
                ->searchWhere($term, $columns, 'any')
                ->orWhereHas('category', function ($query) use ($term) {
                    $query->searchWhere($term, ['name'], 'any');
                })
                ->orWhereHas('tags', function ($query) use ($term) {
                    $query->searchWhere($term, ['name'], 'any');
                })
                ->orWhereHas('author', function ($query) use ($term) {
                    // The author is a plain Eloquent model, which has no searchWhere
                    $query->where('name', 'like', '%'.$term.'%');
                });
        });
    }
}
