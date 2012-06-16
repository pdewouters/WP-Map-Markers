<?php
//hook to add a meta box
add_action( 'add_meta_boxes', 'wpmm_mbe_create' );

function wpmm_mbe_create() {
	global $wpmm_metabox;
	//create a custom meta box
	$wpmm_metabox = add_meta_box( 'wpmm-meta', __( 'Address', 'wpmm' ), 'wpmm_mbe_function', 'wpmm_location', 'normal', 'high' );
}

function wpmm_mbe_function( $post ) {

	//retrieve the meta data values if they exist
	$wpmm_mbe_address = get_post_meta( $post->ID, '_wpmm_mbe_address', true );

	echo 'Please fill out the information below';
	?>
	<p>Address: <input type="text" class="widefat" id="wpmm_mbe_address" name="wpmm_mbe_address" value="<?php echo esc_attr( $wpmm_mbe_address ); ?>" /></p>
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

function wpmm_load_scripts( $hook ) {
	//global $post;
	//$post_type = get_current_screen()->id; // when on post.php or post-new.php

	//if ( ($hook != 'edit.php') && ($hook != 'post.php') && ($hook != 'post-new.php') && ('wpmm_location' != $post_type) )
	//	return;
	//wp_enqueue_script( 'maps-api-js', 'https://maps.googleapis.com/maps/api/js?key=' . MAP_API_KEY . '&sensor=false' );
	//wp_enqueue_script( 'wpmm-ajax', 'http://localhost/wptest/wp-content/plugins/wp-map-markers/js/wpmm-ajax.js', array( 'jquery' ) );
	//wp_localize_script( 'wpmm-ajax', 'wpmm_vars', array(
	//	'wpmm_nonce' => wp_create_nonce( 'wpmm-nonce' ),
	//	'wpmm_post_id' => $post->ID
	//		)
	//);
}

//add_action( 'admin_enqueue_scripts', 'wpmm_load_scripts' );

function wpmm_process_ajax() {

	if ( !isset( $_POST['wpmm_address'] ) || !isset( $_POST['wpmm_nonce'] ) || !wp_verify_nonce( $_POST['wpmm_nonce'], 'wpmm-nonce' ) )
		die( 'Permissions check failed' );


	$lat_lng = do_geocode_address( $_POST['wpmm_address'], $_POST['wpmm_post_id'] );
	
	echo json_encode($lat_lng);
	die();
}

add_action( 'wp_ajax_wpmm_get_results', 'wpmm_process_ajax' );

// Geocodes an address and returns an array with long / lat


function do_geocode_address( $address, $post_id ) {

	
	if( get_post_meta( $post_id , '_wpmm_latitude')){
			$lat_long = array(
		'latitude' => get_post_meta( $post_id ,'_wpmm_latitude'),
		'longitude' => get_post_meta( $post_id ,'_wpmm_longitude')
	);
	}
			
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
	$lat_long = array(
		'latitude' => $json->results[0]->geometry->location->lat,
		'longitude' => $json->results[0]->geometry->location->lng
	);

	return $lat_long;
}
