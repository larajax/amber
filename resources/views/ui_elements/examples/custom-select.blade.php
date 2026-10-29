<div class="ui-field-grid">
    <div class="form-field">
        <label class="form-label" for="uiSelectStatus">
            Status
        </label>
        <select class="form-control custom-select" id="uiSelectStatus" name="status">
            <option value="draft">
                Draft
            </option>
            <option value="review" selected>
                In review
            </option>
            <option value="published">
                Published
            </option>
        </select>
    </div>

    <div class="form-field">
        <label class="form-label" for="uiSelectPriority">
            Priority, no search
        </label>
        <select class="form-control custom-select select-no-search" id="uiSelectPriority" name="priority">
            <option value="low">
                Low
            </option>
            <option value="normal" selected>
                Normal
            </option>
            <option value="high">
                High
            </option>
        </select>
    </div>

    <div class="form-field">
        <label class="form-label" for="uiSelectTeam">
            Team, grouped
        </label>
        <select class="form-control custom-select" id="uiSelectTeam" name="team">
            <optgroup label="Product">
                <option value="design">
                    Design
                </option>
                <option value="engineering">
                    Engineering
                </option>
            </optgroup>
            <optgroup label="Business">
                <option value="sales">
                    Sales
                </option>
                <option value="support">
                    Support
                </option>
            </optgroup>
        </select>
    </div>
</div>
