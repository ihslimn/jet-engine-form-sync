<?php
/**
 * Class: Jet_Smart_Filters_Provider_Jet_Popup
 * Name: JetEngine
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Smart_Filters_Provider_Jet_Popup' ) ) {
	/**
	 * Define Jet_Smart_Filters_Provider_Jet_Popup class
	 */
	class Jet_Smart_Filters_Provider_Jet_Popup extends Jet_Smart_Filters_Provider_Base {

		public function id_prefix() {
			return '#jet-popup-';
		}

		/**
		 * Get provider name
		 */
		public function get_name() {
			return __( 'JetPopup (Form Sync only)', 'jet-smart-filters' );
		}

		/**
		 * Get provider ID
		 */
		public function get_id() {
			return 'jet-popup';
		}

		/**
		 * Get filtered provider content
		 */
		public function ajax_get_content() {
			return '';
		}

		public function jet_popup_get_content() {
			$data = ( ! empty( $_POST['jetEngineListingPopupData'] ) ) ? $_POST['jetEngineListingPopupData'] : false;

			if ( ! $data ) {
				wp_send_json_error( [ 'type' => 'error', 'message' => 'error' ] );
			}

			$popup_data   = apply_filters( 'jet-popup/ajax-request/post-data', $data );
			$popup_id     = $popup_data[ 'popup_id' ];
			$content_type = jet_popup()->post_type->get_popup_content_type( $popup_id );
			$content      = apply_filters( "jet-popup/ajax-request/get-{$content_type}-content", false, $popup_data );

			// Deprecated filter
			$popup_data   = apply_filters( 'jet-popup/ajax-request/after-content-define/post-data', $popup_data );

			switch ( $content_type ) {
				case 'default':
					$render_instance = new \Jet_Popup\Render\Block_Editor_Content_Render( [
						'popup_id'       => $popup_id,
						'with_css'       => true,
						'is_style_deps'  => false,
						'is_script_deps' => false,
					] );
					break;
				case 'elementor':
					$render_instance = new \Jet_Popup\Render\Elementor_Content_Render( [
						'popup_id'       => $popup_id,
						'is_style_deps'  => false,
						'is_script_deps' => false,
					] );
					break;
			}

			$content_data = $render_instance->get_render_data();

			if ( ! empty( $content )  ) {
				$content_data['content'] = $content;
			}

			if ( empty( $content_data ) ) {
				wp_send_json( [
					'type'    => 'error',
					'message' => 'no data'
				] );
			}

			$popup_data = ( ! empty( $popup_data ) ) ? $popup_data : false;

			$result = [
				'type'    => 'success',
				'content' => $content_data,
				'data'    => $popup_data
			];

			return $result;
		}

		/**
		 * Get provider wrapper selector
		 */
		public function get_wrapper_selector() {
			return '.jet-popup__container-inner';
		}

		public function get_list_selector() {

			return '';
		}

		public function get_wrapper_action() {

			return 'replace';
		}

		public function in_depth() {

			return true;
		}

		public function apply_filters_in_request() {
			return '';
		}
	}
}
