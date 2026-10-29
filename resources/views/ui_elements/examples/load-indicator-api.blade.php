<div id="uiLoadTarget" class="loading-indicator-container ui-load-box mb-4">
    <p class="mb-0">
        The indicator covers this box while it shows.
    </p>
</div>

<button type="button" class="btn-solid theme-secondary" id="uiLoadButton">
    Show for two seconds
</button>

<script type="module">
    import { LoadIndicator } from '{{ asset('vendor/larajax/ui/index.js') }}';

    document.getElementById('uiLoadButton').addEventListener('click', () => {
        const box = document.getElementById('uiLoadTarget');

        LoadIndicator.show(box, { text: 'Saving...', opaque: true });
        setTimeout(() => LoadIndicator.hide(box), 2000);
    });
</script>
