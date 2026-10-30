<div class="reference-drag-strip">
    <div data-control="drag-scroll">
        @foreach (range(1, 20) as $week)
            <span class="badge badge-subtle theme-secondary">
                Week {{ $week }}
            </span>
        @endforeach
    </div>
</div>
