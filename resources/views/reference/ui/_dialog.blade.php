<form data-request="onSubmitDialog" data-request-flash data-popup-load-indicator>
    <div class="dialog-header">
        <h4 class="dialog-title">
            {{ $title }}
        </h4>
        <button type="button" class="btn-close" data-dismiss="popup" aria-label="Close"></button>
    </div>

    <div class="dialog-body">
        <p>
            The handler returned this markup. The button's <code>dataSize</code> sets the width, and Submit hides the dialog behind a loading indicator until its own request succeeds.
        </p>

        <div class="form-field mb-5">
            <label class="form-label" for="uiDialogNote">
                Note
            </label>
            <input type="text" class="form-control" id="uiDialogNote" name="note" placeholder="Anything you like">
        </div>
    </div>

    <div class="dialog-footer">
        <button type="submit" class="btn-solid theme-primary">
            Submit
        </button>
        <button type="button" class="btn-solid theme-secondary" data-dismiss="popup">
            Cancel
        </button>
        {{ Ui::popupButton(
            label: 'Open another',
            handler: 'onLoadDialog',
            class: 'btn-link ms-auto',
            requestData: ['size' => 'small'],
            dataSize: 'small'
        ) }}
    </div>
</form>
