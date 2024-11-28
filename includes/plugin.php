<?php

namespace Jet_Engine_Form_Sync;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die();
}

class Plugin {

	private static $instance = null;

	public $storage = null;

	public $settings = null;

	public $assets = null;

	private $url = '';

	private $plugin_path = '';

	public $version = '1.0.0';

	public function __construct() {

		if ( ! function_exists( 'jet_form_builder' ) || ! function_exists( 'jet_engine' ) ) {

			add_action( 'admin_notices', function () {

				$class = 'notice notice-error';
				
				$message = __(
					'<b>Error:</b> <b>JetEngine - Form Sync</b> plugin requires' . 
					' <b>JetFormBuilder</b> and <b>JetEngine</b> plugins to be installed and activated',
					'jfb-select-all'
				);

				printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), wp_kses_post( $message ) );

			} );

			return;
		}
		
		add_action( 'plugins_loaded', array( $this, 'init' ), 150 );

	}

	public function get_path( $path = null ) {

		if ( ! $this->plugin_path ) {
			$this->plugin_path = trailingslashit( plugin_dir_path( dirname( __FILE__ ) ) );
		}

		return $this->plugin_path . $path;
	}

	public function get_url( $path = '' ) {
		if ( empty( $this->url ) ) {
			$this->url = plugins_url( '', dirname( __FILE__ ) );
		}

		return $this->url . $path;
	}

	public static function instance() {

		if ( empty( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;

	}

	public function init() {
		require_once $this->get_path( 'includes/assets.php' );
		$this->assets = new Assets();
		require_once $this->get_path( 'includes/filters/manager.php' );
		new Filters\Manager();

	}

}
