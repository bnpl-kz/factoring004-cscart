{if $runtime.controller == "orders"}
    {script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-popup-overlay/2.1.5/jquery.popupoverlay.min.js"}

    {if $runtime.mode == "manage"}
        {script src="js/addons/factoring004/orders_manage_delivery.js"}
        {script src="js/addons/factoring004/orders_manage_bulk_change_status.js"}
    {/if}
{/if}
