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

require_once dirname(__FILE__ ) .  '/lib/post-types.php';
require_once dirname(__FILE__ ) .  '/lib/taxonomies.php';
require_once dirname(__FILE__ ) .  '/lib/metaboxes.php';

define ( 'MAP_API_KEY', 'AIzaSyDHje59oiWoK8WCgVdN1zrxrIGqrW9cTiQ' );

add_action( 'plugins_loaded','wpmm_plugin_setup' );

// Initialize plugin
function wpmm_plugin_setup(){
    
    /* Set constant path to the Cleaner Gallery plugin directory. */
    define( 'WPMM_DIR', plugin_dir_path( __FILE__ ) );

    /* Set constant path to the Cleaner Gallery plugin URL. */
    define( 'WPMM_URL', plugin_dir_url( __FILE__ ) );
        
    add_action( 'wp_enqueue_scripts', 'wpmm_enqueue_scripts' );
}

// Load necessary javascript and CSS files
function wpmm_enqueue_scripts(){
    
    // Load the maps API
    // http://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&sensor=SET_TO_TRUE_OR_FALSE
    wp_enqueue_script( 'maps-api-js','http://maps.googleapis.com/maps/api/js?sensor=false&libraries=places' );
    
     wp_enqueue_script( 'infobubble-js', 'http://google-maps-utility-library-v3.googlecode.com/svn/trunk/infobubble/src/infobubble-compiled.js',array( 'jquery', 'maps-api-js' ) );   
    // Loads the Google Store Locator library, depends on jQuery
    wp_enqueue_script( 'store-locator-js', plugins_url( 'js/store-locator.compiled.js', __FILE__ ),array( 'jquery', 'maps-api-js', 'infobubble-js' ) );
    
    // Loads the data handler script
    wp_enqueue_script( 'data-feed-js', plugins_url( 'js/wpmm-static-ds.js', __FILE__ ),array( 'jquery','store-locator-js', 'maps-api-js','infobubble-js' ) );
     
    // make stored stores available to map script
    $stores = json_encode( wpmm_fetch_stores() );
    wp_localize_script( 'data-feed-js', 'wpmm_stores', $stores );  
    
    $wpmm_all_features = json_encode( wpmm_get_all_features() );
    wp_localize_script( 'data-feed-js', 'wpmm_features', $wpmm_all_features );
     
    // Loads the panel script
    wp_enqueue_script( 'panel-js', plugins_url( 'js/panel.js', __FILE__ ),array( 'jquery','store-locator-js', 'maps-api-js','infobubble-js' ) ); 
    
     $wpmm_settings = json_encode( wpmm_global_settings() );
    wp_localize_script( 'panel-js', 'wpmm_settings', $wpmm_settings ); 
    
    // Loads the Store Locator default CSS styles
    wp_enqueue_style( 'store-locator-style-css', plugins_url('css/storelocator.css', __FILE__ ) );
}

// This function gets the stores from the database
function wpmm_fetch_stores(){
    
    // fetch location posts which have a latitude, meaning geocoding works
      $args = array(
        'post_type' => 'wpmm_location',
        'post_status' => 'publish',
        'meta_query' => array(
          array(
            'key' => 'wpmm_latitude',
            'value' => '',
            'compare' => '!='
          ),
          array(
            'key' => 'wpmm_displayonmap',
            'value' => '',
            'compare' => '!='
          )            
        )
      );
    $locations = get_posts( $args );
    
    // fetch posts custom meta fields vaules
    foreach($locations as $location){
        // get features
        $features = wpmm_get_location_features($location);
        $store_id = $location->ID;
        $store_name = get_post_meta($location->ID,'wpmm_location_name',true);
        $store_lat = get_post_meta($location->ID,'wpmm_latitude',true);
        $store_lng = get_post_meta($location->ID,'wpmm_longitude',true);
        $store_address = get_post_meta($location->ID,'wpmm_address',true);
        $store_permalink = $location->post_permalink;
        $stores[] = array(
            'id' => $store_id,
            'store_name'=> $store_name,
            'store_lat'=> $store_lat,
            'store_lng'=> $store_lng,
            'address' => $store_address, 
            'store_permalink' => $store_permalink,
            'store_features' => $features
            );
    }

    return $stores;
}

function wpmm_global_settings(){
    
    // TODO fetch this from settings page
    $marker_icon = WPMM_URL . 'images/medicare.png';
    $marker_shadow = WPMM_URL . 'images/medicare-shadow.png';
    $default_zoom = 8;
    $map_center_lat = 51.4992913;
    $map_center_lng = -0.1639785;

    $wpmm_settings[] = array(
        'marker_icon' => $marker_icon, 
        'marker_shadow' => $marker_shadow, 
        'default_zoom' => $default_zoom ,
        'map_center_lat' => $map_center_lat, 
        'map_center_lng' => $map_center_lng);
    return $wpmm_settings;
}

function wpmm_get_location_features($post){
        //Returns Array of Term Names for "my_term"
        $term_list = wp_get_post_terms($post->ID, 'wpmm_feature', array("fields" => "names"));  
        return $term_list;
}

function wpmm_get_all_features(){
    
    //returns all terms fron the taxonomy features
    $features = get_terms('wpmm_feature');
    return $features;
    //print_r($features);die();
}