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
	<label for="custom_callbackurl">{__("paytm_custom_callbackurl")}:</label>
	<input type="text" name="payment_data[processor_params][custom_callbackurl]" id="custom_callbackurl" value="{$processor_params.custom_callbackurl}" class="input-text" />
</div>

<div class="form-field">
	<label for="transaction_url">{__("paytm_transaction_url")}:</label>
	<input type="text" name="payment_data[processor_params][transaction_url]" id="transaction_url" value="{$processor_params.transaction_url}" class="input-text" />
</div>
<div class="form-field">
	<label for="transaction_status_url">{__("paytm_transaction_status_url")}:</label>
	<input type="text" name="payment_data[processor_params][transaction_status_url]" id="transaction_status_url" value="{$processor_params.transaction_status_url}" class="input-text" />
</div>
<div class="form-field">
	<label for="promo_code_view">{__("paytm_promo_code_view")}:</label>
	<select name="payment_data[processor_params][promo_code_view]" id="promo_code_view">
		<option value="no" {if $processor_params.promo_code_view == "no"}selected="selected"{/if}>{__("paytm_promo_code_view_no")}</option>
		<option value="yes" {if $processor_params.promo_code_view == "yes"}selected="selected"{/if}>{__("paytm_promo_code_view_yes")}</option>
	</select>
</div>
<div class="form-field">
	<label for="promo_code_view">{__("paytm_promo_code_local_validation")}:</label>
	<select name="payment_data[processor_params][promo_local_validation]" id="promo_code_view">
		<option value="no" {if $processor_params.promo_local_validation == "no"}selected="selected"{/if}>{__("paytm_promo_code_local_validation_no")}</option>
		<option value="yes" {if $processor_params.promo_local_validation == "yes"}selected="selected"{/if}>{__("paytm_promo_code_local_validation_yes")}</option>
	</select>
</div>
<div class="form-field">
	<label for="promo_code">{__("paytm_promo_code")}:</label>
	<input type="text" name="payment_data[processor_params][promo_code]" id="promo_code" value="{$processor_params.promo_code}" class="input-text" />
</div>
<div class="form-field">
	<label for="log_params">{__("paytm_log_params")}:</label>
	<select name="payment_data[processor_params][log_params]" id="log_params">
		<option value="yes" {if $processor_params.log_params == "yes"}selected="selected"{/if}>{__("paytm_yes")}</option>
		<option value="no" {if $processor_params.log_params == "no"}selected="selected"{/if}>{__("paytm_no")}</option>
	</select>
</div>
<div class="form-field">
	<label for="log_params"></label>
</div>
<div class="form-field">
	<label for="log_params">{__("paytm_version_txt")}</label>
</div>