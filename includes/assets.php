<?php

namespace Jet_Engine_Form_Sync;

use \Jet_Engine_Form_Sync\Plugin;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die();
}

class Assets {

	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend' ) );
	}

	public function frontend() {
		wp_register_script(
			'jet-engine-form-sync-frontend',
			Plugin::instance()->get_url( '/assets/js/frontend.js' ),
			array( 'jet-plugins' ),
			Plugin::instance()->version,
			true
		);
	}

}
