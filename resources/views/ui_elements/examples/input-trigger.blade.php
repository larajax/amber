<div class="ui-field-grid">
    <div>
        <div class="form-field mb-3">
            <input type="checkbox" class="check" id="uiTriggerGift">
            <label for="uiTriggerGift">
                Add a gift message
            </label>
        </div>
        <textarea
            class="form-control"
            placeholder="Gift message"
            data-trigger="#uiTriggerGift"
            data-trigger-action="show"
            data-trigger-condition="checked"></textarea>
    </div>

    <div>
        <div class="form-field mb-3">
            <input type="checkbox" class="check" id="uiTriggerTerms">
            <label for="uiTriggerTerms">
                I accept the terms
            </label>
        </div>
        <button
            type="button"
            class="btn-solid theme-primary"
            data-trigger="#uiTriggerTerms"
            data-trigger-action="enable"
            data-trigger-condition="checked">
            Continue
        </button>
    </div>

    <div>
        <div class="form-field mb-3">
            <label class="form-label" for="uiTriggerShipping">
                Delivery
            </label>
            <select class="form-control" id="uiTriggerShipping">
                <option value="standard">
                    Standard
                </option>
                <option value="express">
                    Express
                </option>
                <option value="pickup">
                    Pick up in store
                </option>
            </select>
        </div>
        <p
            class="fg-3 mb-0"
            data-trigger="#uiTriggerShipping"
            data-trigger-action="hide"
            data-trigger-condition="value[standard]">
            Express and pick up orders are ready the next day.
        </p>
    </div>
</div>
