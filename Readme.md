# INSTALLATION STEPS
Ensure you have a running version of CScart installed. This module was developed under CSCart 3.0.2

 1. Execute the following queries in the backend (database)
 
 ```sh	
  1. REPLACE INTO `cscart_payment_processors` (`processor_id`, `processor`, `processor_script`,	`processor_template`, `admin_template`, `callback`, `type`) VALUES (1000, 'Paytm', 'paytm.php',	'cc_outside.tpl', 'paytm.tpl', 'N', 'P');
 ```
 ```sh
  2. insert into `cscart_payments` (`payment_id`, `company_id`, `usergroup_ids`, `position`, `status`, `template`, `processor_id`, `processor_params`, `a_surcharge`, `p_surcharge`, `tax_ids`, `localization`, `payment_category`) values('14','1','0','0','A','views/orders/components/payments/cc_outside.tpl','1000','a:7:{s:11:\"merchant_id\";s:0:\"\";s:10:\"secret_key\";s:0:\"\";s:13:\"industry_type\";s:0:\"\";s:12:\"website_name\";s:0:\"\";s:10:\"channel_id\";s:0:\"\";s:16:\"transaction_mode\";s:4:\"test\";s:10:\"log_params\";s:2:\"no\";}','0.000','0.000','','','tab3');
 ```
 ```sh
  3. insert into `cscart_payment_descriptions` (`payment_id`, `payment`, `description`, `instructions`, `surcharge_title`, `lang_code`) values('14','Paytm','Simplifying Payments',' ','','EN');
 ```
 ```sh
  4. REPLACE INTO cscart_language_values (`lang_code`,`name`,`value`) VALUES ('EN','paytm_merchant_id','Merchant ID');
        REPLACE INTO cscart_language_values (`lang_code`,`name`,`value`) VALUES ('EN','paytm_secret_key','Merchant Key');
        REPLACE INTO cscart_language_values (`lang_code`,`name`,`value`) VALUES ('EN','paytm_industry_type','Industry Type Id');
        REPLACE INTO cscart_language_values (`lang_code`,`name`,`value`) VALUES ('EN','paytm_website_name','Website');
        REPLACE INTO cscart_language_values (`lang_code`,`name`,`value`) VALUES ('EN','paytm_channel_id','Channel Id');
        REPLACE INTO cscart_language_values (`lang_code`,`name`,`value`) VALUES ('EN','paytm_transaction_mode','Transaction Mode');
        REPLACE INTO cscart_language_values (`lang_code`,`name`,`value`) VALUES ('EN','paytm_live','Live');
        REPLACE INTO cscart_language_values (`lang_code`,`name`,`value`) VALUES ('EN','paytm_test','Test');
        REPLACE INTO cscart_language_values (`lang_code`,`name`,`value`) VALUES ('EN','paytm_log_params','Log');
        REPLACE INTO cscart_language_values (`lang_code`,`name`,`value`) VALUES ('EN','paytm_yes','Yes');
        REPLACE INTO cscart_language_values (`lang_code`,`name`,`value`) VALUES ('EN','paytm_no','No');
 ```
 2. There are two files called "paytm.php" and "paytm.tpl" and one checksum file included with this package.
 3. Copy the files present in the folder "payments" and paste it to (root_dir)/app/payments/
 4. Copy the file present in the folder "design" and paste it to (root_dir)/design/themes/responsive/templates/views/orders/components/payments/

# CONFIGURATIONs FOR CSCart SETTINGS
 1. Login to the administrator area of cscart,
 2. Choose *Payment Methods* under the *Administration* tab, you will see *Paytm* under the Payment method if the module gets insatalled properly. Click on Edit and configure the following: 
    1. paytm Merchant ID: The Merchant Id provided by paytm.
    2. paytm Secret Key: Please note that get this key ,login to your paytm merchant account and visit the "URL and Key's" section at the "Integration" tab and generate a Key.
    3. Transaction Mode: Choose the mode you want to make transaction in.
        - Test(Sandbox)
        - Live.
    4. Choose *yes* if you want to log the parameters which are posting to Paytm.(you can see the logs in the php error log file).
    5. Click update/save.

Now you can make your payment securely through Paytm by selecting Paytm as the Payment Method at the Checkout stage.
