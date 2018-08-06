{script src="js/lib/inputmask/jquery.inputmask.min.js"}
{script src="js/lib/creditcardvalidator/jquery.creditCardValidator.js"}

{if $card_id}
    {assign var="id_suffix" value="`$card_id`"}
{else}
    {assign var="id_suffix" value=""}
{/if}
<style type="text/css">
    .btnStyl{
        color: white;
        padding: 5px 10px;
        font-weight: 700;
    }
    .btnBlu, .btnBlu:hover{
        background-color: blue;
        border-color: blue;
    }
    .btnRed, .btnRed:hover{
        background-color: red;
        border-color: red;
    }
    .errSpan{
        color: red;
    }
    .successSpan{
        color: green;
    }
    .errBorder{
        border-color: red !important;
    }
    .sucessBorder{
        border-color: green !important;
    }
    .hideShowPromoDiv{
        display: none;
    }
</style>
<div class="clearfix">
    <div class="ty-credit-card cm-cc_form_{$id_suffix} hideShowPromoDiv">
            <div class="ty-credit-card__control-group ty-control-group">
                <label for="credit_card_number_{$id_suffix}" class="ty-control-group__title cm-cc-number cc-number_{$id_suffix} cm-required">{__("paytm_promo_code")}</label>
                <input size="35" type="text" id="merchantPromoCode" value="" class="cm-autocomplete-off ty-inputmask-bdi" />
                <input type="hidden" name="payment_info[paytmPromoCode]" value="" id="applyCodeId">
                <span class="messSpan"></span>
            </div>
    
            <div class="ty-credit-card__control-group ty-control-group">
                <input type="button" value="Apply" onclick="applyCode()" class="btnStyl btnBlu">
            </div>
    </div>
    <input type="hidden" id="paytmPromoCodeVal" value="{$payment.processor_params}">
</div>
<script type="text/javascript">
var paytmPromoCodeVal=$('#paytmPromoCodeVal').val();
var tmpArr=paytmPromoCodeVal.split(';');
var showPromoStr='';
var localValidation='';
var promoFinlStr='';
for (var i = 0; i <= tmpArr.length - 1; i++) {
    if(tmpArr[i].indexOf("promo_code")!= -1){
        promoFinlStr=tmpArr[i+1];
    }
    if(tmpArr[i].indexOf("promo_code_view")!= -1){
        showPromoStr=tmpArr[i+1];
    }
    if(tmpArr[i].indexOf("promo_local_validation")!= -1){
        localValidation=tmpArr[i+1];
    }
}
var finalShowPromArr=showPromoStr.split(':');
var shoPromo=finalShowPromArr[finalShowPromArr.length-1].replace(/\"/g, '');   // remove " sign from promo code string
var localValidationArr=localValidation.split(':');
var doLocalValidate=localValidationArr[localValidationArr.length-1].replace(/\"/g, '');   // remove " sign from promo code string
var finalPromArr=promoFinlStr.split(':');
paytmPromoCodeVal=finalPromArr[finalPromArr.length-1].replace(/\"/g, '');   // remove " sign from promo code string
if($.trim(showPromoStr)!=''){
    if($.trim(shoPromo)=='yes'){
        if($.trim(doLocalValidate)=='yes' && $.trim(paytmPromoCodeVal)==''){

        }else{
            $('.hideShowPromoDiv').css('display','block');
        }
    }
}
function applyCode(){
    if($.trim(promoFinlStr)!=''){
        var textPromoVal=$.trim($('#merchantPromoCode').val());
        $('.messSpan').html('');
        $('.messSpan').removeClass('errSpan');
        $('.messSpan').removeClass('successSpan');
        $("#merchantPromoCode").removeClass('errBorder');
        $("#merchantPromoCode").removeClass('sucessBorder');
        $('#applyCodeId').val('');
        if($('.btnStyl').hasClass('btnBlu')){
            if($.trim(textPromoVal)==''){
            
            }else{
                var proArr=paytmPromoCodeVal.split(',');
                var validate=false;
                if($.trim(doLocalValidate)=='yes'){
                    for ( var i = 0, l = proArr.length; i < l; i++ ) {
                        // proArr[i] = $.trim(proArr[i]);
                        if(textPromoVal==$.trim(proArr[i])){
                            validate=true;
                        }
                    }
                }else{
                    validate=true;
                }
                if(validate){
                    $('#merchantPromoCode').attr('disabled',true);
                    $('#merchantPromoCode').prop('disabled',true);
                    $('.messSpan').html('Applied Successfully');
                    $('.messSpan').addClass('successSpan');
                    $("#merchantPromoCode").addClass('sucessBorder');
                    $('.btnStyl').val('Remove');
                    $('.btnStyl').removeClass('btnBlu');
                    $('.btnStyl').addClass('btnRed');
                    $('#applyCodeId').val(textPromoVal);
                }else{
                    $('.messSpan').html('Incorrect Promo Code');
                    $('.messSpan').addClass('errSpan');
                    $("#merchantPromoCode").addClass('errBorder');

                }
            }
        }else{
            $('#merchantPromoCode').attr('disabled',false);
            $('#merchantPromoCode').prop('disabled',false);
            $('.btnStyl').val('Apply');
            $('.btnStyl').removeClass('btnRed');
            $('.btnStyl').addClass('btnBlu');
            $('#merchantPromoCode').val('');
        }
    }else{
    }
}
</script>