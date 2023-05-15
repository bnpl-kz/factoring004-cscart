{assign var=minsum value=6000}
{assign var=maxsum value=200000}
<div class="litecheckout__group">
    <div class="litecheckout__field">
        <div style="width: 100%; {if !empty($cart.payment_method_data.processor_params.factoring004_offer_file)} margin-bottom: 20px; {/if}" >
        {if $cart.total < $minsum}
            <span style="color: brown; font-weight: bold">
                {__("payments.factoring004.condition_sum_min")} {$minsum - $cart.total} {__("payments.factoring004.currency")}
            </span>
        {/if}
        {if $cart.total > $maxsum}
            <span style="color: brown; font-weight: bold">
                {__("payments.factoring004.condition_sum_max")} {$maxsum - $cart.total} {__("payments.factoring004.currency")}
            </span>
        {/if}
        </div>
    </div>
</div>
<script>
    (function (_, $) {
        $(document).ajaxStop(function () {
            const totalSum = parseInt('{$cart.total}');
            const maxSum = parseInt('{$maxsum}');
            const minSum = parseInt('{$minsum}');

            let submitButton = $('.litecheckout__submit-btn');

            if (totalSum > maxSum || totalSum < minSum) submitButton.prop('disabled',true).css('opacity',0.5)
        })
    }(Tygh, Tygh.$))
</script>