<?php

namespace App\Models;

use Database\Factories\CommentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use October\Rain\Database\Model;
use October\Rain\Database\Traits\SoftDelete;

/**
 * Comment is a reader comment on a post, counted by the posts list and soft deleted for the trashed filter.
 */
#[Fillable(['author', 'content'])]
class Comment extends Model
{
    /** @use HasFactory<CommentFactory> */
    use HasFactory;
    use SoftDelete;

    /**
     * @var array<string, mixed> belongsTo relations
     */
    public $belongsTo = [
        'post' => Post::class,
    ];
}
