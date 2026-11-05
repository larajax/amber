<?php

namespace App\Models;

use Database\Factories\CommentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Comment is a reader comment on a post, counted by the posts list and soft deleted for the trashed filter.
 */
#[Fillable(['author', 'content'])]
class Comment extends Model
{
    /** @use HasFactory<CommentFactory> */
    use HasFactory;
    use SoftDeletes;

    /**
     * post is the post the comment was left on.
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}
