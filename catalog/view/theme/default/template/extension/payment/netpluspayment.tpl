<div class="buttons">
  <div class="pull-right">
    <input type="button" value="<?php echo $button_confirm; ?>" id="button_send_to_netplus" class="btn btn-primary" data-loading-text="<?php echo $text_loading; ?>" />
  </div>
</div>



             <form class="demo-fm" method="POST" id="netpluspay_form" name="netpluspay_form" action="" >
 
 <input type="hidden" name="full_name"  >

 <input type="hidden" name="email"  >

 <input type="hidden" name="total_amount" >

 <button  hidden="hidden" type="submit" >Pay</button>

 <input type="hidden" name="merchant_id" >
 <input type="hidden" name="currency_code" value="NGN">
 <input type="hidden" name="narration" value="Online order">
 <input type="hidden" name="order_id" >
 <input type="hidden" name="return_url" >
 <input type="hidden" name="recurring" value="no">
 </form>

 
<script type="text/javascript"><!--
$('#button_send_to_netplus').on('click', function() {
	$.ajax({
		type: 'get',
		url: 'index.php?route=extension/payment/netpluspayment/confirm',
		cache: false,
		beforeSend: function() {
			$('#button-confirm').button('loading');
		},
		complete: function() {
			$('#button-confirm').button('reset');
		},
		success: function(response) {
                        response= $.parseJSON(response);
			
                      var return_url=response.website_url+'index.php?route=netpluspayment/response';

                         $("input[name='merchant_id']").val(response.merchant_id);
		         $("input[name='order_id']").val(response.order_id);
			 $("input[name='return_url']").val(return_url);
			 $("input[name='full_name']").val(response.fullname);
			 $("input[name='email']").val(response.email);
			 $("input[name='total_amount']").val(response.total);
			 $('#netpluspay_form').attr('action', response.gateway_url);
		        $('#netpluspay_form').submit();
                             console.log($('#netpluspay_form').html());
                              console.log(response.gateway_url);
                                     
                        
		}
	});
	
	
	
});
//--></script>
