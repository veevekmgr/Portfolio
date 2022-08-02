<?php


namespace RT\ThePostGrid\Controllers;


use RT\ThePostGrid\Widgets\TPGWidget;

class WidgetController {
	function __construct() {
		add_action( 'widgets_init', [ $this, 'initWidget' ] );
	}

	function initWidget() {
		register_widget( TPGWidget::class );
	}
}