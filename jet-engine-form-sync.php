<?php
/**
 * Plugin Name: JetEngine - Form Sync
 * Plugin URI:  
 * Description: 
 * Version:     1.0.0
 * Author:      Crocoblock
 * Author URI:  https://crocoblock.com/
 * Text Domain: jsf-store-filters
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die();
}

//require_once( 'vendor/autoload.php' );

add_action( 'plugins_loaded', function() {
	require_once trailingslashit( plugin_dir_path( __FILE__ ) ) . 'includes/plugin.php';
	Jet_Engine_Form_Sync\Plugin::instance();
}, 100 );

if ( ! function_exists( 'jet_engine_form_sync' ) ) {
	function jet_engine_form_sync() {
		return Jet_Engine_Form_Sync\Plugin::instance();
	}
}
