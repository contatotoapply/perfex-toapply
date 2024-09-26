<script>
	var list_commission;
	$(function(){
		"use strict";

		appValidateForm($('form'),{'list_commission[]':'required', amount:'required', date:'required', paymentmode: 'required'});

		list_commission = <?php echo html_entity_decode($list_commission_json); ?>;
		$('select[name="list_commission[]"]').on('change', function() {
		  	var amount = 0;
		  	$.each($(this).val(), function( index, value ) {
			  amount = amount + parseFloat(list_commission[value]);
			});
			$('input[id="amount"]').val(amount.toFixed(2));
	  	});

		$('input[name="receipt_for_a_specific_agent"]').on('change', function() {
		    if($('#receipt_for_a_specific_agent').is(':checked') == true){
		      $('#div_receipt_for_a_specific_agent').removeClass('hide');
				get_commission_by_agent();

		    }else{
		      $('#div_receipt_for_a_specific_agent').addClass('hide');
				get_commission_by_agent(true);
		    }
		});

		$('select[name="agent"]').on('change', function() {
			get_commission_by_agent();
		});
	});

	function get_commission_by_agent(all = false){
		var data = {};
		data.receipt_id = $('input[name="receipt_id"]').val();
		if(!all){
			data.agent = $('select[name="agent"]').val();
		}

	    $.post(admin_url + 'commission/get_commission_by_agent', data).done(function(response) {
	      response = JSON.parse(response);
	      var html = '';
	      $.each(response, function() {
	          html += '<option value="'+ this.id +'" data-subtext="'+this.amount+'">'+ this.commission_info +'</option>';
	       });
	      $('select[name="list_commission[]"]').html(html);
	      $('select[name="list_commission[]"]').selectpicker('refresh');
	    });
	}
</script>