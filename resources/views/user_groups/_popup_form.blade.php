<form data-request="onSavePopupForm" data-request-flash data-popup-load-indicator>
    <input type="hidden" name="form_record_id" value="{{ $group->id }}" />

    <div class="modal-header">
        <h4 class="modal-title">
            Edit Group
        </h4>
        <button type="button" class="btn-close" data-dismiss="popup"></button>
    </div>

    <div class="modal-body">
        {{ $formWidget }}
    </div>

    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">
            Save
        </button>
        <button type="button" class="btn btn-default" data-dismiss="popup">
            Cancel
        </button>
    </div>
</form>
