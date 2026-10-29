<button type="button" class="btn-solid theme-secondary" data-control="popover" data-content-from="#uiShippingPopover" data-placement="top">
    Top
</button>
<button type="button" class="btn-solid theme-secondary" data-control="popover" data-content-from="#uiShippingPopover" data-placement="bottom">
    Bottom
</button>
<button type="button" class="btn-solid theme-secondary" data-control="popover" data-content-from="#uiShippingPopover" data-placement="left">
    Left
</button>
<button type="button" class="btn-solid theme-secondary" data-control="popover" data-content-from="#uiShippingPopover" data-placement="right">
    Right
</button>
<button type="button" class="btn-solid theme-primary" data-control="popover" data-content-from="#uiShippingPopover" data-modal="true" data-width="320">
    Modal, 320px wide
</button>

<template id="uiShippingPopover">
    <div class="popover-head">
        <h3>
            Shipping estimate
        </h3>
        <p>
            From the main warehouse
        </p>
        <button type="button" class="btn-close" data-dismiss="popover" aria-label="Close"></button>
    </div>
    <div class="popover-body">
        Standard delivery takes 3 to 5 business days.
    </div>
</template>
