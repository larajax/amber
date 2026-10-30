<div class="ui-icon-grid">
    @foreach (app(\Larajax\Ui\Classes\IconManager::class)->icons() as $iconKey => $iconClass)
        <div class="ui-icon-sample">
            {!! Ui::icon($iconKey) !!}
            <code>
                {{ $iconKey }}
            </code>
        </div>
    @endforeach
</div>
