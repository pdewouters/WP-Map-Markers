<?php

add_action( 'init', 'register_taxonomy_features' );

function register_taxonomy_features() {

	$labels = array(
		'name' => _x( 'Features', 'Features general name', 'wpmm-map-markers' ),
		'singular_name' => _x( 'Features', 'Features singular name', 'wpmm-map-markers' ),
		'search_items' => __( 'Search Features', 'wpmm-map-markers' ),
		'popular_items' => __( 'Popular Features', 'wpmm-map-markers' ),
		'all_items' => __( 'All Features', 'wpmm-map-markers' ),
		'parent_item' => __( 'Parent Feature', 'wpmm-map-markers' ),
		'parent_item_colon' => __( 'Parent Feature:', 'wpmm-map-markers' ),
		'edit_item' => __( 'Edit Feature', 'wpmm-map-markers' ),
		'update_item' => __( 'Update Feature', 'wpmm-map-markers' ),
		'add_new_item' => __( 'Add New Feature', 'wpmm-map-markers' ),
		'new_item_name' => __( 'New Feature', 'wpmm-map-markers' ),
		'separate_items_with_commas' => __( 'Separate Features with commas', 'wpmm-map-markers' ),
		'add_or_remove_items' => __( 'Add or remove Features', 'wpmm-map-markers' ),
		'choose_from_most_used' => __( 'Choose from the most used Features', 'wpmm-map-markers' ),
		'menu_name' => __( 'Feature', 'wpmm-map-markers' ),
	);

	$args = array(
		'labels' => $labels,
		'public' => true,
		'show_in_nav_menus' => true,
		'show_ui' => true,
		'show_tagcloud' => true,
		'hierarchical' => true,
		'rewrite' => true,
		'query_var' => true
	);

	register_taxonomy( 'wpmm_feature', array( 'wpmm_location' ), $args );
}

add_action( 'init', 'register_taxonomy_maps' );

function register_taxonomy_maps() {

	$labels = array(
		'name' => _x( 'Maps', 'Maps general name', 'wpmm-map-markers' ),
		'singular_name' => _x( 'Map', 'Map singular name', 'wpmm-map-markers' ),
		'search_items' => __( 'Search Maps', 'wpmm-map-markers' ),
		'popular_items' => __( 'Popular Maps', 'wpmm-map-markers' ),
		'all_items' => __( 'All Maps', 'wpmm-map-markers' ),
		'parent_item' => __( 'Parent Map', 'wpmm-map-markers' ),
		'parent_item_colon' => __( 'Parent Map:', 'wpmm-map-markers' ),
		'edit_item' => __( 'Edit Map', 'wpmm-map-markers' ),
		'update_item' => __( 'Update Map', 'wpmm-map-markers' ),
		'add_new_item' => __( 'Add New Map', 'wpmm-map-markers' ),
		'new_item_name' => __( 'New Map', 'wpmm-map-markers' ),
		'separate_items_with_commas' => __( 'Separate Maps with commas', 'wpmm-map-markers' ),
		'add_or_remove_items' => __( 'Add or remove Maps', 'wpmm-map-markers' ),
		'choose_from_most_used' => __( 'Choose from the most used Maps', 'wpmm-map-markers' ),
		'menu_name' => __( 'Maps', 'wpmm-map-markers' ),
	);

	$args = array(
		'labels' => $labels,
		'public' => true,
		'show_in_nav_menus' => true,
		'show_ui' => true,
		'show_tagcloud' => true,
		'hierarchical' => true,
		'rewrite' => true,
		'query_var' => true
	);

	register_taxonomy( 'wpmm_map', array( 'wpmm_location' ), $args );
}