<?php
/*
  Plugin Name: WP Map markers
  Plugin URI: http://wpmapmarkers.com
  Description: Allows you to mark your store locations on a Google map. Searchable and gives driving directions. Uses geolocation.
  Version: 0.9.9.4
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
if ( !defined( "WPMM_CURRENT_PAGE" ) )
	define( "WPMM_CURRENT_PAGE", basename( $_SERVER['PHP_SELF'] ) );

/* Define your theme/plugin database version. This should only change when new settings are added. */
if ( !defined( 'WPMM_DB_VERSION' ) )
	define( 'WPMM_DB_VERSION', 2 );


if ( version_compare( PHP_VERSION, '5.2', '<' ) ) {
	if ( is_admin() && (!defined( 'DOING_AJAX' ) || !DOING_AJAX) ) {
		require_once ABSPATH . '/wp-admin/includes/plugin.php';
		deactivate_plugins( __FILE__ );
		wp_die( __( 'WP Map Markers requires PHP 5.2 or higher, as does WordPress 3.2 and higher. The plugin has now disabled itself.', 'wpmm-map-markers' ) );
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
	
	/* Set constant path to the WPMM plugin file. */	
	define( 'WPMM_FILE', __FILE__ );

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
		load_plugin_textdomain( 'wpmm-map-markers', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		/* Load the plugin's admin file. */
		require_once WPMM_DIR . '/lib/admin.php';
	}


	add_action( 'admin_enqueue_scripts', 'wpmm_enqueue_scripts' );

	add_shortcode( 'wpmm_map', 'wpmm_do_display_map' );

	add_action( 'wp_head', 'wpmm_do_map_dimensions' );



	if ( in_array( WPMM_CURRENT_PAGE, array( 'post.php', 'page.php', 'page-new.php', 'post-new.php' ) ) ) {
		add_action( 'media_buttons_context', 'wpmm_add_map_button' );
		add_action( 'admin_footer', 'wpmm_add_popup_map_selector' );
	}
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
		'posts_per_page' => -1,
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

// Set the map dimensions by outputting CSS to the head
function wpmm_do_map_dimensions() {
	$options = get_option( 'wpmm_plugin_map_options' );

	if ( isset( $options['wpmm_map_height'] ) ) {
		$map_height = $options['wpmm_map_height'];
	} else {
		$map_height = '450px';
	}
	if ( isset( $options['wpmm_map_width'] ) ) {
		$map_width = $options['wpmm_map_width'];
	} else {
		$map_width = '68%';
	}
		if ( isset( $options['wpmm_panel_height'] ) ) {
		$panel_height = $options['wpmm_panel_height'];
	} else {
		$panel_height = '450px';
	}
	if ( isset( $options['wpmm_panel_width'] ) ) {
		$panel_width = $options['wpmm_panel_width'];
	} else {
		$panel_width = '28%';
	}
	echo "<style> #wpmm-container {overflow:hidden;width:100%;} #wpmm-container #map-canvas {width: $map_width; height: $map_height; float: left; } #wpmm-container #panel {width: $panel_width; height: $panel_height; float: left; }</style>";
}

//action to add a custom button to the content editor
function wpmm_add_map_button( $context ) {

	//path to my icon
	$img = WPMM_URL . 'images/map-icon.png';

	//the id of the container I want to show in the popup
	$container_id = 'popup_container';

	//our popup's title
	$title = 'Insert a map';

	//append the icon
	$context .= "<a class='thickbox' title='{$title}'
    href='#TB_inline?width=400&inlineId={$container_id}'>
    <img src='{$img}' /></a>";

	return $context;
}

function wpmm_add_popup_map_selector() {
	?>
	<div id="popup_container" style="display:none;">
		<h2>Select a map</h2>
		<script>
			function wpmm_insert_map(){
				var map_id = jQuery("#map_select").val();
				if(map_id == ""){
					alert("<?php _e( "Please select a map", "wpmm-map-markers" ) ?>");
					return;
				}

				window.send_to_editor("[wpmm_map map=\"" + map_id +  "\"" + "]");
			}
		</script>
		<?php
		$args = array(
			'taxonomy' => 'wpmm_map',
			'id' => 'map_select'
		);
		wp_dropdown_categories( $args );
		?>
		<div style="padding:15px;">
			<input type="button" class="button-primary" value="Insert Map" onclick="wpmm_insert_map();"/>&nbsp;&nbsp;&nbsp;
			<a class="button" style="color:#bbb;" href="#" onclick="tb_remove(); return false;"><?php _e( "Cancel", "gravityforms" ); ?></a>
		</div>
	</div>
	<?php
}
