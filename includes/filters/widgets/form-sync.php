<?php
namespace Jet_Engine_Form_Sync\Filters\Widgets;

use \Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Form_Sync extends \Elementor\Jet_Smart_Filters_Base_Widget {

	public function get_name() {
		return 'jet-smart-filters-form-sync';
	}

	public function get_title() {
		return __( 'Form Sync', 'jet-engine' );
	}

	public function get_icon() {
		return '';
	}

	public function get_help_url() {}

	protected function register_controls() {

		$this->start_controls_section(
			'section_general',
			array(
				'label' => __( 'Content', 'jet-smart-filters' ),
			)
		);

		$query_builder_link = admin_url( 'admin.php?page=jet-engine-query' );

		$this->add_control(
			'query_notice',
			array(
				'label' => '',
				'type' => Controls_Manager::RAW_HTML,
				'raw' => sprintf( __( '<b>Please note!</b><br><div class="elementor-control-field-description">This filter is compatible only with queries from <a href="%s" target="_blank">JetEngine Query Builder</a>. ALso you need to set up <a href="https://crocoblock.com/knowledge-base/jetengine/how-to-set-geo-search-based-on-user-geolocation/" target="_blank">Geo Query</a> in your query settings to make the filter work correctly.</div>', 'jet-engine' ), $query_builder_link ),
			)
		);

		$this->add_control(
			'form_id',
			array(
				'label'       => 'Select JetForm',
				'type'        => 'jet-query',
				'query_type'  => 'post',
				'description' => 'Select form that will trigger filtering on submit.',
				'query'       => array(
					'post_type' => jet_form_builder()->post_type->slug(),
				),
			)
		);

		$this->add_control(
			'filter_on',
			array(
				'label'   => 'Filter on',
				'type'    => Controls_Manager::SELECT,
				'default' => 'success',
				'options' => array(
					'success' => 'Successful form submit',
					'always'  => 'Form submit',
				),
			)
		);

		$this->add_control(
			'content_provider',
			array(
				'label'   => __( 'This filter for', 'jet-smart-filters' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => jet_smart_filters()->data->content_providers(),
			)
		);

		$this->add_control(
			'apply_type',
			array(
				'type'    => Controls_Manager::HIDDEN,
				'default' => 'ajax',
			)
		);
		
		$this->add_control(
			'apply_on',
			array(
				'type'    => Controls_Manager::HIDDEN,
				'default' => 'value',
			)
		);

		$this->add_control(
			'epro_posts_notice',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'raw'  => __( 'Please set <b>jet-smart-filters</b> into Query ID option of Posts widget you want to filter', 'jet-smart-filters' ),
				'condition' => array(
					'content_provider' => array( 'epro-posts', 'epro-portfolio' ),
				),
			)
		);

		$this->add_control(
			'query_id',
			array(
				'label'       => esc_html__( 'Query ID', 'jet-smart-filters' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'description' => __( 'Set unique query ID if you use multiple widgets of same provider on the page. Same ID you need to set for filtered widget.', 'jet-smart-filters' ),
			)
		);

		// Include Additional Providers Settings
		//include jet_smart_filters()->plugin_path( 'includes/widgets/common-controls/additional-providers.php' );

		$this->end_controls_section();

		$this->register_filter_settings_controls();

	}

	protected function render() {
		jet_engine_form_sync()->assets->frontend();

		jet_smart_filters()->set_filters_used();
		$this->add_render_attribute(
			'_wrapper',
			array(
				'style' => 'display: none;',
			)
		);

		$args = $this->get_settings();
		$args['filter_id'] = 0;

		$args['additional_providers'] = jet_smart_filters()->utils->get_additional_providers( $args );

		jet_smart_filters()->filter_types->render_filter_template( $this->get_widget_fiter_type(), $args );
	}

}
