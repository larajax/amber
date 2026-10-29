<form data-request="onCreateQuickPost" data-request-flash data-popup-load-indicator>
    <div class="dialog-header">
        <h4 class="dialog-title">
            Quick Post
        </h4>
        <button type="button" class="btn-close" data-dismiss="popup" aria-label="Close"></button>
    </div>

    <div class="dialog-body">
        {{ $formWidget }}
    </div>

    <div class="dialog-footer">
        <button type="submit" class="btn-solid theme-primary">
            Create Post
        </button>
        <button type="button" class="btn-solid theme-secondary" data-dismiss="popup">
            Cancel
        </button>
    </div>
</form>
