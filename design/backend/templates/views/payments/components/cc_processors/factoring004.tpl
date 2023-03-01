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
    <label class="control-label" for="factoring004_delivery_token">{__("payments.factoring004.delivery_token")}:</label>
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
    <label class="control-label" for="factoring004_debug_mode">{__("payments.factoring004.debug_mode")}:</label>
    <div class="controls">
        <input {if isset($processor_params.factoring004_debug_mode)} checked {/if} type="checkbox" name="payment_data[processor_params][factoring004_debug_mode]" id="factoring004_debug_mode"/>
    </div>
</div>