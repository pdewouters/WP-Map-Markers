<?php

function wpmm_do_main_map( $attr ) {
	global $wp_query;
	//echo '<pre>' . var_dump($wp_query) . '</pre>';
	if ( ! isset( $attr['map'] ) ||  is_home() || is_archive() )
		return;

	$stores = json_encode( wpmm_fetch_stores( $attr['map'] ) );
	
	// don't display maps on archive pages because multiple maps don't work
	if ( 'null' != $stores ) {
		// Load the maps API
		// http://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&sensor=SET_TO_TRUE_OR_FALSE
		wp_enqueue_script( 'maps-api-js', 'http://maps.googleapis.com/maps/api/js?sensor=false&libraries=places' );

		wp_enqueue_script( 'infobubble-js', 'http://google-maps-utility-library-v3.googlecode.com/svn/trunk/infobubble/src/infobubble-compiled.js', array( 'jquery', 'maps-api-js' ) );
		// Loads the Google Store Locator library, depends on jQuery
		wp_enqueue_script( 'store-locator-js', WPMM_URL . 'js/store-locator.compiled.js', array( 'jquery', 'maps-api-js', 'infobubble-js' ) );

		// Loads the data handler script
		wp_enqueue_script( 'data-feed-js', WPMM_URL . 'js/wpmm-static-ds.js', array( 'jquery', 'store-locator-js', 'maps-api-js', 'infobubble-js' ) );

		wp_localize_script( 'data-feed-js', 'wpmm_stores', $stores );

		$wpmm_all_features = json_encode( wpmm_get_all_features() );
		wp_localize_script( 'data-feed-js', 'wpmm_features', $wpmm_all_features );
		// Loads the panel script
		wp_enqueue_script( 'panel-js', WPMM_URL . 'js/panel.js', array( 'jquery', 'store-locator-js', 'maps-api-js', 'infobubble-js' ) );
		$options = get_option( 'wpmm_plugin_map_options' );

		$wpmm_settings = json_encode( $options );
		wp_localize_script( 'panel-js', 'wpmm_settings', $wpmm_settings );

		// Loads the Store Locator default CSS styles
		wp_enqueue_style( 'store-locator-style-css', WPMM_URL . 'css/storelocator.css' );

		return '<div id="panel"></div><div id="map-canvas"></div>';
	} else {
		return __( 'No stores', 'wpmm' );
	}
}