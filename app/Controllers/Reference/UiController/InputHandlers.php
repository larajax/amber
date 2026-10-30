<?php

namespace App\Controllers\Reference\UiController;

/**
 * InputHandlers holds the AJAX handlers behind the Inputs page.
 */
trait InputHandlers
{
    /**
     * onGetCountryOptions returns the countries matching the search term in the custom select's result format.
     *
     * @return array{results: array<int, array{id: string, text: string}>}
     */
    public function onGetCountryOptions(): array
    {
        $term = mb_strtolower(trim((string) input('term')));

        $results = collect($this->getCountryNames())
            ->filter(fn (string $name) => $term === '' || str_contains(mb_strtolower($name), $term))
            ->map(fn (string $name, string $code) => ['id' => $code, 'text' => $name])
            ->values()
            ->all();

        return ['results' => $results];
    }

    /**
     * getCountryNames returns the countries the custom select demo searches, keyed by code.
     *
     * @return array<string, string>
     */
    protected function getCountryNames(): array
    {
        return [
            'ar' => 'Argentina',
            'au' => 'Australia',
            'at' => 'Austria',
            'be' => 'Belgium',
            'br' => 'Brazil',
            'ca' => 'Canada',
            'dk' => 'Denmark',
            'fr' => 'France',
            'de' => 'Germany',
            'jp' => 'Japan',
            'nl' => 'Netherlands',
            'nz' => 'New Zealand',
            'pt' => 'Portugal',
            'kr' => 'South Korea',
            'es' => 'Spain',
            'za' => 'South Africa',
        ];
    }
}
