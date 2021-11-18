#### Database Query
  1. REPLACE INTO `cscart_payment_processors` (`processor_id`, `processor`, `processor_script`, `processor_template`, `admin_template`, `callback`, `type`) VALUES (1000, 'Paytm', 'paytm.php', 'views/orders/components/payments/paytm.tpl', 'paytm.tpl', 'Y', 'P');
  2. insert into `cscart_payment_descriptions` (`payment_id`, `payment`, `description`, `instructions`, `surcharge_title`, `lang_code`) values('14','Paytm','Simplifying Payments',' ','','EN');
  3. REPLACE INTO cscart_language_values (`lang_code`,`name`,`value`) VALUES ('EN','paytm_merchant_id','Merchant ID');
  4. REPLACE INTO cscart_language_values (`lang_code`,`name`,`value`) VALUES ('EN','paytm_secret_key','Merchant Key');
  5. REPLACE INTO cscart_language_values (`lang_code`,`name`,`value`) VALUES ('EN','paytm_industry_type','Industry Type Id');
  6. REPLACE INTO cscart_language_values (`lang_code`,`name`,`value`) VALUES ('EN','paytm_website_name','Website');
  7. REPLACE INTO cscart_language_values (`lang_code`,`name`,`value`) VALUES ('EN','paytm_channel_id','Channel Id');
  8. REPLACE INTO cscart_language_values (`lang_code`,`name`,`value`) VALUES ('EN','paytm_version_txt','Paytm plugin updated on 10 November 2021.');
  9. REPLACE INTO cscart_language_values (`lang_code`,`name`,`value`) VALUES ('EN','paytm_custom_callbackurl','Custom Callback URL (if you want)');
  10. REPLACE INTO cscart_language_values (`lang_code`,`name`,`value`) VALUES ('EN','paytm_log_params','Log');
  11. REPLACE INTO cscart_language_values (`lang_code`,`name`,`value`) VALUES ('EN','paytm_yes','Yes');
  12. REPLACE INTO cscart_language_values (`lang_code`,`name`,`value`) VALUES ('EN','paytm_no','No');
  12. REPLACE INTO cscart_language_values (`lang_code`,`name`,`value`) VALUES ('EN','paytm_environment','Environment');
  13. REPLACE INTO cscart_language_values (`lang_code`,`name`,`value`) VALUES ('EN','paytm_staging','Staging');
  14. REPLACE INTO cscart_language_values (`lang_code`,`name`,`value`) VALUES ('EN','paytm_production','Production');
  15. REPLACE INTO cscart_language_values (`lang_code`,`name`,`value`) VALUES ('EN','paytm_bank_offer','Enable Bank Offers');
  16. REPLACE INTO cscart_language_values (`lang_code`,`name`,`value`) VALUES ('EN','paytm_emi_subvention','Enable EMI Subvention');
  17. REPLACE INTO cscart_language_values (`lang_code`,`name`,`value`) VALUES ('EN','paytm_dc_emi','Enable DC EMI');
    
#### Installation/Configurations
  1. Upload the contents of the plugin to your CS Cart Installation directory (content of app folder goes in app folder, content of design folder in design folder).
  2. Log into CS-Cart as administrator. Navigate to Administration / Payment Methods.
  3. Click the "+" to add a new payment method.
  4. Choose Paytm from the list and then click save. For template, choose "paytm.tpl"
  5. Click the 'Configure' tab.
  6. Enter your Paytm Details.
  7. Click 'Save'

#### Note
  1. This plugin supports Debit Card EMI, send us the user mobile number in order to check the user’s Debit Card EMI eligibility and show the applicable banks name.
  2. This plugin supports EMI Subvention and Bank offers, please reach out to us through Contact Us or your KAM to enable EMI Subvention and Bank Offers for your MID.
  
#### In case of any query, please contact to Paytm.
