<?php

namespace App\Models;

use Database\Factories\SampleLinkFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use October\Rain\Database\Model;
use October\Rain\Database\Traits\Sortable;

/**
 * SampleLink is a link on a sample, stored as its own record so the repeater can edit a hasMany relation.
 */
#[Fillable(['title', 'url'])]
class SampleLink extends Model
{
    /** @use HasFactory<SampleLinkFactory> */
    use HasFactory;
    use Sortable;

    /**
     * @var array<string, mixed> belongsTo relations
     */
    public $belongsTo = [
        'sample' => Sample::class,
    ];
}
