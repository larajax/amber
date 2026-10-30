<?php

namespace App\Controllers\Reference\FormsController;

/**
 * FieldsHandlers holds the AJAX handlers of the Field Types page.
 */
trait FieldsHandlers
{
    /**
     * onSearchTimezones returns the timezones matching the typed term, in the format the AJAX dropdown of the timezone partial field expects.
     *
     * @return array{results: array<int, array{id: string, text: string}>}
     */
    public function onSearchTimezones(): array
    {
        $term = strtolower(trim((string) input('term')));

        $results = collect(timezone_identifiers_list())
            ->filter(fn (string $zone) => $term === '' || str_contains(strtolower($zone), $term))
            ->take(20)
            ->map(fn (string $zone) => ['id' => $zone, 'text' => str_replace('_', ' ', $zone)])
            ->values()
            ->all();

        return ['results' => $results];
    }
}
