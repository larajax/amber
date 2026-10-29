<?php

namespace App\Controllers\UiElementsController;

use Illuminate\Support\Sleep;
use Larajax\Classes\AjaxResponse;

/**
 * UiElementHandlers holds the AJAX handlers behind the UI element pages, which need no model.
 */
trait UiElementHandlers
{
    /**
     * onFlashMessage shows a flash message, as the handler for the plain AJAX button.
     */
    public function onFlashMessage(): AjaxResponse
    {
        return ajax()->flash('success', 'Hello from the server.');
    }

    /**
     * onConfirmedAction runs after the visitor accepts the confirmation prompt.
     */
    public function onConfirmedAction(): AjaxResponse
    {
        return ajax()->flash('success', 'The action ran after you confirmed it.');
    }

    /**
     * onArchiveItem reads the item number sent as request data.
     */
    public function onArchiveItem(): AjaxResponse
    {
        $itemNumber = (int) input('item');

        return ajax()->flash('success', "Item {$itemNumber} archived.");
    }

    /**
     * onLoadDialog renders the demo dialog at the size the popup button asked for.
     */
    public function onLoadDialog(): string
    {
        $size = input('size');

        if (! in_array($size, $this->getDialogSizes(), true)) {
            $size = 'default';
        }

        return view('ui_elements._dialog', ['size' => $size])->render();
    }

    /**
     * onSubmitDialog handles the dialog's submit button, which closes the popup when it succeeds.
     */
    public function onSubmitDialog(): AjaxResponse
    {
        Sleep::for(500)->milliseconds();

        return ajax()->flash('success', 'The dialog was submitted.');
    }

    /**
     * onDropdownAction runs the dropdown item named in the request data.
     */
    public function onDropdownAction(): AjaxResponse
    {
        $actions = [
            'duplicate' => 'Duplicated the project.',
            'archive' => 'Archived the project.',
        ];

        return ajax()->flash('success', $actions[input('action')] ?? 'Nothing to do.');
    }

    /**
     * onRefreshData stands in for a refresh, as the handler for the icon button and the plain hotkey.
     */
    public function onRefreshData(): AjaxResponse
    {
        return ajax()->flash('info', 'The data was refreshed.');
    }

    /**
     * onSaveDraft stands in for a save, as the handler for the hotkey buttons.
     */
    public function onSaveDraft(): AjaxResponse
    {
        return ajax()->flash('success', 'Draft saved.');
    }

    /**
     * onCalloutAction handles the button inside the danger callout.
     */
    public function onCalloutAction(): AjaxResponse
    {
        return ajax()->flash('info', 'Payment details are not part of this demo.');
    }

    /**
     * onSearchCities filters the city list by the search term and updates the results.
     */
    public function onSearchCities(): AjaxResponse
    {
        $term = trim((string) input('city_search'));

        $cities = collect($this->getCityNames())
            ->filter(fn (string $city) => $term === '' || str_contains(mb_strtolower($city), mb_strtolower($term)))
            ->values()
            ->all();

        return ajax()->update([
            '#uiCityResults' => view('ui_elements._city_results', [
                'cities' => $cities,
                'term' => $term,
            ]),
        ]);
    }

    /**
     * onSlowRequest sleeps briefly so the loader container has time to show.
     */
    public function onSlowRequest(): AjaxResponse
    {
        Sleep::for(1500)->milliseconds();

        return ajax()->flash('success', 'The slow request finished.');
    }

    /**
     * onSaveProfile stands in for a save, as the handler for the change monitor form.
     */
    public function onSaveProfile(): AjaxResponse
    {
        return ajax()->flash('success', 'Profile saved.');
    }

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
     * onRowAction handles the table row that runs an AJAX request.
     */
    public function onRowAction(): AjaxResponse
    {
        return ajax()->flash('info', 'The row ran an AJAX handler.');
    }

    /**
     * getDialogSizes returns the popup sizes the dialog demo accepts.
     *
     * @return array<int, string>
     */
    protected function getDialogSizes(): array
    {
        return ['tiny', 'small', 'default', 'large', 'huge'];
    }

    /**
     * getCityNames returns the cities the search input demo filters.
     *
     * @return array<int, string>
     */
    protected function getCityNames(): array
    {
        return [
            'Amsterdam',
            'Auckland',
            'Barcelona',
            'Berlin',
            'Brisbane',
            'Buenos Aires',
            'Cape Town',
            'Lisbon',
            'Melbourne',
            'Montreal',
            'Osaka',
            'Paris',
            'Perth',
            'Seoul',
            'Sydney',
            'Toronto',
            'Vancouver',
        ];
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
