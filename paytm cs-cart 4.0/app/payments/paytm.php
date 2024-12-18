 <?php
//
// Paytm_v2.2 - CSCart
//
//ini_set('display_errors','On');
//error_reporting(E_ALL);

if ( !defined('AREA') ) { die('Access denied'); }
use Tygh\Registry;

//include_once('encdec_paytm.php');

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
			"https://secure.paytmpayments.com/merchant-status/getTxnStatus",
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
		// $joint_order_id		= explode("-",$_POST['ORDERID']);
		// $order_id			= $joint_order_id[0];
		$order_id = !empty($_POST['ORDERID'])? PaytmHelper::getOrderId($_POST['ORDERID']) : 0;
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
			if( $processor_data["processor_params"]['environment'] == "Staging"){
				$env = 0;
			}else{
				$env = 1;
			}
			// $mod = $processor_data["processor_params"]['transaction_mode'];

			if(!empty($_POST['CHECKSUMHASH'])){
	            $post_checksum = $_POST['CHECKSUMHASH'];
	            unset($_POST['CHECKSUMHASH']);  
	        }else{
	            $post_checksum = "";
	        }

	        $isValidChecksum = PaytmChecksum::verifySignature($_POST, $secret_key, $post_checksum);
	        if($isValidChecksum === true)
	        {
	        	$reqParams = array(
	                "MID"       => $merchant_id,
	                "ORDERID"   => $_POST['ORDERID']
	            );

	            $reqParams['CHECKSUMHASH'] = PaytmChecksum::generateSignature($reqParams, $secret_key);
	            /* number of retries untill cURL gets success */
	            $retry = 1;
	            do{
	                $postData = 'JsonData='.urlencode(json_encode($reqParams));
	                $resParams = PaytmHelper::executecUrl(PaytmHelper::getPaytmURL(PaytmConstants::ORDER_STATUS_URL, $env), $postData);
	                $retry++;

	               
	            } while(!$resParams['STATUS'] && $retry < PaytmConstants::MAX_RETRY_COUNT);
	            if($resParams['STATUS'] == 'TXN_SUCCESS') {            	
					$pp_response['order_status'] = 'P';
					$pp_response['reason_text'] = "Thank you. Your order has been processed successfully.".$paytmTxnIdText;
	            }else{
					$pp_response['order_status'] = 'D';
					$pp_response['reason_text'] = "It seems some issue in server to server communication. Kindly connect with administrator.".$paytmTxnIdText;
				}
	        }else{
	        	$pp_response['order_status'] = 'D';
				$pp_response['reason_text'] = "Thank you. Your order has been declined due to security reasons.".$paytmTxnIdText;
	        }
	        fn_change_order_status($order_id,$pp_response['order_status']);
	     	fn_finish_payment($order_id, $pp_response,array());
	      	fn_order_placement_routines('route',$order_id);		
			exit;				
		}	
	} 
}else {
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
	$paytm_url = (!empty($transaction_url))?$transaction_url:"https://securestage.paytmpayments.com/theia/processTransaction";	
	$paytm_total = fn_format_price($order_info['total']) ;
	$amount = $paytm_total ;							// Should be in Rupees 
	$paytm_shipping = fn_order_shipping_cost($order_info);//var_dump($order_info);exit;
	//$paytm_order_id = (($order_info['repaid']) ? ($order_id .'_'. $order_info['repaid']) : $order_id);
	$paytm_order_id = PaytmHelper::getPaytmOrderId($order_id);
	//$paytm_order_id = time();
	$date = date('Y-m-d H:i:s');
	$msg = fn_get_lang_var('text_cc_processor_connection');
	$msg = str_replace('[processor]', 'paytm', $msg);
	
	if (!empty($order_info['items'])) {
		foreach ($order_info['items'] as $k => $v) {
			$v['product'] = htmlspecialchars($v['product']);

		}
	}
	$return_url =fn_url("payment_notification.notify?payment=paytm&order_id=$paytm_order_id", AREA, 'http') . '&';
	$secret_key = $processor_data['processor_params']['secret_key'];
	
	if($log == "yes")
	{
		error_log("All Params(Parameters which are posting to paytm) : " .$all);
		error_log("paytm Secret Key : " .$secret_key);
	}

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
				"value"     => $amount,
				"currency"  => "INR",
			),
			"userInfo"      => array(
				"custId"    => $order_info['email'],
			),
		);
		// for bank offers
        if($processor_data["processor_params"]['bank_offer'] == "yes" ){
            $paytmParams["body"]["simplifiedPaymentOffers"]["applyAvailablePromo"]= "true";
        }
        // for emi subvention
        if($processor_data["processor_params"]['emi_subvention'] == "yes"){
            $paytmParams["body"]["simplifiedSubvention"]["customerId"]= $order_info['email'];
            $paytmParams["body"]["simplifiedSubvention"]["subventionAmount"]= $amount;
            $paytmParams["body"]["simplifiedSubvention"]["selectPlanOnCashierPage"]= "true";
            //$paytmParams["body"]["simplifiedSubvention"]["offerDetails"]["offerId"]= 1;
        }
        // for dc emi
        if($processor_data["processor_params"]['dc_emi'] == "yes"){
            $paytmParams["body"]["userInfo"]["mobile"]= $order_info['phone'];
        }
        $checksum = PaytmChecksum::generateSignature(json_encode($paytmParams["body"], JSON_UNESCAPED_SLASHES), $secret_key);

		$paytmParams["head"] = [];
		$paytmParams["head"]['signature'] = $checksum;
		
		$post_data = json_encode($paytmParams, JSON_UNESCAPED_SLASHES);
		
		if($processor_data['processor_params']['environment'] == "Staging"){
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
			fn_set_notification('W', __('important'), ('Something went wrong.Please contact administrator.'));
			fn_redirect('checkout.checkout');
			exit;		

		}

	/********* js checkout ends here ***********/

	$wait_msg='<style>#paytm-pg-spinner{width:70px;text-align:center;z-index:999999;position:fixed;top:25%;left:50%}#paytm-pg-spinner>div{width:10px;height:10px;background-color:#012b71;border-radius:100%;display:inline-block;-webkit-animation:sk-bouncedelay 1.4s infinite ease-in-out both;animation:sk-bouncedelay 1.4s infinite ease-in-out both}#paytm-pg-spinner .bounce1{-webkit-animation-delay:-.64s;animation-delay:-.64s}#paytm-pg-spinner .bounce2{-webkit-animation-delay:-.48s;animation-delay:-.48s}#paytm-pg-spinner .bounce3{-webkit-animation-delay:-.32s;animation-delay:-.32s}#paytm-pg-spinner .bounce4{-webkit-animation-delay:-.16s;animation-delay:-.16s}#paytm-pg-spinner .bounce4,#paytm-pg-spinner .bounce5{background-color:#48baf5}@-webkit-keyframes sk-bouncedelay{0%,100%,80%{-webkit-transform:scale(0)}40%{-webkit-transform:scale(1)}}@keyframes sk-bouncedelay{0%,100%,80%{-webkit-transform:scale(0);transform:scale(0)}40%{-webkit-transform:scale(1);transform:scale(1)}}.paytm-overlay{width:100%;position:fixed;top:0;left:0;opacity:.3;height:100%;background:#000;z-index:9999}.paytm-woopg-loader p{font-size:10px!important}.paytm-woopg-loader a{font-size:15px!important}.refresh-payment{display:inline;margin-right:20px;width:100px;background:#00b9f5;padding:10px 15px;border-radius:5px;color:#fff;text-decoration:none}#paytm-checkoutjs{display:block!important}.paytm-action-btn{display:block;padding:25px}</style><script type="application/javascript" crossorigin="anonymous" src="'.$host.'/merchantpgpui/checkoutjs/merchants/'.$merchant_id.'.js" onload="invokeBlinkCheckoutPopup();"></script><div id="paytm-pg-spinner" class="paytm-woopg-loader"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div><div class="bounce4"></div><div class="bounce5"></div><p class="loading-paytm">Loading Paytm...</p></div><div class="paytm-overlay paytm-woopg-loader"></div><div class="paytm-action-btn">	
	</div>';
			
			echo '<script type="text/javascript">
			function invokeBlinkCheckoutPopup(){
				console.log("method called");
				var config = {
					"root": "",
					"flow": "DEFAULT",
					"data": {
					  "orderId": "'.$data['orderId'].'", 
					  "token": "'.$data['txnToken'].'", 
					  "tokenType": "TXN_TOKEN",
					  "amount": "'.$amount.'"
					},
				        "integration": {
		                               "platform": "CsCart",
		                               "version": "'.PRODUCT_VERSION.'|'.PaytmConstants::PLUGIN_VERSION.'"
		                        },	
					"handler": {
					  "notifyMerchant": function(eventName,data){
						console.log("notifyMerchant handler function called");
						if(eventName=="APP_CLOSED")
						{
							var url = window.location.href;
							var res = url.replace("place_order", "checkout");
							window.location = res;

						}
					  } 
					}
				  };
			
				  if(window.Paytm && window.Paytm.CheckoutJS){
					  window.Paytm.CheckoutJS.onLoad(function excecuteAfterCompleteLoad() {
						  window.Paytm.CheckoutJS.init(config).then(function onSuccess() {
							  window.Paytm.CheckoutJS.invoke();
						  }).catch(function onError(error){
							  console.log("error => ",error);
						  });
					  });
				  } 
			}
			jQuery(document).ready(function(){ jQuery(".re-invoke").on("click",function(){ 
				window.Paytm.CheckoutJS.invoke(); return false; }); });
			</script>'.$wait_msg.'
			';
	fn_flush();
}
exit;
?>
