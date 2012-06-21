jQuery(document).ready(function($) {
        
    var center;
    if(wpmm_vars != 'null')
        center = new google.maps.LatLng(wpmm_vars.lat, wpmm_vars.lng);
    else
        center = new google.maps.LatLng(38.8978881835938, -77.0363311767578);
    
    var myOptions = {
        center: center,
        zoom: 16,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };            
    var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
    var is_new_location = false;
    var marker;
    if( $('input#wpmm_mbe_address').length ){
        current_address = $('input#wpmm_mbe_address').val();
    } else if($('input#wpmm_plugin_map_options\\[default_mapcenter\\]').length){
        current_address = $('input#wpmm_plugin_map_options\\[default_mapcenter\\]').val();
    } else {
        current_address = 'wrong';
    }
    // get address field value to compare later
    if(current_address == "") {
        is_new_location = true;
    } else {
        is_new_location = false;
            var location = new google.maps.LatLng(wpmm_vars.lat,wpmm_vars.lng);
             marker = new google.maps.Marker({
                position: location,
                draggable:true,
                map: map
            });            
            map.setCenter(location);        
        
    }


    $('#wpmm_geocode_button').click(function() {
        
        if(is_new_location){
                if( $('input#wpmm_mbe_address').length ){
        current_address = $('input#wpmm_mbe_address').val();
    } else if($('input#wpmm_plugin_map_options\\[default_mapcenter\\]').length){
        current_address = $('input#wpmm_plugin_map_options\\[default_mapcenter\\]').val();
    } else {
        current_address = 'wrong';
    }
        }
  

        
        var new_address ;
        if(typeof current_address == 'undefined' || null == current_address || current_address == "" )
            return;
        
        if( $('input#wpmm_mbe_address').length ){
            if($('input#wpmm_mbe_address').val() != current_address || is_new_location) {
                new_address = $('input#wpmm_mbe_address').val();
                current_address = new_address;
                is_new_location = false;
            } else {
                return; //nothing to do
            }
        } else if($('input#wpmm_plugin_map_options\\[default_mapcenter\\]').length){
            if($('input#wpmm_plugin_map_options\\[default_mapcenter\\]').val() != current_address || is_new_location) {
                new_address = $('input#wpmm_plugin_map_options\\[default_mapcenter\\]').val();
                current_address = new_address;
                is_new_location = false;
            } else {
                return; //nothing to do
            }     
        } else {
            new_address = 'no_val';
        };

        
        data = {
            action: 'wpmm_get_results',
            wpmm_nonce: wpmm_vars.wpmm_nonce,
            wpmm_address: new_address,
            wpmm_post_id: wpmm_vars.wpmm_post_id,
            wpmm_current_address: wpmm_vars.current_address,
            wpmm_address_field: wpmm_vars.address_field
        };

        $.post(ajaxurl, data, function (response) {
 

            var wpmm_response_vars = jQuery.parseJSON(response);

            $('#wpmm_loading').show();
            $('#wpmm_geocode_button').attr('disabled', true);
            var lat_lng = wpmm_response_vars.lat_lng;
            
            
            if( wpmm_vars.wpmm_post_id != '' ){
                $('input#_wpmm_latitude').val(lat_lng.latitude);
                $('input#_wpmm_longitude').val(lat_lng.longitude);
            }
            else {
                $('input#wpmm_plugin_map_options\\[default_latitude\\]').val(lat_lng.latitude);
                $('input#wpmm_plugin_map_options\\[default_longitude\\]').val(lat_lng.longitude);
            }           

            var location = new google.maps.LatLng(lat_lng.latitude,lat_lng.longitude);
            if(typeof marker == 'undefined'){
                             marker = new google.maps.Marker({
                position: location,
                draggable:true,
                map: map
            });  
            }
            marker.setPosition(location);         
            map.setCenter(location);

            $('#wpmm_loading').hide();
            $('#wpmm_geocode_button').attr('disabled', false);



        });	
        
        return false;
    });
                google.maps.event.addListener (marker, 'dragend', 
                function (event) {       
                    // Pan the maps center to the markers position
                    // you could do any of your own stuff here
                    var pos = marker.getPosition();
                    map.panTo(pos);   

                    if( $('input#_wpmm_latitude').length ){
                        $('input#_wpmm_latitude').val(pos.lat());
                        $('input#_wpmm_longitude').val(pos.lng());
                    }
                    else {
                        $('input#wpmm_plugin_map_options\\[default_latitude\\]').val(pos.lat());
                        $('input#wpmm_plugin_map_options\\[default_longitude\\]').val(pos.lng());
                    } 
                });
});