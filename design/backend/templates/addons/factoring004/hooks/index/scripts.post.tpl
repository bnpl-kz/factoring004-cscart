{if $runtime.controller == "orders" || "order_management"}
    {script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-popup-overlay/2.1.5/jquery.popupoverlay.min.js"}

    <script>
        {foreach from=getFactoring004Translations() item="translate" key="langKey"}
            Tygh.tr('{$langKey}', '{$translate}');
        {/foreach}
    </script>

    {if $runtime.mode == "manage"}
        {script src="js/addons/factoring004/orders_manage_delivery.js"}
        {script src="js/addons/factoring004/orders_manage_bulk_change_status.js"}
        {script src="js/addons/factoring004/orders_manage_refund.js"}
    {/if}

    {if $runtime.mode == "details"}
        {script src="js/addons/factoring004/orders_details_delivery.js"}
        {script src="js/addons/factoring004/orders_details_refund.js"}
    {/if}

    {if $runtime.mode == "update"}
        {assign var=processorData value=fn_get_processor_data($cart.payment_id)}

        <script>
            window.__factoring004 = {
                orderId: '{$cart.order_id}',
                paymentId: '{$cart.payment_id}',
            };
        </script>

        {if $processorData.processor_script == "factoring004.php"}
            {script src="js/addons/factoring004/order_management_update_delivery.js"}
            {script src="js/addons/factoring004/order_management_update_refund.js"}
        {/if}
    {/if}
{/if}
