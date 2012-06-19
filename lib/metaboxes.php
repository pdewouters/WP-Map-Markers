<?php

/**
 * Include and setup custom metaboxes and fields.
 *
 * @category WP Map Markers
 * @package  Metaboxes
 * @license  http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link     https://github.com/jaredatch/Custom-Metaboxes-and-Fields-for-WordPress
 */
add_filter( 'cmb_meta_boxes', 'cmb_wpmm_metaboxes' );

/**
 * Define the metabox and field configurations.
 *
 * @param  array $meta_boxes
 * @return array
 */
function cmb_wpmm_metaboxes( array $meta_boxes ) {

	// Start with an underscore to hide fields from custom fields list
	$prefix = '_wpmm_';

	$meta_boxes[] = array(
		'id' => 'wpmm_metabox',
		'title' => __('Map Markers Options','wpmm-map-markers'),
		'pages' => array( 'wpmm_location', ), // Post type
		'context' => 'normal',
		'priority' => 'high',
		'show_names' => true, // Show field names on the left
		'fields' => array(
			array(
				'name' => __('Location name','wpmm-map-markers'),
				'desc' => __('the name of the location','wpmm-map-markers'),
				'id' => $prefix . 'location_name',
				'type' => 'text',
			),
			array(
				'name' => __('Display on map','wpmm-map-markers'),
				'desc' => __('check to display this location on the global map','wpmm-map-markers'),
				'id' => $prefix . 'displayonmap',
				'std' => true,
				'type' => 'checkbox',
				
			),
			array(
				'name' => __('Latitude','wpmm-map-markers'),
				'desc' => __('the latitude of the location','wpmm-map-markers'),
				'id' => $prefix . 'latitude',
				'type' => 'text',
			),
			array(
				'name' => __('Longitude','wpmm-map-markers'),
				'desc' => __('the longitude of the location','wpmm-map-markers'),
				'id' => $prefix . 'longitude',
				'type' => 'text',
			),
			array(
				'name' => __('Marker icon','wpmm-map-markers'),
				'desc' => __('select the marker icon','wpmm-map-markers'),
				'id' => $prefix . 'marker_icon',
				'type' => 'radio',
				'options' => array(
					array( 'name' => __('Blue','wpmm-map-markers'), 'value' => 'blue-marker', ),
					array( 'name' => __('Green','wpmm-map-markers'), 'value' => 'green-marker', ),
					array( 'name' => __('Orange','wpmm-map-markers'), 'value' => 'orange-marker', ),
					array( 'name' => __('Yellow','wpmm-map-markers'), 'value' => 'yellow-marker', ),
					array( 'name' => __('Red','wpmm-map-markers'), 'value' => 'red-marker', ),
					array( 'name' => __('Pink','wpmm-map-markers'), 'value' => 'pink-marker', ),
				),
				'std' => 'blue-marker'
			),
		),
	);

	// Add other metaboxes as needed

	return $meta_boxes;
}

add_action( 'init', 'cmb_initialize_cmb_meta_boxes', 9999 );

/**
 * Initialize the metabox class.
 */
function cmb_initialize_cmb_meta_boxes() {

	if ( !class_exists( 'cmb_Meta_Box' ) )
		require_once 'metabox/init.php';
}

add_filter( 'cmb_meta_box_url', 'windows_cmb_meta_box_url' );

function windows_cmb_meta_box_url( $url ) {
	return trailingslashit( str_replace( '\\', '/', str_replace( str_replace( '/', '\\', WP_CONTENT_DIR ), WP_CONTENT_URL, $url ) ) );
}