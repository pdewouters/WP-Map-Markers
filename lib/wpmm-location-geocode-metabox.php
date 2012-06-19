<?php
//hook to add a meta box
add_action( 'add_meta_boxes', 'wpmm_mbe_create' );

function wpmm_mbe_create() {
	global $wpmm_metabox;
	//create a custom meta box
	$wpmm_metabox = add_meta_box( 'wpmm-meta', __( 'Address', 'wpmm-map-markers' ), 'wpmm_mbe_function', 'wpmm_location', 'normal', 'high' );
}

function wpmm_mbe_function( $post ) {

	//retrieve the meta data values if they exist
	$wpmm_mbe_address = get_post_meta( $post->ID, '_wpmm_mbe_address', true );

	_e( 'Please fill out the information below', 'wpmm-map-markers' );
	?>
	<p>
		<?php _e( 'Address:', 'wpmm-map-markers' ); ?> 
		<input type="text" class="widefat" id="wpmm_mbe_address" name="wpmm_mbe_address" value="<?php echo esc_attr( $wpmm_mbe_address ); ?>" />
	</p>
	<form id="wpmm-form" action="" method="POST">
		<div>
			<input id="wpmm_geocode_button" class="button" type="button" value="Geocode address" />
			<img src="<?php echo admin_url( '/images/wpspin_light.gif' ); ?>" class="waiting" id="wpmm_loading" style="display:none;"/>
		</div>
	</form>
	<div style="width: 100%;height:300px;"><div id="map_canvas" style="width:100%; height:100%"></div></div>
	<?php
}

//hook to save the meta box data
add_action( 'save_post', 'wpmm_mbe_save_meta' );

function wpmm_mbe_save_meta( $post_id ) {

	//verify the meta data is set
	if ( isset( $_POST['wpmm_mbe_address'] ) ) {

		//save the meta data
		update_post_meta( $post_id, '_wpmm_mbe_address', strip_tags( $_POST['wpmm_mbe_address'] ) );
	}
}

function wpmm_process_ajax() {

	// Geocode button was clicked
	// verify the nonce field
	if ( !isset( $_POST['wpmm_nonce'] ) || !wp_verify_nonce( $_POST['wpmm_nonce'], 'wpmm-nonce' ) )
		die( 'Permissions check failed' );

	// clicking geocode address calls the click event on the button in 
	// display-map.js which makes the address available to this function for processing

	if ( isset( $_POST['wpmm_address'] ) )
		$new_address = strip_tags( $_POST['wpmm_address'] );
	if ( isset( $_POST['wpmm_current_address'] ) )
		$current_address = strip_tags( $_POST['wpmm_current_address'] );
	if ( isset( $_POST['wpmm_address_field'] ) )
		$address_field = strip_tags( $_POST['wpmm_address_field'] );

	if ( $address_field != '' ) {
		if ( $new_address != $current_address ) {
			// address field value changed so process
			$lat_lng = wpmm_do_geocode_address( $new_address );
			$response = array( 'changed' => true, 'lat_lng' => $lat_lng );
		} else {
			$response = array( 'changed' => false, 'lat_lng' => '' );
		}
	} else {
		// address field is still empty so put a default val
	}

	echo json_encode( $response );
	die();
}

add_action( 'wp_ajax_wpmm_get_results', 'wpmm_process_ajax' );

// Geocodes an address and returns an array with long / lat


function wpmm_do_geocode_address( $address ) {

	// Send GET request to Google Maps API
	$api_url = 'http://maps.googleapis.com/maps/api/geocode/json?sensor=false&address=';
	$api_response = wp_remote_get( $api_url . urlencode( $address ) );

	//Get the JSON object
	$json = wp_remote_retrieve_body( $api_response );

	// make sure request was successful or return false
	if ( empty( $json ) )
		return false;

	// decode the JSON object 
	// return an array with lat / long
	$json = json_decode( $json );
	$lat_lng = array(
		'latitude' => $json->results[0]->geometry->location->lat,
		'longitude' => $json->results[0]->geometry->location->lng
	);

	return $lat_lng;
}
