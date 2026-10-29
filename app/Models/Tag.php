<?php

namespace App\Models;

use Database\Factories\TagFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use October\Rain\Database\Model;

/**
 * Tag labels posts through a many-to-many relation, created on the fly by the post form's tag list.
 */
#[Fillable(['name'])]
class Tag extends Model
{
    /** @use HasFactory<TagFactory> */
    use HasFactory;

    /**
     * @var array<string, mixed> belongsToMany relations
     */
    public $belongsToMany = [
        'posts' => [Post::class, 'table' => 'post_tag'],
    ];
}
