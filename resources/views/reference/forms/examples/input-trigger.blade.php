<div class="w-100">
    <div class="form-field">
        <input class="check" type="checkbox" id="triggerAgreeTerms" />
        <div class="form-field-content">
            <label for="triggerAgreeTerms">
                I have read the terms
            </label>
        </div>
    </div>

    <p
        class="fg-3 mt-3"
        data-trigger="#triggerAgreeTerms"
        data-trigger-action="hide"
        data-trigger-condition="checked">
        Tick the box to continue.
    </p>

    <button
        type="button"
        class="btn-solid theme-primary mt-3"
        data-trigger="#triggerAgreeTerms"
        data-trigger-action="enable"
        data-trigger-condition="checked"
        disabled>
        Continue
    </button>
</div>
