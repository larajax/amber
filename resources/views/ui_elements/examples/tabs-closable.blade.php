<div id="uiClosableTabs" class="control-tabs primary-tabs" data-control="tab" data-closable>
    <ul class="nav nav-tabs">
        <li class="active">
            <a href="javascript:;">
                index.html
            </a>
        </li>
        <li>
            <a href="javascript:;">
                app.css
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active">
            <p class="pt-4 mb-0">
                Close a tab with its cross, or with a middle click.
            </p>
        </div>
        <div class="tab-pane">
            <p class="pt-4 mb-0">
                The stylesheet tab.
            </p>
        </div>
    </div>
</div>

<button
    type="button"
    class="btn-solid theme-secondary mt-4"
    onclick="jax.fetchControl(document.getElementById('uiClosableTabs'), 'tab').addTab('untitled.txt', '<p class=&quot;pt-4 mb-0&quot;>Added from JavaScript.</p>')">
    Add a tab
</button>
