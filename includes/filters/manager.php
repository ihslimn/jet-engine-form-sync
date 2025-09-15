<?php
namespace Jet_Engine_Form_Sync\Filters;

class Manager {

	public function __construct() {
		add_action( 'jet-smart-filters/filter-types/register', array( $this, 'register_filter_types' ) );
		add_action( 'jet-engine/elementor-views/widgets/register', array( $this, 'register_widgets' ), 20, 2 );
		add_filter( 'jet-smart-filters/query/vars', array( $this, 'register_query_var' ) );
		add_action( 'jet-smart-filters/providers/register', array( $this, 'register_providers' ) );
		add_action( 'wp_ajax_form_sync_ajax_popup', array( $this, 'get_popup_content' ) );
		add_action( 'wp_ajax_nopriv_form_sync_ajax_popup', array( $this, 'get_popup_content' ) );
	}

	public function get_popup_content() {
	
		if ( ! function_exists( 'jet_popup' ) ) {
			return;
		}

		jet_popup()->ajax_handlers->jet_popup_get_content();
		
	}

	public function register_query_var( $vars ) {
		$vars[] = 'form_sync';
		return $vars;
	}

	public function register_widgets( $widgets_manager, $elementor_views ) {

		if ( ! function_exists( 'jet_smart_filters' ) ) {
			return;
		}

		$filters_path = jet_engine_form_sync()->get_path( 'includes/filters/widgets/' );

		$elementor_views->register_widget(
			$filters_path . 'form-sync.php',
			$widgets_manager,
			__NAMESPACE__ . '\Widgets\Form_Sync'
		);

	}

	public function register_filter_types( $types_manager ) {

		$filters_path = jet_engine_form_sync()->get_path( 'includes/filters/types/' );

		$types_manager->register_filter_type(
			'\Jet_Engine_Form_Sync\Filters\Types\Form_Sync',
			$filters_path . 'form-sync.php'
		);

	}

	public function register_providers( $providers_manager  ) {
		$filters_path = jet_engine_form_sync()->get_path( 'includes/filters/providers/' );

		$providers_manager->register_provider(
			'\Jet_Smart_Filters_Provider_Jet_Popup', // Custom provider class name
			$filters_path . 'jet-popup.php' // Path to file where this class defined
		);
	}

}
