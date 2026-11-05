<?php

namespace App\Models;

use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use October\Contracts\Database\SortableInterface;
use October\Contracts\Database\TreeInterface;

// #region docs-tree
/**
 * Category is a post category stored as a simple tree, where each category points at its parent and keeps its place among its siblings in a sort order column.
 */
#[Fillable(['name', 'slug', 'description', 'parent_id'])]
class Category extends Model implements SortableInterface, TreeInterface
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory;

    /**
     * parent is the category this one sits below, or null at the top level.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * children are the categories directly below this one, in their sort order.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('sort_order');
    }

    /**
     * getChildren returns the categories directly below this one, nested in memory by getNested.
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    /**
     * getChildCount returns the number of categories anywhere below this one.
     */
    public function getChildCount(): int
    {
        return $this->getAllChildren()->count();
    }

    /**
     * scopeGetNested returns the top level categories of the query with their children nested below them, leaving out categories whose parent the query skipped.
     */
    public function scopeGetNested($query): Collection
    {
        $categories = $query->orderBy('sort_order')->get()->keyBy('id');

        foreach ($categories as $category) {
            $category->setRelation('children', $category->newCollection());
        }

        $roots = $this->newCollection();

        foreach ($categories as $category) {
            if (!$category->parent_id) {
                $roots->push($category);
            }
            elseif ($parent = $categories->get($category->parent_id)) {
                $parent->children->push($category);
            }
        }

        return $roots;
    }

    /**
     * scopeListsNested returns the column values keyed by the key column, in tree order and indented by depth.
     *
     * @return array<int|string, string>
     */
    public function scopeListsNested($query, $column, $key = null, $indent = '&nbsp;&nbsp;&nbsp;'): array
    {
        $result = [];

        $addCategories = function (Collection $categories, int $depth) use (&$addCategories, &$result, $column, $key, $indent) {
            foreach ($categories as $category) {
                $label = str_repeat($indent, $depth).$category->{$column};

                if ($key === null) {
                    $result[] = $label;
                }
                else {
                    $result[$category->{$key}] = $label;
                }

                $addCategories($category->getChildren(), $depth + 1);
            }
        };

        $addCategories($query->getNested(), 0);

        return $result;
    }
    // #endregion docs-tree

    // #region docs-sortable-order
    /**
     * setSortableOrder stores a new order for the given categories, numbered from one unless the orders are supplied.
     *
     * @param  array<int, int|string>  $itemIds
     * @param  array<int, int>|bool|null  $itemOrders
     */
    public function setSortableOrder($itemIds, $itemOrders = null): void
    {
        if (!is_array($itemIds)) {
            return;
        }

        $itemIds = array_values($itemIds);
        $orders = is_array($itemOrders) && count($itemOrders) === count($itemIds)
            ? array_values($itemOrders)
            : range(1, count($itemIds));

        foreach ($itemIds as $index => $id) {
            static::query()->whereKey($id)->update(['sort_order' => $orders[$index]]);
        }
    }
    // #endregion docs-sortable-order

    /**
     * booted places a new category after the others and deletes the categories below one being deleted.
     */
    protected static function booted(): void
    {
        static::creating(function (Category $category) {
            $category->sort_order ??= (int) static::query()->max('sort_order') + 1;
        });

        static::deleting(function (Category $category) {
            $category->children->each->delete();
        });
    }

    /**
     * posts are the posts filed in this category.
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    // #region docs-has-posts
    /**
     * scopeHasPosts limits the query to categories holding at least one post, used by the posts category filter.
     */
    public function scopeHasPosts($query)
    {
        return $query->has('posts');
    }
    // #endregion docs-has-posts

    /**
     * getAllChildren returns every category below this one, at any depth.
     */
    public function getAllChildren(): Collection
    {
        $result = $this->newCollection();

        foreach ($this->getChildren() as $child) {
            $result->push($child);
            $result = $result->merge($child->getAllChildren());
        }

        return $result;
    }

    /**
     * getParentsAndSelf returns the path from the top level category down to this one.
     */
    public function getParentsAndSelf(): Collection
    {
        $path = $this->newCollection([$this]);

        for ($parent = $this->parent; $parent; $parent = $parent->parent) {
            $path->prepend($parent);
        }

        return $path;
    }

    /**
     * hasPostsInTree checks whether this category or any category below it holds a post.
     */
    public function hasPostsInTree(): bool
    {
        $categoryIds = $this->getAllChildren()->push($this)->pluck('id');

        return Post::whereIn('category_id', $categoryIds)->exists();
    }
}
