<div class="ui-field-grid w-100">
    <div>
        <label class="form-label" for="presetClassTitle">
            Class title
        </label>
        <input type="text" id="presetClassTitle" class="form-control" placeholder="Type a title, such as Order Line Item" />
    </div>

    <div>
        <label class="form-label" for="presetClassName">
            Class name
        </label>
        <input
            type="text"
            id="presetClassName"
            class="form-control"
            data-input-preset="#presetClassTitle"
            data-input-preset-type="namespace" />
    </div>

    <div>
        <label class="form-label" for="presetTableName">
            Table name
        </label>
        <input
            type="text"
            id="presetTableName"
            class="form-control"
            data-input-preset="#presetClassTitle"
            data-input-preset-type="snake"
            data-input-preset-remove-words="false" />
    </div>
</div>
