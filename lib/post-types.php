<?php

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
		'rewrite' => true,
		'capability_type' => 'post'
	);

	register_post_type( 'wpmm_location', $args );
}

function wpmm_rewrite_flush() {
	// First, we "add" the custom post type via the above written function.
	// Note: "add" is written with quotes, as CPTs don't get added to the DB,
	// They are only referenced in the post_type column with a post entry, 
	// when you add a post of this CPT.
	register_cpt_wpmm_location();

	// ATTENTION: This is *only* done during plugin activation hook in this example!
	// You should *NEVER EVER* do this on every page load!!
	flush_rewrite_rules();
}

register_activation_hook( WPMM_FILE, 'wpmm_rewrite_flush' );