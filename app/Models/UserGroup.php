<?php

namespace App\Models;

use Database\Factories\UserGroupFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * User group model.
 *
 * Groups a user can belong to, used to demonstrate the relation field types on
 * the user form and as a second resource with its own list and form.
 */
#[Fillable(['name', 'code', 'description'])]
class UserGroup extends Model
{
    /** @use HasFactory<UserGroupFactory> */
    use HasFactory;

    /**
     * Users belonging to this group (secondary members).
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_user_group');
    }

    /**
     * Exclude the built-in "guest" group. Used by the relation fields so a user
     * cannot be assigned to the guest pseudo-group.
     */
    public function scopeWithoutGuest($query)
    {
        return $query->where('code', '<>', 'guest');
    }
}
