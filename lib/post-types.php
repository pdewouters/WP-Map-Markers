<?php
/**
 * Example for writing a WP plugin that adds a custom post type and flushes
 * rewrite rules only once on initialization.
 */

/**
 * On activation, we'll set an option called 'my_plugin_name_flush' to true,
 * so our plugin knows, on initialization, to flush the rewrite rules.
 */
function wpmm_cpt_activation() {
    add_option( 'wpmm_flush', 'true' );
}

register_activation_hook( WPMM_FILE, 'wpmm_cpt_activation' );

/**
 * On deactivation, we'll remove our 'my_plugin_name_flush' option if it is
 * still around. It shouldn't be after we register our post type.
 */
function wpmm_cpt_deactivation() {
    delete_option( 'wpmm_flush' );
}

register_deactivation_hook( WPMM_FILE , 'wpmm_cpt_deactivation' );

// Register the post type for locations
add_action( 'init', 'register_cpt_wpmm_location' );

function register_cpt_wpmm_location() {

	$labels = array(
		'name' => _x( 'Locations', 'post type general name', 'wpmm-map-markers' ),
		'singular_name' => _x( 'Location', 'post type singular name', 'wpmm-map-markers' ),
		'add_new' => __( 'Add New', 'wpmm-map-markers' ),
		'add_new_item' => __( 'Add New Location', 'wpmm-map-markers' ),
		'edit_item' => __( 'Edit Location', 'wpmm-map-markers' ),
		'new_item' => __( 'New Location', 'wpmm-map-markers' ),
		'view_item' => __( 'View Location', 'wpmm-map-markers' ),
		'search_items' => __( 'Search Locations', 'wpmm-map-markers' ),
		'not_found' => __( 'No locations found', 'wpmm-map-markers' ),
		'not_found_in_trash' => __( 'No locations found in Trash', 'wpmm-map-markers' ),
		'parent_item_colon' => __( 'Parent Locations:', 'wpmm-map-markers' ),
		'menu_name' => __( 'Locations', 'wpmm-map-markers' ),
	);

	$args = array(
		'labels' => $labels,
		'hierarchical' => false,
		'description' => __('Represents an entity that can be represented as a location on a Google Map','wpmm-map-markers'),
		'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions' ),
		'public' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'show_in_nav_menus' => true,
		'publicly_queryable' => true,
		'exclude_from_search' => false,
		'has_archive' => false,
		'query_var' => true,
		'can_export' => true,
		'rewrite' => array( 'slug' => 'locations'),
		'capability_type' => 'post'
	);

	register_post_type( 'wpmm_location', $args );

	    // Check the option we set on activation.
    if (get_option('wpmm_flush') == 'true') {
        flush_rewrite_rules();
        delete_option('wpmm_flush');
    }
}

