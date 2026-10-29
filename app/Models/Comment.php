<?php

namespace App\Models;

use Database\Factories\CommentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use October\Rain\Database\Model;

/**
 * Comment is a reader comment on a post, counted by the posts list.
 */
#[Fillable(['author', 'content'])]
class Comment extends Model
{
    /** @use HasFactory<CommentFactory> */
    use HasFactory;

    /**
     * @var array<string, mixed> belongsTo relations
     */
    public $belongsTo = [
        'post' => Post::class,
    ];
}
