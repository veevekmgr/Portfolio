<?php

namespace RT\ThePostGrid\Widgets;

use Elementor\Widget_Base;
use RT\ThePostGrid\Helpers\Fns;

class ElementorWidget extends Widget_Base {

	public function get_name() {
		return 'the-post-grid';
	}

	public function get_title() {
		return __( 'The Post Grid', 'the-post-grid' );
	}

	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	public function get_categories() {
		return [ 'general' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'The Post Grid', 'the-post-grid' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'post_grid_id',
			array(
				'type'    => \Elementor\Controls_Manager::SELECT2,
				'id'      => 'style',
				'label'   => __( 'Post Grid', 'the-post-grid' ),
				'options' => Fns::getAllTPGShortCodeList()
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		if(isset($settings['post_grid_id']) && !empty($settings['post_grid_id']) && $id = absint($settings['post_grid_id'])){
			echo do_shortcode( '[the-post-grid id="' . $id . '"]' );
		}else{
			echo "Please select a post grid";
		}
	}
}