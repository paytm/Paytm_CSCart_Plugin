
PAYMENT MODULE : PAYTM
---------------------------
	Allows you to use Paytm payment gateway with CSCart.
	
	    INSTALLATION PROCEDURE
	  --------------------------
	  
			Ensure you have a running version of cscart installed. This module was developed under CSCart 3.0.2
				-	Execute the following query in your backend (database)
				
				1.  REPLACE INTO `cscart_payment_processors` (`processor_id`, `processor`, `processor_script`, 
					`processor_template`, `admin_template`, `callback`, `type`) VALUES (1000, 'Paytm', 'paytm.php',
					'cc_outside.tpl', 'paytm.tpl', 'N', 'P');
				
				2.insert into `cscart_payments` (`payment_id`, `company_id`, `usergroup_ids`, `position`, `status`, `template`, `processor_id`, `processor_params`, `a_surcharge`, `p_surcharge`, `tax_ids`, `localization`, `payment_category`) 
				values('14','1','0','0','A','views/orders/components/payments/cc_outside.tpl','1000','a:7:{s:11:\"merchant_id\";s:0:\"\";s:10:\"secret_key\";s:0:\"\";s:13:\"industry_type\";s:0:\"\";s:12:\"website_name\";s:0:\"\";s:10:\"channel_id\";s:0:\"\";s:16:\"transaction_mode\";s:4:\"test\";s:10:\"log_params\";s:2:\"no\";}','0.000','0.000','','','tab3');
				
				3.insert into `cscart_payment_descriptions` (`payment_id`, `payment`, `description`, `instructions`, `surcharge_title`, `lang_code`) values('14','Paytm','Simplifying Payments',' ','','EN');
				
				4.	REPLACE INTO cscart_language_values (`lang_code`,`name`,`value`) VALUES ('EN','paytm_merchant_id','Merchant ID');
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
					
				-	Extract the downloaded zip file , there are two files called "paytm.php" and "paytm.tpl" and one
					checksum file included with this package ,
				-	Copy the files present in the folder "payments" and paste it to (root_dir)\app\payments\,
				-	Copy the file present in the folder "design" and paste it to (root_dir)\design\themes\responsive\templates\views\orders\components\payments\.
				
            CONFIGURATION
		  -----------------
			CSCart Settings
			
				-	Login to the administrator area of cscart,
				-	Choose Payment Methods under Administration tab , you can see Paytm under the Payment method if the module gets insatalled properly, 
				-	Click Edit and configure the following 
							
							- paytm Merchant ID: The Merchant Id provided by paytm.

							- paytm Secret Key: Please note that get this key ,login to your paytm merchant account 
							and visit the "URL and Key's" section at the "Integration" tab and generate a Key.
							
							- Transaction Mode: The mode you want to make transaction.  1.Test(Sandbox)	2.Live.
							
								
							and choose yes if you want to log the parameters which are posting to Paytm.(you can see the logs in the php error log file)
							
				-	Click update/save .
			
		
		Now you can make your payment securely through Paytm by selecting Paytm as the Payment Method at the Checkout stage.
			
