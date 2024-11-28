<?php
namespace Jet_Engine_Form_Sync\Filters\Types;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define User_Geolocation class
 */
class Form_Sync extends \Jet_Smart_Filters_Filter_Base {

	/**
	 * Get provider ID
	 *
	 * @return string
	 */
	public function get_id() {
		return 'form-sync';
	}

	/**
	 * Get provider name
	 *
	 * @return string
	 */
	public function get_name() {
		return __( 'Form Sync', 'jet-engine' );
	}

	/**
	 * Get provider wrapper selector
	 *
	 * @return string
	 */
	public function get_scripts() {
		return array( 'jet-engine-form-sync-frontend' );
	}

	public function get_template( $args = array() ) {
		return jet_engine_form_sync()->get_path( 'includes/filters/types/form-sync-template.php' );
	}

	/**
	 * Prepare filter template argumnets
	 *
	 * @param  [type] $args [description]
	 *
	 * @return [type]       [description]
	 */
	public function prepare_args( $args ) {

		$content_provider     = isset( $args['content_provider'] ) ? $args['content_provider'] : false;
		$additional_providers = isset( $args['additional_providers'] ) ? $args['additional_providers'] : false;

		return array(
			'options'              => false,
			'query_type'           => 'form_sync',
			'query_var'            => '',
			'content_provider'     => $content_provider,
			'additional_providers' => $additional_providers,
			'apply_type'           => 'ajax',
			'form_id'              => $args['form_id'] ?? '',
			'filter_on'            => $args['filter_on'] ?? 'success',
		);

	}

	public function additional_filter_data_atts( $args ) {
		$additional_filter_data_atts = array();

		$additional_filter_data_atts['data-form-id']   = $args['form_id'];
		$additional_filter_data_atts['data-filter-on'] = $args['filter_on'];

		return $additional_filter_data_atts;
	}

}
