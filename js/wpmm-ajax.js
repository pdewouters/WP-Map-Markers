jQuery(document).ready(function($) {
	$('#wpmm_geocode_button').click(function() {
		$('#wpmm_loading').show();
		$('#wpmm_geocode_button').attr('disabled', true);
		
      data = {
      	action: 'wpmm_get_results',
      	wpmm_nonce: wpmm_vars.wpmm_nonce,
        wpmm_address: $('#wpmm_mbe_address').val()
      };

     	$.post(ajaxurl, data, function (response) {

            var lat_lng = jQuery.parseJSON(response);
            console.log(lat_lng);
			$('input#_wpmm_latitude').val(lat_lng.latitude);
			$('input#_wpmm_longitude').val(lat_lng.longitude);            
			$('#wpmm_loading').hide();
			$('#wpmm_geocode_button').attr('disabled', false);
		});	
		
		return false;
	});
});