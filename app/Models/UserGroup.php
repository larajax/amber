<?php

namespace App\Models;

use Database\Factories\UserGroupFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use October\Rain\Database\Model;

/**
 * UserGroup is a group the authors belong to, an October Rain model with its relations declared in the array style.
 */
#[Fillable(['name', 'code', 'description'])]
class UserGroup extends Model
{
    /** @use HasFactory<UserGroupFactory> */
    use HasFactory;

    /**
     * @var array belongsToMany relations, the users belonging to this group (secondary members)
     */
    public $belongsToMany = [
        'users' => [User::class, 'table' => 'user_user_group'],
    ];

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
