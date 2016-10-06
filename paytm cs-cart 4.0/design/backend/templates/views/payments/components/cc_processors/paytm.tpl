<div class="form-field">
	<label for="merchant_i">{__("paytm_merchant_id")}:</label>
	<input type="text" name="payment_data[processor_params][merchant_id]" id="merchant_id" value="{$processor_params.merchant_id}" class="input-text" />
</div>

<div class="form-field">
	<label for="secret_key">{__("paytm_secret_key")}:</label>
	<input type="text" name="payment_data[processor_params][secret_key]" id="secret_key" value="{$processor_params.secret_key}" class="input-text" />
</div>

<div class="form-field">
	<label for="industry_type">{__("paytm_industry_type")}:</label>
	<input type="text" name="payment_data[processor_params][industry_type]" id="industry_type" value="{$processor_params.industry_type}" class="input-text" />
</div>

<div class="form-field">
	<label for="website_name">{__("paytm_website_name")}:</label>
	<input type="text" name="payment_data[processor_params][website_name]" id="website_name" value="{$processor_params.website_name}" class="input-text" />
</div>

<div class="form-field">
	<label for="channel_id">{__("paytm_channel_id")}:</label>
	<input type="text" name="payment_data[processor_params][channel_id]" id="channel_id" value="{$processor_params.channel_id}" class="input-text" />
</div>

<div class="form-field">
	<label for="callback">{__("paytm_callback")}:</label>
	<select name="payment_data[processor_params][callback]" id="callback">
		<option value="yes" {if $processor_params.callback == "yes"}selected="selected"{/if}>{__("paytm_callback_yes")}</option>
		<option value="no" {if $processor_params.callback == "no"}selected="selected"{/if}>{__("paytm_callback_no")}</option>
	</select>
</div>


<div class="form-field">
	<label for="transaction_mode">{__("paytm_transaction_mode")}:</label>
	<select name="payment_data[processor_params][transaction_mode]" id="transaction_mode">
		<option value="test" {if $processor_params.transaction_mode == "test"}selected="selected"{/if}>{__("paytm_test")}</option>
		<option value="live" {if $processor_params.transaction_mode == "live"}selected="selected"{/if}>{__("paytm_live")}</option>
	</select>
</div>
<div class="form-field">
	<label for="log_params">{__("paytm_log_params")}:</label>
	<select name="payment_data[processor_params][log_params]" id="log_params">
		<option value="yes" {if $processor_params.log_params == "yes"}selected="selected"{/if}>{__("paytm_yes")}</option>
		<option value="no" {if $processor_params.log_params == "no"}selected="selected"{/if}>{__("paytm_no")}</option>
	</select>
</div>