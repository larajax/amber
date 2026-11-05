<?php

namespace App\Models;

use Database\Factories\SampleLinkFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use October\Contracts\Database\SortableInterface;

/**
 * SampleLink is a link on a sample, stored as its own record so the repeater can edit a hasMany relation, and kept in the order the repeater saves.
 */
#[Fillable(['title', 'url'])]
class SampleLink extends Model implements SortableInterface
{
    /** @use HasFactory<SampleLinkFactory> */
    use HasFactory;

    /**
     * sample is the sample the link belongs to.
     */
    public function sample(): BelongsTo
    {
        return $this->belongsTo(Sample::class);
    }

    /**
     * getSortOrderColumn names the column the repeater writes each item's position to.
     */
    public function getSortOrderColumn(): string
    {
        return 'sort_order';
    }

    /**
     * setSortableOrder stores a new order for the given links, numbered from one unless the orders are supplied.
     *
     * @param  array<int, int|string>  $itemIds
     * @param  array<int, int>|bool|null  $itemOrders
     */
    public function setSortableOrder($itemIds, $itemOrders = null): void
    {
        if (!is_array($itemIds)) {
            return;
        }

        $itemIds = array_values($itemIds);
        $orders = is_array($itemOrders) && count($itemOrders) === count($itemIds)
            ? array_values($itemOrders)
            : range(1, count($itemIds));

        foreach ($itemIds as $index => $id) {
            static::query()->whereKey($id)->update(['sort_order' => $orders[$index]]);
        }
    }

    /**
     * booted places a new link after the others.
     */
    protected static function booted(): void
    {
        static::creating(function (SampleLink $link) {
            $link->sort_order ??= (int) static::query()->max('sort_order') + 1;
        });
    }
}
