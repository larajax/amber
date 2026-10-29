<div class="ui-field-grid">
    <div class="form-field">
        <label class="form-label" for="uiPresetTitle">
            Title
        </label>
        <input type="text" class="form-control" id="uiPresetTitle" placeholder="My First Blog Post">
    </div>

    <div class="form-field">
        <label class="form-label" for="uiPresetSlug">
            Slug
        </label>
        <input type="text" class="form-control" id="uiPresetSlug" data-input-preset="#uiPresetTitle" data-input-preset-type="slug">
    </div>

    <div class="form-field">
        <label class="form-label" for="uiPresetUrl">
            URL
        </label>
        <input type="text" class="form-control" id="uiPresetUrl" data-input-preset="#uiPresetTitle" data-input-preset-type="url">
    </div>

    <div class="form-field">
        <label class="form-label" for="uiPresetCamel">
            Handle
        </label>
        <input type="text" class="form-control" id="uiPresetCamel" data-input-preset="#uiPresetTitle" data-input-preset-type="camel">
    </div>
</div>
