<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use October\Contracts\Database\SortableInterface;

/**
 * User is a stock Eloquent model, shown in the showcase as the blog's authors beside the Rain models of the posts, whose SortableInterface contract lets a reorderable list store a manual order.
 */
#[Fillable([
    'name',
    'email',
    'email_verified_at',
    'password',
    'notes',
    'links',
    'is_mail_blocked',
    'is_two_factor_enabled',
    'primary_group_id',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements SortableInterface
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'links' => 'array',
            'is_mail_blocked' => 'boolean',
            'is_two_factor_enabled' => 'boolean',
        ];
    }

    /**
     * The primary group this user belongs to (belongs-to relation, rendered by
     * the "Primary Group" relation field on the form).
     */
    public function primary_group(): BelongsTo
    {
        return $this->belongsTo(UserGroup::class, 'primary_group_id');
    }

    /**
     * The secondary groups this user belongs to (many-to-many relation,
     * rendered by the "Secondary Groups" relation field on the form).
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(UserGroup::class, 'user_user_group');
    }

    /**
     * Filter users by email domain, used by the "Email Domain" scope on the Filter Options reference page.
     */
    public function scopeApplyDomain($query, $scope)
    {
        return $query->where('email', 'like', '%@'.$scope->value);
    }

    /**
     * Search users by the start of their name or email, used as a record finder's searchScope on the Record Finder reference page.
     *
     * @param  array<int, string>  $columns
     */
    public function scopeSearchNameOrEmail(\Illuminate\Database\Eloquent\Builder $query, string $term, array $columns = []): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where(function ($query) use ($term) {
            $query->where('name', 'like', $term.'%')->orWhere('email', 'like', $term.'%');
        });
    }

    /**
     * Options for the "Name" group filter scope.
     *
     * @return array<string, string>
     */
    public static function getNameGroupOptions(): array
    {
        return static::query()->orderBy('name')->pluck('name', 'name')->all();
    }

    /**
     * The posts this user wrote, a has-many relation to a Rain model counted by the authors list.
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'author_id');
    }

    /**
     * Limit the query to users who wrote at least one post, used by the author filter on the posts list.
     */
    public function scopeHasPosts(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->has('posts');
    }

    /**
     * Persist a new record ordering. Called by the ListStructure widget when
     * rows are dragged into a new order.
     *
     * @param  array<int, int|string>  $itemIds
     * @param  array<int, int>|null  $itemOrders
     */
    public function setSortableOrder($itemIds, $itemOrders = null): void
    {
        if (! is_array($itemIds)) {
            return;
        }

        foreach (array_values($itemIds) as $index => $id) {
            static::withoutEvents(function () use ($id, $index) {
                static::query()->whereKey($id)->update(['sort_order' => $index + 1]);
            });
        }
    }
}
