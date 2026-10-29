<form data-change-monitor class="ui-narrow">
    <p class="ui-change-status">
        <span class="badge is-saved">
            <i class="ph ph-check"></i>
            No unsaved changes
        </span>
        <span class="badge theme-warning is-changed">
            <i class="ph ph-pencil-simple"></i>
            Unsaved changes
        </span>
    </p>

    <div class="form-field mb-4">
        <label class="form-label" for="uiProfileName">
            Display name
        </label>
        <input type="text" class="form-control" id="uiProfileName" name="display_name" value="Ada Lovelace">
    </div>

    <div class="form-field mb-4">
        <input type="checkbox" class="check" id="uiProfilePublic" name="is_public" value="1" checked>
        <label for="uiProfilePublic">
            Show my profile publicly
        </label>
    </div>

    {{ Ui::ajaxButton(
        label: 'Save',
        handler: 'onSaveProfile',
        primary: true,
        hotkey: ['ctrl+s', 'cmd+s'],
        dataRequestFlash: true,
        dataChangeMonitorCommit: true
    ) }}
</form>
