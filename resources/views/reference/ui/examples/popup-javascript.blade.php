<button type="button" class="btn-solid theme-secondary" id="uiTemplatePopupButton">
    Open from a template
</button>

<template id="uiTemplatePopup">
    <div class="dialog-header">
        <h4 class="dialog-title">
            From a template
        </h4>
        <button type="button" class="btn-close" data-dismiss="popup" aria-label="Close"></button>
    </div>
    <div class="dialog-body dialog-no-footer">
        <p class="mb-0">
            This content was already on the page, so no request ran.
        </p>
    </div>
</template>

<script>
    document.getElementById('uiTemplatePopupButton').addEventListener('click', () => {
        jax.popup.open({
            content: document.getElementById('uiTemplatePopup').innerHTML,
            size: 'small'
        });
    });
</script>
