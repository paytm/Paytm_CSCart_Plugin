#### Database Query
```
  REPLACE INTO `cscart_payment_processors` (`processor_id`, `processor`, `processor_script`, `processor_template`, `admin_template`, `callback`, `type`) VALUES (1000, 'Paytm', 'paytm.php', 'views/orders/components/payments/paytm.tpl', 'paytm.tpl', 'Y', 'P');
  INSERT INTO `cscart_payment_descriptions` (`payment_id`, `payment`, `description`, `instructions`, `surcharge_title`, `lang_code`) values('14','Paytm','Simplifying Payments',' ','','EN');
  REPLACE INTO cscart_language_values (`lang_code`,`name`,`value`) VALUES ('EN','paytm_merchant_id','Merchant ID');
  REPLACE INTO cscart_language_values (`lang_code`,`name`,`value`) VALUES ('EN','paytm_secret_key','Merchant Key');
  REPLACE INTO cscart_language_values (`lang_code`,`name`,`value`) VALUES ('EN','paytm_industry_type','Industry Type Id');
  REPLACE INTO cscart_language_values (`lang_code`,`name`,`value`) VALUES ('EN','paytm_website_name','Website');
  REPLACE INTO cscart_language_values (`lang_code`,`name`,`value`) VALUES ('EN','paytm_channel_id','Channel Id');
  REPLACE INTO cscart_language_values (`lang_code`,`name`,`value`) VALUES ('EN','paytm_version_txt','Paytm plugin updated on 06 Aug 2018.');
  REPLACE INTO cscart_language_values (`lang_code`,`name`,`value`) VALUES ('EN','paytm_transaction_url','Transaction URL');
  REPLACE INTO cscart_language_values (`lang_code`,`name`,`value`) VALUES ('EN','paytm_transaction_status_url','Transaction Status URL');
  REPLACE INTO cscart_language_values (`lang_code`,`name`,`value`) VALUES ('EN','paytm_custom_callbackurl','Custom Callback URL (if you want)');
  REPLACE INTO cscart_language_values (`lang_code`,`name`,`value`) VALUES ('EN','paytm_log_params','Log');
  REPLACE INTO cscart_language_values (`lang_code`,`name`,`value`) VALUES ('EN','paytm_yes','Yes');
  REPLACE INTO cscart_language_values (`lang_code`,`name`,`value`) VALUES ('EN','paytm_no','No');
  REPLACE INTO cscart_language_values (`lang_code`,`name`,`value`) VALUES ('EN','paytm_promo_code','Promo Code Value');
  REPLACE INTO cscart_language_values (`lang_code`,`name`,`value`) VALUES ('EN','paytm_promo_code_view','Promo Code');
  REPLACE INTO cscart_language_values (`lang_code`,`name`,`value`) VALUES ('EN','paytm_promo_code_view_yes','Yes');
  REPLACE INTO cscart_language_values (`lang_code`,`name`,`value`) VALUES ('EN','paytm_promo_code_view_no','No');
  REPLACE INTO cscart_language_values (`lang_code`,`name`,`value`) VALUES ('EN','paytm_promo_code_local_validation','Local Validation');
  REPLACE INTO cscart_language_values (`lang_code`,`name`,`value`) VALUES ('EN','paytm_promo_code_local_validation_yes','Yes');
  REPLACE INTO cscart_language_values (`lang_code`,`name`,`value`) VALUES ('EN','paytm_promo_code_local_validation_no','No');
```
#### Installation/Configurations
  1. Upload the contents of the plugin to your CS Cart Installation directory (content of app folder goes in app folder, content of design folder in design folder).
  2. Log into CS-Cart as administrator. Navigate to Administration / Payment Methods.
  3. Click the "+" to add a new payment method.
  4. Choose Paytm from the list and then click save. For template, choose "paytm.tpl"
  5. Click the 'Configure' tab.
  6. Enter your Paytm Details.
  7. Click 'Save'

#### In case of any query, please contact to Paytm.