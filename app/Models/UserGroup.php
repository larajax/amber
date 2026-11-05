<?php

namespace App\Models;

use Database\Factories\UserGroupFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * UserGroup is a group the authors belong to, picked by the relation fields of the author form and the reference pages.
 */
#[Fillable(['name', 'code', 'description'])]
class UserGroup extends Model
{
    /** @use HasFactory<UserGroupFactory> */
    use HasFactory;

    /**
     * users are the members holding this group as one of their secondary groups.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_user_group');
    }

    // #region docs-without-guest
    /**
     * Exclude the built-in "guest" group. Used by the relation fields so a user
     * cannot be assigned to the guest pseudo-group.
     */
    public function scopeWithoutGuest($query)
    {
        return $query->where('code', '<>', 'guest');
    }
    // #endregion docs-without-guest
}
