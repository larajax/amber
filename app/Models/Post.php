<?php

namespace App\Models;

use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * Post is a blog post that drives the posts list, filter, toolbar and form demos, with its relations declared as methods the widgets inspect.
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
    'category_id',
    'author_id',
])]
class Post extends Model
{
    /** @use HasFactory<PostFactory> */
    use HasFactory;

    /**
     * @var array<string, mixed> attributes holds the defaults of a new post.
     */
    protected $attributes = [
        'status' => 'draft',
        'published' => false,
        'views' => 0,
    ];

    // #region docs-relations
    /**
     * category is the category the post is filed in.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * author is the user who wrote the post.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * tags label the post, picked or created by the tag list.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'post_tag');
    }

    /**
     * comments are the reader comments, counted by the comments list column.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * featured_image is the image uploaded for the post, a file record named by its field.
     */
    public function featured_image(): MorphOne
    {
        return $this->morphOne(File::class, 'attachment')->withAttributes(['field' => 'featured_image']);
    }
    // #endregion docs-relations

    // #region docs-casts
    /**
     * casts returns the attributes that should be cast, where the array cast stores the keywords tag list as JSON.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'keywords' => 'array',
            'published' => 'boolean',
            'published_at' => 'datetime',
            'review_on' => 'date',
            'views' => 'integer',
        ];
    }
    // #endregion docs-casts

    /**
     * booted stamps the publish date of a post published without one, keeps an emptied view count at zero and deletes the featured image with the post.
     */
    protected static function booted(): void
    {
        static::saving(function (Post $post) {
            if ($post->published && !$post->published_at) {
                $post->published_at = $post->freshTimestamp();
            }

            $post->views ??= 0;
        });

        static::deleting(function (Post $post) {
            $post->featured_image?->delete();
        });
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

    // #region docs-list-status-color-options
    /**
     * listStatusColorOptions returns the workflow statuses as a label and color pair, which a dropdown shows as a status dot.
     *
     * @return array<string, array{0: string, 1: string}>
     */
    public static function listStatusColorOptions(): array
    {
        return [
            'draft' => ['Draft', 'var(--bs-orange-500)'],
            'review' => ['In Review', 'var(--bs-indigo-500)'],
            'approved' => ['Approved', 'var(--bs-green-500)'],
            'archived' => ['Archived', 'var(--bs-gray-400)'],
        ];
    }
    // #endregion docs-list-status-color-options

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
     * getPermalink returns the public address of the post, built from the category path and the slug.
     */
    public function getPermalink(): string
    {
        $categorySlugs = $this->category
            ? $this->category->getParentsAndSelf()->pluck('slug')->all()
            : [];

        return '/'.implode('/', array_filter(['blog', ...$categorySlugs, $this->slug]));
    }

    // #region docs-filter-fields
    /**
     * filterFields fills the read-only permalink field, which the form refreshes when the category or slug changes.
     */
    public function filterFields($fields, $context = null): void
    {
        if (isset($fields->_permalink)) {
            $fields->_permalink->value = $this->getPermalink();
        }
    }
    // #endregion docs-filter-fields

    // #region docs-apply-search
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
                    $query->searchWhere($term, ['name'], 'any');
                });
        });
    }
    // #endregion docs-apply-search
}
