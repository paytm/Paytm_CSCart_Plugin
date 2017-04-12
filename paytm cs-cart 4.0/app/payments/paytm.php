<?php
//
// Paytm_v2.1 - CSCart
//
//ini_set('display_errors','On');
//error_reporting(E_ALL);

if ( !defined('AREA') ) { die('Access denied'); }
use Tygh\Registry;

include_once('encdec_paytm.php');

if (!defined('BOOTSTRAP')) { die('Access denied'); }
// Handling response from paytm 

if (defined('PAYMENT_NOTIFICATION')) {

	
	$joint_order_id		= explode("-",$_POST['ORDERID']);
	$order_id			= $joint_order_id[0];
	$res_code			= $_POST['RESPCODE'];
	$res_desc			= $_POST['RESPMSG'];
	$checksum_recv		= $_POST['CHECKSUMHASH'];
	$paramList			= $_POST;

	if (fn_check_payment_script('paytm.php', $order_id, $processor_data)){
	
		if (empty($processor_data)) {
				$processor_data = fn_get_processor_data($order_info['email']);
				}
	$secret_key = $processor_data["processor_params"]['secret_key'];
	$merchant_id = $processor_data["processor_params"]['merchant_id'];
	$mod = $processor_data["processor_params"]['transaction_mode'];
	
		
	$bool = "FALSE";
	$bool = verifychecksum_e($paramList, $secret_key, $checksum_recv);
	$paytmTxnIdText = "";
	if(isset($_POST['TXNID']) && !empty($_POST['TXNID'])){
		$paytmTxnIdText = " Paytm Transaction Id : ".$_POST['TXNID'];
	}
	if (!empty($order_id)) {		
		if (fn_check_payment_script('paytm.php', $order_id, $processor_data)) {		
			$pp_response = array();			
			$order_info = fn_get_order_info($order_id);			
			if($bool =="TRUE"){
				if($_REQUEST['RESPCODE'] == 01){
					// Create an array having all required parameters for status query.
					$requestParamList = array("MID" => $merchant_id , "ORDERID" => $_POST['ORDERID']);
					
					$StatusCheckSum = getChecksumFromArray($requestParamList, $secret_key);
							
					$requestParamList['CHECKSUMHASH'] = $StatusCheckSum;
					
					// Call the PG's getTxnStatus() function for verifying the transaction status.
					if($mod=='test')
					{
						$check_status_url = 'https://pguat.paytm.com/oltp/HANDLER_INTERNAL/getTxnStatus';
					}
					else
					{
						$check_status_url = 'https://secure.paytm.in/oltp/HANDLER_INTERNAL/getTxnStatus';
					}
					$responseParamList = callNewAPI($check_status_url, $requestParamList);
					if($responseParamList['STATUS']=='TXN_SUCCESS' && $responseParamList['TXNAMOUNT']==$_POST['TXNAMOUNT'])
					{
						$pp_response['order_status'] = 'P';
						$pp_response['reason_text'] = "Thank you. Your order has been processed successfully.".$paytmTxnIdText;
					}
					else{
						$pp_response['order_status'] = 'D';
						$pp_response['reason_text'] = "Thank you. Your order has been declined due to security reasons.".$paytmTxnIdText;
					}
				}
				else{
					$pp_response['order_status'] = 'F';
					$pp_response['reason_text'] = "Thank you. Your order has been unsuccessfull".$paytmTxnIdText;
				}
			}
			else {
				$pp_response['order_status'] = 'D';
				$pp_response['reason_text'] = "Thank you. Your order has been declined due to security reasons.".$paytmTxnIdText;
			}
			
			fn_change_order_status($order_id,$pp_response['order_status']);
      fn_finish_payment($order_id, $pp_response,array());
      fn_order_placement_routines('route',$order_id);
      
		}
		exit;
	}
} 
}else {
	
	$merchant_id = $processor_data["processor_params"]['merchant_id'];
	$industry_type = $processor_data["processor_params"]['industry_type'];
	$website_name = $processor_data["processor_params"]['website_name'];
	$channel_id = $processor_data["processor_params"]['channel_id'];
	$current_location = Registry::get('config.current_location');
	
	$mod = $processor_data["processor_params"]['transaction_mode'];
	$callback = $processor_data["processor_params"]['callback'];
	
	$log = $processor_data['processor_params']['log_params'];
	
	if($mod == "test"){
	$paytm_url =  "https://pguat.paytm.com/oltp-web/processTransaction"; 
	}else {
		$paytm_url = "https://secure.paytm.in/oltp-web/processTransaction";	
	}
	//Order Total
	$paytm_total = fn_format_price($order_info['total']) ;
	$amount = $paytm_total ;							// Should be in Rupees 
	$paytm_shipping = fn_order_shipping_cost($order_info);//var_dump($order_info);exit;
	$paytm_order_id = (($order_info['repaid']) ? ($order_id .'_'. $order_info['repaid']) : $order_id).'-'.time();
	$date = date('Y-m-d H:i:s');
	
	$msg = fn_get_lang_var('text_cc_processor_connection');
	$msg = str_replace('[processor]', 'paytm', $msg);
	
	if (!empty($order_info['items'])) {
		foreach ($order_info['items'] as $k => $v) {
			$v['product'] = htmlspecialchars($v['product']);

		}
	}
	if($mod == "test")
	$mode = 0;
	else 
	$mode = 1;
	
	$return_url =fn_url("payment_notification.notify?payment=paytm&order_id=$order_id", AREA, 'http') . '&';
	
	$post_variables = Array(
            "MID" =>  $merchant_id,
            "ORDER_ID" => $paytm_order_id,
            "CUST_ID" => $order_info['email'],
            "TXN_AMOUNT" =>  $amount,
            "CHANNEL_ID" => $channel_id,
            "INDUSTRY_TYPE_ID" => $industry_type,
	      		"WEBSITE" => $website_name,
            );
	if($callback == 'yes')
	{
		$post_variables["CALLBACK_URL"] = $return_url;
	}
	$secret_key = $processor_data['processor_params']['secret_key'];
	
		
	if($log == "yes")
	{
		error_log("All Params(Parameters which are posting to paytm) : " .$all);
		error_log("paytm Secret Key : " .$secret_key);
	}

	//$checksum = $sum->calculateChecksum($secret_key,$all);
	$checksum = getChecksumFromArray($post_variables, $secret_key);//
	
	if($callback == 'yes')
	{
	echo <<<EOT
	<html>
	<body onLoad="document.paytm_form.submit();">
	<form action="{$paytm_url}" method="post" name="paytm_form">
	
	<input type=hidden name="MID" value="{$merchant_id}">
	<input type=hidden name="ORDER_ID" value="$paytm_order_id">
	<input type=hidden name="WEBSITE" value="{$website_name}">
	<input type=hidden name="INDUSTRY_TYPE_ID" value="{$industry_type}">
	<input type=hidden name="CHANNEL_ID" value="{$channel_id}">
	<input type=hidden name="TXN_AMOUNT" value="{$amount}">
	<input type=hidden name="CUST_ID"  value="{$order_info['email']}">
    <input type=hidden name="CALLBACK_URL" value="{$return_url}"> 
	<input type=hidden name="CHECKSUMHASH" value="{$checksum}">
	
	
EOT;
	}
	else{
		echo <<<EOT
	<html>
	<body onLoad="document.paytm_form.submit();">
	<form action="{$paytm_url}" method="post" name="paytm_form">
	
	<input type=hidden name="MID" value="{$merchant_id}">
	<input type=hidden name="ORDER_ID" value="$paytm_order_id">
	<input type=hidden name="WEBSITE" value="{$website_name}">
	<input type=hidden name="INDUSTRY_TYPE_ID" value="{$industry_type}">
	<input type=hidden name="CHANNEL_ID" value="{$channel_id}">
	<input type=hidden name="TXN_AMOUNT" value="{$amount}">
	<input type=hidden name="CUST_ID"  value="{$order_info['email']}">
    <input type=hidden name="CHECKSUMHASH" value="{$checksum}">
	
	
EOT;
	}


	echo <<<EOT
	</form>
	<div align=center>{$msg}</div>
	</body>
	</html>
EOT;

	fn_flush();
}
exit;
?>