{assign var=minsum value=6000}
{assign var=maxsum value=200000}

<div class="litecheckout__group">
    <div class="litecheckout__field">
        <div {if !empty($cart.payment_method_data.processor_params.factoring004_offer_file)} style="margin-bottom: 20px;" {/if}>
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

        {if !empty($cart.payment_method_data.processor_params.factoring004_offer_file)}
            <div class="litecheckout__terms" id="litecheckout_terms">
                <div class="ty-control-group ty-checkout__terms">
                    <div class="cm-field-container">
                        <label for="factoring004-agreement" class="cm-check-agreement">
                            <input type="checkbox" id="factoring004-agreement" class="cm-agreement checkbox">{__("payments.factoring004.offer_file_checkbox")}
                            <a target="_blank" href="/images/{$cart.payment_method_data.processor_params.factoring004_offer_file}">{__("payments.factoring004.name")}</a>
                        </label>
                    </div>
                </div>
            </div>
        {/if}
    </div>
</div>

<script>
    (function (_, $) {
        const totalSum = parseInt('{$cart.total}');
        const maxSum = parseInt('{$maxsum}');
        const minSum = parseInt('{$minsum}');

        let submitButton = $('.litecheckout__submit-btn');

        if (totalSum > maxSum || totalSum < minSum) submitButton.prop('disabled',true).css('opacity',0.5)

    }(Tygh, Tygh.$))
</script>