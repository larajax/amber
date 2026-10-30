<div
    id="uiModifiedTabs"
    class="control-tabs secondary-tabs"
    data-control="tab"
    data-closable
    data-close-confirmation="This file has unsaved changes. Close it anyway?">
    <ul class="nav nav-tabs">
        <li class="active">
            <a href="javascript:;">
                notes.md
            </a>
        </li>
        <li>
            <a href="javascript:;">
                todo.md
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active">
            <textarea class="form-control" rows="3" placeholder="Type here to mark the tab as modified"></textarea>
        </div>
        <div class="tab-pane">
            <textarea class="form-control" rows="3" placeholder="Type here to mark the tab as modified"></textarea>
        </div>
    </div>
</div>

<script>
    document.getElementById('uiModifiedTabs').addEventListener('input', (ev) => {
        ev.target.dispatchEvent(new CustomEvent('tab:modified', { bubbles: true }));
    });
</script>
