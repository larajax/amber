<?php

namespace App\Models;

use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use October\Rain\Database\Model;
use October\Rain\Database\Traits\NestedTree;
use October\Rain\Database\Traits\Validation;

/**
 * Category is a post category stored as a nested set tree, so the list structure can display, nest and reorder it.
 */
#[Fillable(['name', 'slug', 'description', 'parent'])]
class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory;
    use NestedTree;
    use Validation;

    /**
     * @var array<string, mixed> rules for validation, where the unique rule ignores the record being updated.
     */
    public $rules = [
        'name' => 'required',
        'slug' => ['required', 'regex:/^[a-z0-9-]+$/', 'unique:categories'],
    ];

    /**
     * @var array<string, mixed> hasMany relations, where the nested tree trait adds the children relation.
     */
    public $hasMany = [
        'posts' => Post::class,
    ];

    /**
     * scopeHasPosts limits the query to categories holding at least one post, used by the posts category filter.
     */
    public function scopeHasPosts($query)
    {
        return $query->has('posts');
    }

    /**
     * hasPostsInTree checks whether this category or any category below it holds a post.
     */
    public function hasPostsInTree(): bool
    {
        $categoryIds = $this->getAllChildrenAndSelf()->pluck('id');

        return Post::whereIn('category_id', $categoryIds)->exists();
    }
}
