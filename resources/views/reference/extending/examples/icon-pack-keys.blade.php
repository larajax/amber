<div class="ui-icon-grid">
    @foreach (app('larajax.ui.icons')->icons() as $iconKey => $iconClass)
        <div class="ui-icon-sample">
            <i class="{{ $iconClass }}" aria-hidden="true"></i>
            <code>
                {{ $iconKey }}
            </code>
        </div>
    @endforeach
</div>
