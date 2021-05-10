{script src="js/lib/inputmask/jquery.inputmask.min.js"}
{script src="js/lib/creditcardvalidator/jquery.creditCardValidator.js"}

{if $card_id}
    {assign var="id_suffix" value="`$card_id`"}
{else}
    {assign var="id_suffix" value=""}
{/if}

<script type="application/javascript" crossorigin="anonymous" src="{{ srcUrl }}" onload="alert('Script loaded!'); loaded=true;"></script>
<div class="buttons">
	<div class="pull-right">
	  <input type="button" value="{{ button_confirm }}" id="button-confirm" class="btn btn-primary" />
	</div>
  </div>
