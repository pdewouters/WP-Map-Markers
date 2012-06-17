jQuery(document).ready(function($) {
 // console.log(wpmm_vars);
    if(wpmm_vars != 'null')
        var center = new google.maps.LatLng(wpmm_vars.lat[0], wpmm_vars.lng[0]);
    else
        var center = new google.maps.LatLng(38.8978881835938, -77.0363311767578);
    
    var myOptions = {
        center: center,
        zoom: 16,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };            
    var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
    var location = new google.maps.LatLng(wpmm_vars.lat[0],wpmm_vars.lng[0]);
    
    if(wpmm_vars != 'null'){
        var marker = new google.maps.Marker({
            position: location,
            draggable:true,
            map: map
        });  
        google.maps.event.addListener (marker, 'dragend', 
            function (event) {       
                // Pan the maps center to the markers position
                // you could do any of your own stuff here
                var pos = marker.getPosition();
                map.panTo(pos);   

                $('input#_wpmm_latitude').val(pos.lat());
                $('input#_wpmm_longitude').val(pos.lng());
            });

    }
    $('#wpmm_geocode_button').click(function() {
        $('#wpmm_loading').show();
        $('#wpmm_geocode_button').attr('disabled', true);
		
        data = {
            action: 'wpmm_get_results',
            wpmm_nonce: wpmm_vars.wpmm_nonce,
            wpmm_address: $('#wpmm_mbe_address').val(),
            wpmm_post_id: wpmm_vars.wpmm_post_id
        };

        $.post(ajaxurl, data, function (response) {

            var lat_lng = jQuery.parseJSON(response);
            $('input#_wpmm_latitude').val(lat_lng.latitude);
            $('input#_wpmm_longitude').val(lat_lng.longitude);
            
            var location = new google.maps.LatLng(lat_lng.latitude,lat_lng.longitude);
            var marker = new google.maps.Marker({
                position: location,
                draggable:true,
                map: map
            });            
            map.setCenter(location);
            google.maps.event.addListener (marker, 'dragend', 
                function (event) {       
                    // Pan the maps center to the markers position
                    // you could do any of your own stuff here
                    var pos = marker.getPosition();
                    map.panTo(pos);   

                    $('input#_wpmm_latitude').val(pos.lat());
                    $('input#_wpmm_longitude').val(pos.lng());
                });
            $('#wpmm_loading').hide();
            $('#wpmm_geocode_button').attr('disabled', false);
        });	
		
        return false;
    });
});