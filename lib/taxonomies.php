<?php
add_action( 'init', 'register_taxonomy_wpmm_feature' );

function register_taxonomy_wpmm_feature() {

    $labels = array( 
        'name' => _x( 'Features', 'wpmm_feature' ),
        'singular_name' => _x( 'Feature', 'wpmm_feature' ),
        'search_items' => _x( 'Search Features', 'wpmm_feature' ),
        'popular_items' => _x( 'Popular Features', 'wpmm_feature' ),
        'all_items' => _x( 'All Features', 'wpmm_feature' ),
        'parent_item' => _x( 'Parent Feature', 'wpmm_feature' ),
        'parent_item_colon' => _x( 'Parent Feature:', 'wpmm_feature' ),
        'edit_item' => _x( 'Edit Feature', 'wpmm_feature' ),
        'update_item' => _x( 'Update Feature', 'wpmm_feature' ),
        'add_new_item' => _x( 'Add New Feature', 'wpmm_feature' ),
        'new_item_name' => _x( 'New Feature', 'wpmm_feature' ),
        'separate_items_with_commas' => _x( 'Separate features with commas', 'wpmm_feature' ),
        'add_or_remove_items' => _x( 'Add or remove Features', 'wpmm_feature' ),
        'choose_from_most_used' => _x( 'Choose from most used Features', 'wpmm_feature' ),
        'menu_name' => _x( 'Features', 'wpmm_feature' ),
    );

    $args = array( 
        'labels' => $labels,
        'public' => true,
        'show_in_nav_menus' => true,
        'show_ui' => true,
        'show_tagcloud' => false,
        'hierarchical' => true,

        'rewrite' => true,
        'query_var' => true
    );

    register_taxonomy( 'wpmm_feature', array('wpmm_location'), $args );
}