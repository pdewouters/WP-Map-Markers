<?php

/*
  Plugin Name: WP Map markers
  Plugin URI: http://wpconsult.net/wp-map-markers
  Description: Allows you to mark your store locations on a Google map. Searchable and gives driving directions. Uses geolocation.
  Version: 0.0.1
  Author: Paul de Wouters
  Author URI: http://wpconsult.net
  License: GPLv2
 */

/*  Copyright 2012  Paul de Wouters - WpConsult

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 2 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/* TODO */
/*
 * localization
 */

// Load required files




define( 'MAP_API_KEY', 'AIzaSyDHje59oiWoK8WCgVdN1zrxrIGqrW9cTiQ' );

/* Register activation hook. */
register_activation_hook( __FILE__, 'wpmm_activation' );

function wpmm_activation() {
	$role = get_role( 'administrator' );

	if ( !empty( $role ) ) {
		$role->add_cap( 'wpmm_unique_capability' );
	}
}

add_action( 'plugins_loaded', 'wpmm_plugin_setup' );

// Initialize plugin
function wpmm_plugin_setup() {

	/* Set constant path to the WPMM plugin directory. */
	define( 'WPMM_DIR', plugin_dir_path( __FILE__ ) );

	/* Set constant path to the WPMM plugin URL. */
	define( 'WPMM_URL', plugin_dir_url( __FILE__ ) );
	require_once WPMM_DIR . '/lib/wpmm-location-geocode-metabox.php';
	require_once WPMM_DIR . '/lib/post-types.php';
	require_once WPMM_DIR . '/lib/taxonomies.php';
	require_once WPMM_DIR . '/lib/metaboxes.php';
	require_once WPMM_DIR . '/lib/shortcodes.php';


	if ( is_admin() ) {

		/* Load translations. */
		load_plugin_textdomain( 'map-markers', false, 'wp-map-markers/languages' );

		/* Load the plugin's admin file. */
		require_once WPMM_DIR . '/lib/admin.php';
	}


	add_action( 'admin_enqueue_scripts', 'wpmm_enqueue_scripts' );

	add_shortcode( 'wpmm_map', 'wpmm_do_main_map' );
}

// Load necessary javascript and CSS files
function wpmm_enqueue_scripts( $hook ) {
	$post_type = get_current_screen()->id; // when on post.php or post-new.php
	
	if ( ($hook != 'edit.php') && ($hook != 'post.php') && ($hook != 'post-new.php') && ('wpmm_location' != $post_type) )
		return;

	global $post;

	$lat = get_post_meta( $post->ID, '_wpmm_latitude' );
	$lng = get_post_meta( $post->ID, '_wpmm_longitude' );


	wp_enqueue_script( 'gmaps', 'https://maps.googleapis.com/maps/api/js?key=' . MAP_API_KEY . '&sensor=false' );
	wp_enqueue_script( 'display-map', 'http://localhost/wptest/wp-content/plugins/wp-map-markers/js/display-map.js', array( 'jquery' ) );
	wp_localize_script( 'display-map', 'wpmm_vars', array(
		'wpmm_nonce' => wp_create_nonce( 'wpmm-nonce' ),
		'wpmm_post_id' => $post->ID,
		'lat' => $lat,
		'lng' => $lng
			)
	);
}

// This function gets the stores from the database
function wpmm_fetch_stores() {

	// fetch location posts which have a latitude, meaning geocoding works
	$args = array(
		'post_type' => 'wpmm_location',
		'post_status' => 'publish',
		'meta_query' => array(
			array(
				'key' => '_wpmm_latitude',
				'value' => '',
				'compare' => '!='
			),
			array(
				'key' => '_wpmm_displayonmap',
				'value' => '',
				'compare' => '!='
			)
		)
	);
	$locations = get_posts( $args );

	if ( !$locations )
		return;
	// fetch posts custom meta fields vaules
	foreach ( $locations as $location ) {
		// get features
		$features = wpmm_get_location_features( $location );
		$store_id = $location->ID;
		$store_name = sanitize_text_field( get_post_meta( $location->ID, '_wpmm_location_name', true ) );
		$store_lat = sanitize_text_field( get_post_meta( $location->ID, '_wpmm_latitude', true ) );
		$store_lng = sanitize_text_field( get_post_meta( $location->ID, '_wpmm_longitude', true ) );
		$store_address = sanitize_text_field( get_post_meta( $location->ID, '_wpmm_address', true ) );
		$store_marker =  WPMM_URL . '/images/' . get_post_meta( $location->ID, '_wpmm_marker_icon', true ) . '.png' ;

		$store_permalink = get_permalink( $location->ID );
		$stores[] = array(
			'id' => $store_id,
			'store_name' => $store_name,
			'store_lat' => $store_lat,
			'store_lng' => $store_lng,
			'address' => $store_address,
			'store_permalink' => $store_permalink,
			'store_marker' => $store_marker,
			'store_features' => $features
		);
	}

	return $stores;
}

function wpmm_global_settings() {
	$options = get_option( 'wpmm_plugin_map_options' );
	//print_r($options);die();
	// TODO fetch this from settings page
	//$marker_icon = WPMM_URL . 'images/blue-marker.png';
	$marker_shadow = WPMM_URL . 'images/marker-shadow.png';

	if ( $options['default_zoom'] )
		$default_zoom = sanitize_text_field( $options['default_zoom'] );
	else
		$default_zoom = 8;
	if ( $options['default_latitude'] )
		$map_center_lat = sanitize_text_field( $options['default_latitude'] );
	else
		$map_center_lat = 38.8978881835938;
	if ( $options['default_longitude'] )
		$map_center_lng = sanitize_text_field( $options['default_longitude'] );
	else
		$map_center_lng = -77.0363311767578;

	if ( $options['map_type'] )
		$map_type = sanitize_text_field( $options['map_type'] );
	else
		$map_type = 'google.maps.MapTypeId.ROADMAP';


	$wpmm_settings[] = array(
		'marker_shadow' => $marker_shadow,
		'default_zoom' => $default_zoom,
		'map_center_lat' => $map_center_lat,
		'map_center_lng' => $map_center_lng,
		'map_type' => 'google.maps.MapTypeId.' . strtoupper( $map_type )
	);
	return $wpmm_settings;
}

function wpmm_get_location_features( $post ) {
	//Returns Array of Term Names for "my_term"
	$term_list = wp_get_post_terms( $post->ID, 'wpmm_feature', array( "fields" => "all" ) );
	return $term_list;
}

function wpmm_get_all_features() {

	//returns all terms fron the taxonomy features
	$features = get_terms( 'wpmm_feature' );
	return $features;
	//print_r($features);die();
}