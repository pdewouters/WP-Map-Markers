<?php
add_action( 'init', 'register_taxonomy_features' );

function register_taxonomy_features() {

    $labels = array( 
        'name' => _x( 'Features', 'Features general name','wpmm' ),
        'singular_name' => _x( 'Features', 'Features singular name','wpmm' ),
        'search_items' => __( 'Search Features', 'wpmm' ),
        'popular_items' => __( 'Popular Features', 'wpmm' ),
        'all_items' => __( 'All Features', 'wpmm' ),
        'parent_item' => __( 'Parent Feature', 'wpmm' ),
        'parent_item_colon' => __( 'Parent Feature:', 'wpmm' ),
        'edit_item' => __( 'Edit Feature', 'wpmm' ),
        'update_item' => __( 'Update Feature', 'wpmm' ),
        'add_new_item' => __( 'Add New Feature', 'wpmm' ),
        'new_item_name' => __( 'New Feature', 'wpmm' ),
        'separate_items_with_commas' => __( 'Separate Features with commas', 'wpmm' ),
        'add_or_remove_items' => __( 'Add or remove Features', 'wpmm' ),
        'choose_from_most_used' => __( 'Choose from the most used Features', 'wpmm' ),
        'menu_name' => __( 'Feature', 'wpmm' ),
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

    register_taxonomy( 'wpmm_feature', array('wpmm_location'), $args );
}

add_action( 'init', 'register_taxonomy_maps' );

function register_taxonomy_maps() {

    $labels = array( 
        'name' => _x( 'Maps', 'Maps general name','wpmm' ),
        'singular_name' => _x( 'Map', 'Map singular name','wpmm' ),
        'search_items' => __( 'Search Maps', 'wpmm' ),
        'popular_items' => __( 'Popular Maps', 'wpmm' ),
        'all_items' => __( 'All Maps', 'wpmm' ),
        'parent_item' => __( 'Parent Map', 'wpmm' ),
        'parent_item_colon' => __( 'Parent Map:', 'wpmm' ),
        'edit_item' => __( 'Edit Map', 'wpmm' ),
        'update_item' => __( 'Update Map', 'wpmm' ),
        'add_new_item' => __( 'Add New Map', 'wpmm' ),
        'new_item_name' => __( 'New Map', 'wpmm' ),
        'separate_items_with_commas' => __( 'Separate Maps with commas', 'wpmm' ),
        'add_or_remove_items' => __( 'Add or remove Maps', 'wpmm' ),
        'choose_from_most_used' => __( 'Choose from the most used Maps', 'wpmm' ),
        'menu_name' => __( 'Maps', 'wpmm' ),
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

    register_taxonomy( 'wpmm_map', array('wpmm_location'), $args );
}