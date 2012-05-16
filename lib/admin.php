<?php

/* ------------------------------------------------------------------------ *
 * Setting Registration
 * ------------------------------------------------------------------------ */ 

/**
 * Initializes the plugin options page by registering the Sections,
 * Fields, and Settings.
 *
 * This function is registered with the 'admin_init' hook.
 */
add_action('admin_init', 'wpmm_initialize_plugin_options');
function wpmm_initialize_plugin_options() {
    if( false == get_option( 'wpmm_plugin_display_options' ) ) {
	add_option( 'wpmm_plugin_display_options' );
    } // end if


	// First, we register a section. This is necessary since all future options must belong to one.
	add_settings_section(
		'general_settings_section',			// ID used to identify this section and with which to register options
		'Map marker Options',					// Title to be displayed on the administration page
		'wpmm_general_options_callback',	// Callback used to render the description of the section
		'wpmm_plugin_map_options'							// Page on which to add this section of options
	);

	// Next, we will introduce the fields for toggling the visibility of content elements.
	add_settings_field(
		'default_latitude',						// ID used to identify the field throughout the plugin
		'Default latitude',							// The label to the left of the option interface element
		'wpmm_default_latitude_callback',	// The name of the function responsible for rendering the option interface
		'wpmm_plugin_map_options',							// The page on which this option will be displayed
		'general_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			'Define the default latitude.'
		)
	);

	add_settings_field(
		'default_longitude',
		'Defaault longitude',
		'wpmm_default_longitude_callback',
		'wpmm_plugin_map_options',
		'general_settings_section',
		array(
			'Define the default longitude.'
		)
	);

	add_settings_field(
		'default_zoom',
		'Default zoom',
		'wpmm_default_zoom_callback',
		'wpmm_plugin_map_options',
		'general_settings_section',
		array(
			'Define the default zoom.'
		)
	);

	// Finally, we register the fields with WordPress
	register_setting(
		'wpmm_plugin_map_options',
		'wpmm_plugin_map_options'
	);


} // end wpmm_initialize_plugin_options

/* ------------------------------------------------------------------------ *
 * Section Callbacks
 * ------------------------------------------------------------------------ */ 

/**
 * This function provides a simple description for the General Options page.
 *
 * It is called from the 'wpmm_initialize_plugin_options' function by being passed as a parameter
 * in the add_settings_section function.
 */
function wpmm_general_options_callback() {
	echo '<p>Set the Google maps defaults.</p>';
} // end wpmm_general_options_callback

/* ------------------------------------------------------------------------ *
 * Field Callbacks
 * ------------------------------------------------------------------------ */ 

/**
 * This function renders the interface elements for toggling the visibility of the header element.
 *
 * It accepts an array of arguments and expects the first element in the array to be the description
 * to be displayed next to the checkbox.
 */
function wpmm_default_latitude_callback($args) {
        
        // Read the options collection
        $options = get_option('wpmm_plugin_map_options');
        
	// Note the ID and the name attribute of the element match that of the ID in the call to add_settings_field
	$html = '<input type="text" id="wpmm_plugin_map_options[default_latitude]" name="wpmm_plugin_map_options[default_latitude]" value="' . $options['default_latitude'] . '" />'; 

	// Here, we will take the first argument of the array and add it to a label next to the checkbox
	$html .= '<label for="wpmm_plugin_map_options[default_latitude]"> '  . $args[0] . '</label>'; 

	echo $html;

} // end wpmm_toggle_header_callback

function wpmm_default_longitude_callback($args) {
    
        // Read the options collection
        $options = get_option('wpmm_plugin_map_options');
        
	// Note the ID and the name attribute of the element match that of the ID in the call to add_settings_field
	$html = '<input type="text" id="wpmm_plugin_map_options[default_longitude]" name="wpmm_plugin_map_options[default_longitude]" value="' . $options['default_longitude'] . '" />'; 

	// Here, we will take the first argument of the array and add it to a label next to the checkbox
	$html .= '<label for="wpmm_plugin_map_options[default_longitude]"> '  . $args[0] . '</label>'; 

	echo $html;

} // end wpmm_toggle_content_callback

function wpmm_default_zoom_callback($args) {
    
            // Read the options collection
        $options = get_option('wpmm_plugin_map_options');

	// Note the ID and the name attribute of the element match that of the ID in the call to add_settings_field
	$html = '<input type="text" id="wpmm_plugin_map_options[default_zoom]" name="wpmm_plugin_map_options[default_zoom]" value="' . $options['default_zoom'] . '" />'; 

	// Here, we will take the first argument of the array and add it to a label next to the checkbox
	$html .= '<label for="wpmm_plugin_map_options[default_zoom]"> '  . $args[0] . '</label>'; 

	echo $html;

} // end wpmm_toggle_footer_callback

function wpmm_create_menu_page() {

	add_plugins_page(
		'Map Markers Options',			// The title to be displayed on the corresponding page for this menu
		'Map Markers',					// The text to be displayed for this actual menu item
		'administrator',			// Which type of users can see this menu
		'wpmm',					// The unique ID - that is, the slug - for this menu item
		'wpmm_plugin_display',// The name of the function to call when rendering the menu for this page
		''
	);
             

} // end wpmm_create_menu_page
add_action('admin_menu', 'wpmm_create_menu_page');

function wpmm_plugin_display() {
?>
	<!-- Create a header in the default WordPress 'wrap' container -->
	<div class="wrap">

		<!-- Add the icon to the page -->
		<div id="icon-plugins" class="icon32"></div>
		<h2>Map Markers Options</h2>

		<!-- Make a call to the WordPress function for rendering errors when settings are saved. -->
		<?php settings_errors(); ?>

		<!-- Create the form that will be used to render our options -->
	<form method="post" action="options.php">
		<?php settings_fields( 'wpmm_plugin_map_options' ); ?>
		<?php do_settings_sections( 'wpmm_plugin_map_options' ); ?>
		<?php submit_button(); ?>
	</form>


	</div><!-- /.wrap -->
<?php
} // end wpmm_plugin_display



?>
