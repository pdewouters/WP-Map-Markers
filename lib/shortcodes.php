<?php

function wpmm_do_display_map( $attr ) {
	global $wp_query;
	$options = get_option( 'wpmm_plugin_map_options' );
	// don't display maps on archive pages because multiple maps don't work
	if ( !isset( $attr['map'] ) || is_home() || is_archive() )
		return;
	
		$term = get_term_by( 'id', $attr['map'], 'wpmm_map' );
		$term_id = $term->term_id;
		$term_slug = $term->slug;
	$stores = json_encode( wpmm_fetch_stores( $term_slug ) );

	if ( 'null' != $stores ) {
		// get map coordinates

		
		$lat = get_tax_meta( $term_id, 'wpmm_map_tax_default_lat' );
		$lng = get_tax_meta( $term_id, 'wpmm_map_tax_default_lng' );
		
		if ( $lat == '' || $lng == '' ) {
			// map coordinates not set, so set default
			$lat = $options['default_latitude'];
			$lng = $options['default_longitude'];
		}
		
		// load front end scripts only when shortcode is present
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


		$wpmm_settings = json_encode( $options );
		wp_localize_script( 'panel-js', 'wpmm_panel_vars', array(
			'wpmm_settings' => $wpmm_settings,
			'lat' => $lat,
			'lng' => $lng
		) );
		
		// Loads the Store Locator default CSS styles
		wp_enqueue_style( 'store-locator-style-css', WPMM_URL . 'css/storelocator.css' );

		return '<div id="wpmm-container"><div id="panel"></div><div id="map-canvas"></div></div>';
	} else {
		return __( 'No locations found', 'wpmm-map-markers' );
	}
}