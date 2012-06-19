<?php

/*
  Plugin Name: WP Map markers
  Plugin URI: http://wpmapmarkers.com
  Description: Allows you to mark your store locations on a Google map. Searchable and gives driving directions. Uses geolocation.
  Version: 0.9
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

if ( version_compare(PHP_VERSION, '5.2', '<') ) {
	if ( is_admin() && (!defined('DOING_AJAX') || !DOING_AJAX) ) {
		require_once ABSPATH.'/wp-admin/includes/plugin.php';
		deactivate_plugins( __FILE__ );
	    wp_die( __('WP Map Markers requires PHP 5.2 or higher, as does WordPress 3.2 and higher. The plugin has now disabled itself.', 'wpmm-map-markers') );
	} else {
		return;
	}
}

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

	add_image_size( 'store-thumb', 50, 50, true );

	/* Set constant path to the WPMM plugin directory. */
	define( 'WPMM_DIR', plugin_dir_path( __FILE__ ) );

	/* Set constant path to the WPMM plugin URL. */
	define( 'WPMM_URL', plugin_dir_url( __FILE__ ) );

	require_once WPMM_DIR . '/lib/map-taxo-meta.php';
	require_once WPMM_DIR . '/lib/wpmm-location-geocode-metabox.php';
	require_once WPMM_DIR . '/lib/post-types.php';
	require_once WPMM_DIR . '/lib/taxonomies.php';
	require_once WPMM_DIR . '/lib/metaboxes.php';
	require_once WPMM_DIR . '/lib/shortcodes.php';


	if ( is_admin() ) {

		/* Load translations. */
		load_plugin_textdomain( 'wpmm-map-markers', false, 'wp-map-markers/languages' );

		/* Load the plugin's admin file. */
		require_once WPMM_DIR . '/lib/admin.php';
	}


	add_action( 'admin_enqueue_scripts', 'wpmm_enqueue_scripts' );

	add_shortcode( 'wpmm_map', 'wpmm_do_display_map' );
}

// Load necessary javascript and CSS files
function wpmm_enqueue_scripts( $hook ) {
	global $post_type;
	
	// only load scripts when necessary
	if ( $hook == 'settings_page_wpmm-settings' || (($hook == 'post.php') || ($hook == 'post-new.php')) && ('wpmm_location' == $post_type) ) {
		$marker_vars = wpmm_get_initial_marker_location( $hook );
		wp_enqueue_script( 'gmaps', 'https://maps.googleapis.com/maps/api/js?sensor=false' );
		wp_enqueue_script( 'display-map', WPMM_URL . 'js/display-map.js', array( 'jquery' ) );
		wp_localize_script( 'display-map', 'wpmm_vars', array(
			'wpmm_nonce' => wp_create_nonce( 'wpmm-nonce' ),
			'wpmm_post_id' => $marker_vars['post_id'],
			'lat' => $marker_vars['latitude'],
			'lng' => $marker_vars['longitude'],
			'current_address' => $marker_vars['current_address'],
			'address_field' => $marker_vars['address_field']
				)
		);
	}
}

// This function gets the stores from the database
function wpmm_fetch_stores( $map ) {

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
			),
		),
		'tax_query' => array(
			array(
				'taxonomy' => 'wpmm_map',
				'field' => 'slug',
				'terms' => $map
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
		$store_address = sanitize_text_field( get_post_meta( $location->ID, '_wpmm_mbe_address', true ) );
		$store_marker = WPMM_URL . '/images/' . get_post_meta( $location->ID, '_wpmm_marker_icon', true ) . '.png';
		$store_permalink = get_permalink( $location->ID );
		$store_thumbnail = '';
		if ( has_post_thumbnail( $location->ID ) ) {
			$store_thumbnail = get_the_post_thumbnail( $location->ID, 'store-thumb' );
		}

		$stores[] = array(
			'id' => $store_id,
			'store_name' => $store_name,
			'store_lat' => $store_lat,
			'store_lng' => $store_lng,
			'address' => $store_address,
			'store_permalink' => $store_permalink,
			'store_marker' => $store_marker,
			'store_thumb' => $store_thumbnail,
			'store_features' => $features
		);
	}

	return $stores;
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
}

function wpmm_get_initial_marker_location( $hook ) {

	$is_settings_page = false;
	$post_type = get_current_screen()->id; // when on post.php or post-new.php
	$options = get_option( 'wpmm_plugin_map_options' );


	if ( $hook == 'settings_page_wpmm-settings' ) {
		// WPMM settings page
		$is_settings_page = true;
	} elseif ( (($hook == 'post.php') || ($hook == 'post-new.php')) && ('wpmm_location' == $post_type) ) {
		global $post;

		// set address text field and current address
		$address_text_field_id = '#wpmm_mbe_address';
		if ( get_post_meta( $post->ID, '_wpmm_mbe_address' ) )
			$current_address = get_post_meta( $post->ID, '_wpmm_mbe_address', true );
		else
			$current_address = '';

		// set map center from location address
		if ( get_post_meta( $post->ID, '_wpmm_latitude', true ) && get_post_meta( $post->ID, '_wpmm_longitude', true ) ) {
			// already has address
			$lat = get_post_meta( $post->ID, '_wpmm_latitude', true );
			$lng = get_post_meta( $post->ID, '_wpmm_longitude', true );
		} else {
			// no address set, get map center from global settings
			if ( $options['default_latitude'] ) {
				$lat = sanitize_text_field( $options['default_latitude'] );
			} else {
				$lat = 38.898748;
			}
			if ( $options['default_longitude'] ) {
				$lng = sanitize_text_field( $options['default_longitude'] );
			} else {
				$lng = -77.037684;
			}
		}
	} else {
		return;
	}


	if ( $is_settings_page ) {
		$post_id = '';

		// set address text field and current address
		$address_text_field_id = 'input#wpmm_plugin_map_options[default_mapcenter]';
		if ( sanitize_text_field( $options['default_mapcenter'] ) )
			$current_address = sanitize_text_field( $options['default_mapcenter'] );
		else
			$current_address = '';

		if ( $options['default_latitude'] ) {
			$lat = sanitize_text_field( $options['default_latitude'] );
		} else {
			$lat = 38.898748;
		}
		if ( $options['default_longitude'] ) {
			$lng = sanitize_text_field( $options['default_longitude'] );
		} else {
			$lng = -77.037684;
		}
	} else {
		$post_id = $post->ID;
	}

	$marker_vars = array(
		'latitude' => $lat,
		'longitude' => $lng,
		'post_id' => $post_id,
		'current_address' => $current_address,
		'address_field' => $address_text_field_id
	);
	return $marker_vars;
}


/* Display a notice that can be dismissed */
add_action('admin_notices', 'wpmm_admin_notice');

function wpmm_admin_notice() {
	$settings_page = admin_url() . 'options-general.php?page=wpmm-settings';
	global $current_user ;
	$user_id = $current_user->ID;
	/* Check that the user hasn't already clicked to ignore the message */
	if ( ! get_user_meta($user_id, 'wpmm_ignore_notice') ) {
		echo '<div class="updated"><p>';
		printf(__('Plugin activated. Please check the %1$ssettings%2$s. | %3$sDismiss%4$s','wpmm-map-markers'), '<a href="' . $settings_page . '">', '</a>','<a href="?wpmm_nag_ignore=0">','</a>');
		echo "</p></div>";
	}
}

add_action('admin_init', 'wpmm_nag_ignore');

function wpmm_nag_ignore() {
	global $current_user;
	$user_id = $current_user->ID;
	/* If user clicks to ignore the notice, add that to their user meta */
	if ( isset($_GET['wpmm_nag_ignore']) && '0' == $_GET['wpmm_nag_ignore'] ) {
		add_user_meta($user_id, 'wpmm_ignore_notice', 'true', true);
	}
}