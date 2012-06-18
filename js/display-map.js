jQuery(document).ready(function($) {
    console.log(wpmm_vars);
    if(wpmm_vars != 'null')
        var center = new google.maps.LatLng(wpmm_vars.lat, wpmm_vars.lng);
    else
        var center = new google.maps.LatLng(38.8978881835938, -77.0363311767578);
    
    var myOptions = {
        center: center,
        zoom: 16,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };            
    var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
    var location = new google.maps.LatLng(wpmm_vars.lat,wpmm_vars.lng);
    
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
                if( wpmm_vars.wpmm_post_id != '' ){
                    $('input#_wpmm_latitude').val(pos.lat());
                    $('input#_wpmm_longitude').val(pos.lng());
                }
                else {
                    $('input#wpmm_plugin_map_options\\[default_latitude\\]').val(pos.lat());
                    $('input#wpmm_plugin_map_options\\[default_longitude\\]').val(pos.lng());
                }
            });

    }
    $('#wpmm_geocode_button').click(function() {
        $('#wpmm_loading').show();
        $('#wpmm_geocode_button').attr('disabled', true);
        var address;
        if(wpmm_vars.wpmm_post_id != ''){
            address = $('#wpmm_mbe_address').val();
        } else{
            $input = $('.form-table').find('input[type=text]').filter(':visible:first');
            // address = $('#wpmm_plugin_map_options[default_mapcenter]').val();
            address= $input.val();
        }
        console.log(address);
        data = {
            action: 'wpmm_get_results',
            wpmm_nonce: wpmm_vars.wpmm_nonce,
            wpmm_address: address,
            wpmm_post_id: wpmm_vars.wpmm_post_id
        };

        $.post(ajaxurl, data, function (response) {

            var lat_lng = jQuery.parseJSON(response);
            console.log(lat_lng);       
            // $('input#_wpmm_latitude').val(lat_lng.latitude);
            //$('input#_wpmm_longitude').val(lat_lng.longitude);
            
            if( wpmm_vars.wpmm_post_id != '' ){
                $('input#_wpmm_latitude').val(lat_lng.latitude);
                $('input#_wpmm_longitude').val(lat_lng.latitude);
            }
            else {
                $('input#wpmm_plugin_map_options\\[default_latitude\\]').val(lat_lng.latitude);
                $('input#wpmm_plugin_map_options\\[default_longitude\\]').val(lat_lng.longitude);
            }           

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

                    if( wpmm_vars.wpmm_post_id != '' ){
                        $('input#_wpmm_latitude').val(pos.lat());
                        $('input#_wpmm_longitude').val(pos.lat());
                    }
                    else {
                        $('input#wpmm_plugin_map_options\\[default_latitude\\]').val(pos.lat());
                        $('input#wpmm_plugin_map_options\\[default_longitude\\]').val(pos.lng());
                    } 
                });
            $('#wpmm_loading').hide();
            $('#wpmm_geocode_button').attr('disabled', false);
        });	
		
        return false;
    });
});