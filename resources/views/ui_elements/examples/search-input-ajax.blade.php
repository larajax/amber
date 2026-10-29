<div class="ui-narrow mb-4">
    {{ Ui::searchInput(name: 'city_search', placeholder: 'Search cities...', handler: 'onSearchCities') }}
</div>

<div id="uiCityResults">
    @include('ui_elements._city_results', ['cities' => [], 'term' => ''])
</div>
