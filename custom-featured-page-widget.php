<?php
/**
 * Custom Featured Page Widget
 *
 * @package           Custom_Featured_Page_Widget
 * @author            Sridhar Katakam
 * @license           GPL-2.0+
 * @link              https://sridharkatakam.com/
 * @copyright         2017 Sridhar Katakam
 *
 * @wordpress-plugin
 * Plugin Name:       Custom Featured Page Widget
 * Plugin URI:        https://sridharkatakam.com/...
 * Description:       Provides a canvas for modifying Genesis Featured Page Widget.
 * Version:           1.0.1
 * Author:            Sridhar Katakam
 * Author URI:        https://sridharkatakam.com/
 * Text Domain:       custom-featured-page-widget
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

register_activation_hook( __FILE__, 'custom_activation_check' );
/**
 * Check if Genesis is the parent theme.
 */
function custom_activation_check() {
	$theme_info = wp_get_theme();
	$genesis_flavors = array(
		'genesis',
		'genesis-trunk',
	);
	if ( ! in_array( $theme_info->Template, $genesis_flavors, true ) ) {
		deactivate_plugins( plugin_basename( __FILE__ ) ); // Deactivate ourself.
		$message = sprintf(
			/* translators: %s: URL to Genesis Framework. */
			__( 'Sorry, you can\'t activate this plugin unless you have installed <a href="%s">Genesis</a>.', 'genesis-simple-hook-guide' ),
			esc_url( 'https://my.studiopress.com/themes/genesis/' )
		);
		wp_die( $message );
	}
}

add_action( 'widgets_init', 'custom_featured_page_widget', 15 );
/**
 * Register widgets for use in a Genesis child theme.
 *
 * @since 1.0.0
 */
function custom_featured_page_widget() {
	require plugin_dir_path( __FILE__ ) . 'class-custom-featured-page.php';
	unregister_widget( 'Genesis_Featured_Page' );
	register_widget( 'Custom_Featured_Page' );
}

/* Note: If the require is happening too late, then move into a new function, hooked to genesis_setup, 15 instead:

add_action( 'genesis_setup', 'custom_featured_page_load_class', 15 );
function custom_featured_page_load_class() {
	require plugin_dir_path( __FILE__ ) . 'class-custom-featured-page.php';
}

*/
