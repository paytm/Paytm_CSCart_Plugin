 <?php
//
// Paytm_v2.1 - CSCart
//
//ini_set('display_errors','On');
//error_reporting(E_ALL);

if ( !defined('AREA') ) { die('Access denied'); }
use Tygh\Registry;

include_once('encdec_paytm.php');

require_once('PaytmChecksum.php');
require_once('PaytmHelper.php');
require_once('PaytmConstants.php');

//echo PaytmConstants::ENVIRONMENT;

if (!defined('BOOTSTRAP')) { die('Access denied'); }
// Handling response from paytm 
if (defined('PAYMENT_NOTIFICATION')) {
	if($_GET['dispatch']=='payment_notification.curlTest'){
		$testing_urls = array(
			fn_url(''),
			"https://www.gstatic.com/generate_204",
			"https://securegw.paytm.in/merchant-status/getTxnStatus",
		);
		foreach($testing_urls as $key=>$url){
			$debug[$key]["info"][] = "Connecting to <b>" . $url . "</b> using cURL";
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$res = curl_exec($ch);
			$content='';
			if (!curl_errno($ch)) {
				$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				$debug[$key]["info"][] = "cURL executed succcessfully.";
				$debug[$key]["info"][] = "HTTP Response Code: <b>". $http_code . "</b>";
				$content = $res;
			} else {
				$debug[$key]["info"][] = "Connection Failed !!";
				$debug[$key]["info"][] = "Error Code: <b>" . curl_errno($ch) . "</b>";
				$debug[$key]["info"][] = "Error: <b>" . curl_error($ch) . "</b>";
				break;
			}
			curl_close($ch);
		}
		foreach($debug as $k=>$v){
			echo "<ul>";
			foreach($v["info"] as $info){
				echo "<li>".$info."</li>";
			}
			if($k==(sizeof($debug)-1)){
				echo "<li>".$content."</li>";
			}
			echo "</ul>";
			echo "<hr/>";
		}
		die;
	}else{
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
		// $mod = $processor_data["processor_params"]['transaction_mode'];
		$transaction_url = $processor_data["processor_params"]['transaction_url'];
		$transaction_status_url = $processor_data["processor_params"]['transaction_status_url'];
		
			
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
						$check_status_url = $transaction_status_url;
						$responseParamList = callNewAPI($check_status_url, $requestParamList);
						if($responseParamList['STATUS']=='TXN_SUCCESS' && $responseParamList['TXNAMOUNT']==$_POST['TXNAMOUNT'])
						{
							$pp_response['order_status'] = 'P';
							$pp_response['reason_text'] = "Thank you. Your order has been processed successfully.".$paytmTxnIdText;
						}
						else{
							$pp_response['order_status'] = 'D';
							$pp_response['reason_text'] = "It seems some issue in server to server communication. Kindly connect with administrator.".$paytmTxnIdText;
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
} 
}else {

	//echo "<pre>";print_r($processor_data);die;
	$merchant_id = $processor_data["processor_params"]['merchant_id'];
	$industry_type = $processor_data["processor_params"]['industry_type'];
	$website_name = $processor_data["processor_params"]['website_name'];
	$channel_id = $processor_data["processor_params"]['channel_id'];
	$current_location = Registry::get('config.current_location');

	$promoCodeView=$processor_data["processor_params"]['promo_code_view'];
	$promoLocalValidation=$processor_data["processor_params"]['promo_local_validation'];
	$promoCode=$processor_data["processor_params"]['promo_code'];
	$userEnterCode=$order_info['payment_info']['paytmPromoCode'];
	$addPromoInReq=false;
	if($promoCodeView=='yes' && trim($userEnterCode)!=''){
		$addPromoInReq=true;
		$userEnterCode=trim($userEnterCode);
	}
	$transaction_url = $processor_data["processor_params"]['transaction_url'];
	$transaction_status_url = $processor_data["processor_params"]['transaction_status_url'];
	$customCallBackUrl = $processor_data["processor_params"]['paytm_custom_callbackurl'];
	$log = $processor_data['processor_params']['log_params'];
	//Order Total
	$paytm_url = (!empty($transaction_url))?$transaction_url:"https://securegw-stage.paytm.in/theia/processTransaction";	
	$paytm_total = fn_format_price($order_info['total']) ;
	$amount = $paytm_total ;							// Should be in Rupees 
	$paytm_shipping = fn_order_shipping_cost($order_info);//var_dump($order_info);exit;
	$paytm_order_id = (($order_info['repaid']) ? ($order_id .'_'. $order_info['repaid']) : $order_id);
	$date = date('Y-m-d H:i:s');
	
	$msg = fn_get_lang_var('text_cc_processor_connection');
	$msg = str_replace('[processor]', 'paytm', $msg);
	
	if (!empty($order_info['items'])) {
		foreach ($order_info['items'] as $k => $v) {
			$v['product'] = htmlspecialchars($v['product']);

		}
	}
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
	$post_variables['CALLBACK_URL']=$return_url=trim($customCallBackUrl)!=''?$customCallBackUrl:$return_url;
	if($addPromoInReq){
		$post_variables['PROMO_CAMP_ID']=$userEnterCode;
	}
	$secret_key = $processor_data['processor_params']['secret_key'];
	
		
	if($log == "yes")
	{
		error_log("All Params(Parameters which are posting to paytm) : " .$all);
		error_log("paytm Secret Key : " .$secret_key);
	}

	$checksum = getChecksumFromArray($post_variables, $secret_key);//
	//echo "<pre>";print_r($post_variables);print_r($checksum);var_dump($addPromoInReq);die;
	
	/****** js checkout code starts here*********/

		$paytmParams = array();
		$returnUrl = $return_url=trim($customCallBackUrl)!=''?$customCallBackUrl:$return_url;
		
		$paytmParams["body"] = array(
			"requestType"   => "Payment",
			"mid"           => $merchant_id,
			"websiteName"   => $website_name,
			"orderId"       => $paytm_order_id,
			"callbackUrl"   => $returnUrl,
			"txnAmount"     => array(
				"value"     => round($amount),
				"currency"  => "INR",
			),
			"userInfo"      => array(
				"custId"    => $order_info['email'],
			),
		);
		// $paytmParams["body"] = [];
		// $paytmParams["body"]['requestType'] = "Payment";
		// $paytmParams["body"]['mid'] = $merchant_id;
		// $paytmParams["body"]['websiteName'] = $website_name;
		// $paytmParams["body"]['orderId'] = $paytm_order_id;
		// $paytmParams["body"]['callbackUrl'] = $returnUrl;
		// $paytmParams["body"]['txnAmount'] = [];
		// $paytmParams["body"]['txnAmount']['value'] = $amount;
		// $paytmParams["body"]['txnAmount']['currency'] = "INR";
		// $paytmParams["body"]['userInfo'] = [];
		// $paytmParams["body"]['userInfo']['custId'] = $order_info['email'];
		//echo "<pre>";print_r($paytmParams["body"]);die;
		
		$checksum = PaytmChecksum::generateSignature(json_encode($paytmParams["body"], JSON_UNESCAPED_SLASHES), $secret_key);

		// $paytmParams["head"] = array(
		//     "signature"    => $checksum
		// );
		$paytmParams["head"] = [];
		$paytmParams["head"]['signature'] = $checksum;
		
		$post_data = json_encode($paytmParams, JSON_UNESCAPED_SLASHES);
		
		if(PaytmConstants::ENVIRONMENT == "stagging"){
			// /* for Staging */
			$urlInitiateToken = PaytmConstants::STAGING_HOST.PaytmConstants::INITIATE_TRANSACTION_URL."?mid=".$merchant_id."&orderId=".$paytm_order_id."";
			$host = PaytmConstants::STAGING_HOST;
		}else{
			
			/* for Production */
			$urlInitiateToken = PaytmConstants::PRODUCTION_HOST.PaytmConstants::INITIATE_TRANSACTION_URL."?mid=".$merchant_id."&orderId=".$paytm_order_id."";
			$host = PaytmConstants::PRODUCTION_HOST;
		}
	
		$response = PaytmHelper::executecUrl($urlInitiateToken, $post_data);
	
		$data = [];
		if(isset($response['body']['txnToken']) && !empty($response['body']['txnToken'])){
			$data['txnToken'] = $response['body']['txnToken'];
			$data['orderId'] = $paytm_order_id;
			$data['message'] = "Token generated successfully";
		}else{
			$data['txnToken'] = '';
			$data['orderId'] = '';
			$data['message'] = "Something went wrong";
		}
		//echo "<pre>";print_r($data);die;
		

	/********* js checkout ends here ***********/
	if($addPromoInReq){
		echo <<<EOT
		<html>
		<body onLoad="document.paytm_form.submit();">
		<form action="{$paytm_url}" method="post" name="paytm_form">
		
			<input type=hidden name="MID" value="{$merchant_id}">
			<input type=hidden name="PROMO_CAMP_ID" value="{$userEnterCode}">
			<input type=hidden name="ORDER_ID" value="$paytm_order_id">
			<input type=hidden name="WEBSITE" value="{$website_name}">
			<input type=hidden name="INDUSTRY_TYPE_ID" value="{$industry_type}">
			<input type=hidden name="CHANNEL_ID" value="{$channel_id}">
			<input type=hidden name="TXN_AMOUNT" value="{$amount}">
			<input type=hidden name="CUST_ID"  value="{$order_info['email']}">
		    <input type=hidden name="CALLBACK_URL" value="{$return_url}"> 
			<input type=hidden name="CHECKSUMHASH" value="{$checksum}">
		</form>
		<div align=center>{$msg}</div>
		</body>
		</html>
EOT;
	}else{

		echo'
		<html>
			<body>
			<script type="application/javascript" crossorigin="anonymous" src="'.$host.'/merchantpgpui/checkoutjs/merchants/'.$merchant_id.'.js"></script>
				<script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
				<script type="text/javascript">
					
					$( document ).ready(function() {
						function invokeBlinkCheckoutPopup(){
							var orderId = '.$paytm_order_id.';
							var txnToken = "'.$data['txnToken'].'";
							var amount = '.round($amount).';
						var config = {
				         "root": "",
				         "flow": "DEFAULT",
				         "data": {
				          "orderId": orderId /* update order id */,
				          "token": txnToken /* update token value */,
				          "tokenType": "TXN_TOKEN",
				          "amount": amount /* update amount */
				         },
				         "handler": {
				            "notifyMerchant": function(eventName,data){
				        if(eventName == "SESSION_EXPIRED"){
				          location.reload(); 
				        }
				            } 
				          }
				        };
	      
	        			if(window.Paytm && window.Paytm.CheckoutJS){
	                		// initialze configuration using init method 
	                		window.Paytm.CheckoutJS.init(config).then(function onSuccess() {
	                   		// after successfully update configuration invoke checkoutjs
	                   			window.Paytm.CheckoutJS.invoke();
	                		}).catch(function onError(error){
			                    console.log("error => ",error);
			                });
	       				} 
	    			}
	    			setTimeout(function(){ 
	    				invokeBlinkCheckoutPopup(); 
	    			}, 3000);
	    			
	    		});

			   		
		    	</script>
			</body>
		</html>';
		//echo $paytm_url;die;
// 		echo <<<EOT
// 		<html>
// 		<body onLoad="document.paytm_form.submit();">
// 		<form action="{$paytm_url}" method="post" name="paytm_form">
		
// 			<input type=hidden name="MID" value="{$merchant_id}">
// 			<input type=hidden name="ORDER_ID" value="$paytm_order_id">
// 			<input type=hidden name="WEBSITE" value="{$website_name}">
// 			<input type=hidden name="INDUSTRY_TYPE_ID" value="{$industry_type}">
// 			<input type=hidden name="CHANNEL_ID" value="{$channel_id}">
// 			<input type=hidden name="TXN_AMOUNT" value="{$amount}">
// 			<input type=hidden name="CUST_ID"  value="{$order_info['email']}">
// 		    <input type=hidden name="CALLBACK_URL" value="{$return_url}"> 
// 			<input type=hidden name="CHECKSUMHASH" value="{$checksum}">
// 		</form>
// 		<div align=center>{$msg}</div>
// 		</body>
// 		</html>
// EOT;
	}

	fn_flush();
}
exit;
?>
