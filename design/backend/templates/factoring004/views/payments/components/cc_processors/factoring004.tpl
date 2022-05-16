<div class="control-group">
    <label class="control-label" for="factoring004_api_host">{__("payments.factoring004.api_host")}:</label>
    <div class="controls">
        <input required style="width: 80% !important;" type="text" name="payment_data[processor_params][factoring004_api_host]" id="factoring004_api_host" value="{$processor_params.factoring004_api_host}"/>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="factoring004_preapp_token">{__("payments.factoring004.preapp_token")}:</label>
    <div class="controls">
        <input required style="width: 80% !important;" type="text" name="payment_data[processor_params][factoring004_preapp_token]" id="factoring004_preapp_token" value="{$processor_params.factoring004_preapp_token}"/>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="factoring004_delivery_token">{__("payments.factoring004.preapp_token")}:</label>
    <div class="controls">
        <input required style="width: 80% !important;" type="text" name="payment_data[processor_params][factoring004_delivery_token]" id="factoring004_delivery_token" value="{$processor_params.factoring004_delivery_token}"/>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="factoring004_partner_name">{__("payments.factoring004.partner_name")}:</label>
    <div class="controls">
        <input required style="width: 80% !important;" type="text" name="payment_data[processor_params][factoring004_partner_name]" id="factoring004_partner_name" value="{$processor_params.factoring004_partner_name}"/>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="factoring004_partner_code">{__("payments.factoring004.partner_code")}:</label>
    <div class="controls">
        <input required style="width: 80% !important;" type="text" name="payment_data[processor_params][factoring004_partner_code]" id="factoring004_partner_code" value="{$processor_params.factoring004_partner_code}"/>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="factoring004_point_code">{__("payments.factoring004.point_code")}:</label>
    <div class="controls">
        <input required style="width: 80% !important;" type="text" name="payment_data[processor_params][factoring004_point_code]" id="factoring004_point_code" value="{$processor_params.factoring004_point_code}"/>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="factoring004_partner_email">{__("payments.factoring004.partner_email")}:</label>
    <div class="controls">
        <input style="width: 80% !important;" type="text" name="payment_data[processor_params][factoring004_partner_email]" id="factoring004_partner_email" value="{$processor_params.factoring004_partner_email}"/>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="factoring004_partner_website">{__("payments.factoring004.partner_website")}:</label>
    <div class="controls">
        <input style="width: 80% !important;" type="text" name="payment_data[processor_params][factoring004_partner_website]" id="factoring004_partner_website" value="{$processor_params.factoring004_partner_website}"/>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="factoring004_delivery_method">{__("payments.factoring004.delivery_method")}:</label>
    {foreach from=getShippings() item="n" key="k"}
        <div class="controls">
            <label for="{$k}">
                <input {foreach from=$processor_params.factoring004_delivery_methods item="i" key="j"} {if $j === $k} checked  {/if}  {/foreach}
                        type="checkbox" name="payment_data[processor_params][factoring004_delivery_methods][{$k}]" id="{$k}"/>
                {$n}
            </label>
        </div>
    {/foreach}
</div>

<div class="control-group">
    <label class="control-label" for="factoring004_offer_file">{__("payments.factoring004.offer_file")}:</label>
    {if empty($processor_params.factoring004_offer_file) }
        <div class="controls">
            <button id="factoring004-agreement-button" class="btn btn-primary" type="button">{__("payments.factoring004.select_file")}</button>
            <input style="display:none;" type="file" name="payment_data[processor_params][factoring004_offer_file]" id="factoring004_offer_file"/>
            <span style="display:block;">{__("payments.factoring004.offer_file_help")}</span>
        </div>
    {else}
        <div class="controls">
            <a target="_blank" href="/images/{$processor_params.factoring004_offer_file}" class="btn btn-success agreement-link">{__("payments.factoring004.offer_file_show")}</a>
            <button id="factoring004-agreement-file-remove" type="button" data-value="{$processor_params.factoring004_offer_file}" class="btn btn-primary">{__("payments.factoring004.offer_file_delete")}</button>
        </div>
    {/if}
</div>

<input class="factoring004_offer_file" type="hidden" {if !empty($processor_params.factoring004_offer_file)} value="{$processor_params.factoring004_offer_file}" {else} value="" {/if} name="payment_data[processor_params][factoring004_offer_file]">

<div class="control-group">
    <label class="control-label" for="factoring004_debug_mode">{__("payments.factoring004.debug_mode")}:</label>
    <div class="controls">
        <input {if isset($processor_params.factoring004_debug_mode)} checked {/if} type="checkbox" name="payment_data[processor_params][factoring004_debug_mode]" id="factoring004_debug_mode"/>
    </div>
</div>

<script>
    (function(_, $) {
        $(document).on('click','#factoring004-agreement-button', function (e) {
            let inputFile = $('#factoring004_offer_file')
            inputFile.click()
            inputFile.change(function () {
                let file = inputFile.prop("files")[0];
                let form_data = new FormData();
                form_data.append("file",file);
                $.ajax({
                    url : fn_url('factoring004.upload&security_hash=' + _.security_hash),
                    type : 'POST',
                    data : form_data,
                    dataType: "json",
                    processData: false,
                    contentType: false,
                    beforeSend: function () {
                        $('#factoring004-agreement-button').prop('disabled',true);
                    },
                    complete: function (data) {
                        alert(data.responseJSON.message)
                    },
                    success: function(res) {
                        if (res.success) {
                            $('.factoring004_offer_file').val(res.fileName)
                        } else {
                            $('#factoring004-agreement-button').prop('disabled',false);
                        }
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText)
                    }
                })

            })
        })

        $(document).on('click','#factoring004-agreement-file-remove',function (e) {
            let filename = $(e.target).data('value');
            let form_data = new FormData();
            form_data.append("filename",filename);
            $.ajax({
                url: fn_url('factoring004.remove&security_hash=' + _.security_hash),
                type: 'POST',
                data: form_data,
                dataType: "json",
                processData: false,
                contentType: false,
                beforeSend: function () {
                    $('#factoring004-agreement-file-remove').prop('disabled',true);
                },
                complete: function (data) {
                    alert(data.responseJSON.message)
                },
                success: function(res) {
                    if (res.success) {
                        $('.factoring004_offer_file').val('')
                        $('.agreement-link').removeAttr('href')
                    } else {
                        $('#factoring004-agreement-file-remove').prop('disabled',false);
                    }
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText)
                }
            })
        })

    }(Tygh, Tygh.$))
</script>