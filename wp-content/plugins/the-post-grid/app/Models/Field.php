<?php
namespace RT\ThePostGrid\Models;

use RT\ThePostGrid\Helpers\Fns;
use RT\ThePostGrid\Helpers\Options;

class Field {
	private $type;
	private $name;
	private $value;
	private $default;
	private $label;
	private $id;
	private $class;
	private $holderClass;
	private $description;
	private $options;
	private $option;
	private $optionLabel;
	private $attr;
	private $multiple;
	private $alignment;
	private $placeholder;
	private $blank;

	function __construct() {
	}

	private function setArgument( $key, $attr ) {
		global $pagenow;

		$this->type     = isset( $attr['type'] ) ? ( $attr['type'] ? $attr['type'] : 'text' ) : 'text';
		$this->multiple = isset( $attr['multiple'] ) ? ( $attr['multiple'] ? $attr['multiple'] : false ) : false;
		$this->name     = ! empty( $key ) ? $key : null;
		$id             = isset( $attr['id'] ) ? $attr['id'] : null;
		$this->id       = ! empty( $id ) ? $id : $this->name;
		$this->default  = isset( $attr['default'] ) ? $attr['default'] : null;
		$this->value    = isset( $attr['value'] ) ? ( $attr['value'] ? $attr['value'] : null ) : null;

		if ( ! $this->value ) {
			$post_id = get_the_ID();
			if ( ! Fns::meta_exist( $this->name, $post_id ) &&  $pagenow == 'post-new.php') {
				$this->value = $this->default;
			} else {
				if ( $this->multiple ) {
					if (metadata_exists('post', $post_id, $this->name)) {
						$this->value = get_post_meta( $post_id, $this->name );
					} else {
						$this->value = $this->default;
					}
				} else {
					if (metadata_exists('post', $post_id, $this->name)) {
						$this->value = get_post_meta( $post_id, $this->name, true );
					} else {
						$this->value = $this->default;
					}
				}
			}
		}

		$this->label       = isset( $attr['label'] ) ? ( $attr['label'] ? $attr['label'] : null ) : null;
		$this->class       = isset( $attr['class'] ) ? ( $attr['class'] ? $attr['class'] : null ) : null;
		$this->holderClass = isset( $attr['holderClass'] ) ? ( $attr['holderClass'] ? $attr['holderClass'] : null ) : null;
		$this->placeholder = isset( $attr['placeholder'] ) ? ( $attr['placeholder'] ? $attr['placeholder'] : null ) : null;
		$this->description = isset( $attr['description'] ) ? ( $attr['description'] ? $attr['description'] : null ) : null;
		$this->options     = isset( $attr['options'] ) ? ( $attr['options'] ? $attr['options'] : array() ) : array();
		$this->option      = isset( $attr['option'] ) ? ( $attr['option'] ? $attr['option'] : null ) : null;
		$this->optionLabel = isset( $attr['optionLabel'] ) ? ( $attr['optionLabel'] ? $attr['optionLabel'] : null ) : null;
		$this->attr        = isset( $attr['attr'] ) ? ( $attr['attr'] ? $attr['attr'] : null ) : null;
		$this->alignment   = isset( $attr['alignment'] ) ? ( $attr['alignment'] ? $attr['alignment'] : null ) : null;
		$this->blank       = ! empty( $attr['blank'] ) ? $attr['blank'] : null;

	}

	public function Field( $key, $attr = array() ) {
		$this->setArgument( $key, $attr );
		$holderId = $this->name . "_holder";

		if (!rtTPG()->hasPro()) {
			$class = $this->holderClass;
		} else {
			$class = str_replace('pro-field', '', $this->holderClass);
		}
		$html     = null;
		$html     .= "<div class='field-holder {$class}' id='{$holderId}'>";

		$holderClass = explode(' ', $this->holderClass);

		if ( $this->label ) {
			$html .= "<div class='field-label'>";
			$html .= "<label>{$this->label}</label>";
			if (in_array('pro-field', $holderClass) && ! rtTPG()->hasPro()) {
				$html .= '<span class="rttpg-tooltip">[Pro]<span class="rttpg-tooltip-text">'.esc_html__('Premium Option', 'the-post-grid').'</span></span>';
			}
			$html .= "</div>";
		}
		$html .= "<div class='field'>";
		switch ( $this->type ) {
			case 'text':
				$html .= $this->text();
				break;

			case 'url':
				$html .= $this->url();
				break;

			case 'number':
				$html .= $this->number();
				break;

			case 'select':
				$html .= $this->select();
				break;

			case 'textarea':
				$html .= $this->textArea();
				break;

			case 'checkbox':
				$html .= $this->checkbox();
				break;

			case 'switch':
				$html .= $this->switchField();
				break;

			case 'checkboxFilter':
				$html .= $this->checkboxFilter();
				break;

			case 'radio':
				$html .= $this->radioField();
				break;

			case 'radio-image':
				$html .= $this->radioImage();
				break;

			case 'date_range':
				$html .= $this->dateRange();
				break;

			case 'script':
				$html .= $this->script();
				break;

			case 'image':
				$html .= $this->image();
				break;

			case 'image_size':
				$html .= $this->imageSize();
				break;
		}
		if ( $this->description ) {
			$html .= "<p class='description'>{$this->description}</p>";
		}
		$html .= "</div>"; // field
		$html .= "</div>"; // field holder

		return $html;
	}

	private function text() {
		$holderClass = explode(' ', $this->holderClass);
		$h = null;
		$h .= "<input
                    type='text'
                    class='{$this->class}'
                    id='{$this->id}'
                    value='{$this->value}'
                    name='{$this->name}'
                    placeholder='{$this->placeholder}'
                    {$this->attr}
                    />";

		return $h;
	}

	private function script() {
		$type = "script";
		if ( $this->id == "custom-css" ) {
			$type = "css";
		}
		$h = null;
		$h .= '<div class="rt-script-wrapper" data-type="' . $type . '">';
		$h .= '<div class="rt-script-container">';
		$h .= "<div name='{$this->name}' id='ret-" . mt_rand() . "' class='rt-script'>";
		$h .= '</div>';
		$h .= '</div>';

		$h .= "<textarea
                        style='display: none;'
                        class='rt-script-textarea'
                        id='{$this->id}'
                        name='{$this->name}'
                        >{$this->value}</textarea>";
		$h .= '</div>';

		return $h;
	}

	private function url() {
		$h = null;
		$h .= "<input
                    type='url'
                    class='{$this->class}'
                    id='{$this->id}'
                    value='{$this->value}'
                    name='{$this->name}'
                    placeholder='{$this->placeholder}'
                    {$this->attr}
                    />";

		return $h;
	}

	private function number() {
		$holderClass = explode(' ', $this->holderClass);
		$h = null;
		$h .= "<input
                    type='number'
                    class='{$this->class}'
                    id='{$this->id}'
                    value='{$this->value}'
                    name='{$this->name}'
                    placeholder='{$this->placeholder}'
                    {$this->attr}
                    />";

		return $h;
	}

	private function select() {
		$holderClass = explode(' ', $this->holderClass);
		$atts = (in_array('pro-field', $holderClass)) && !rtTPG()->hasPro() ? 'disabled="true"' : '';
		$h = null;
		if ( $this->multiple ) {
			$this->attr  = " style='min-width:160px;'";
			$this->name  = $this->name . "[]";
			$this->attr  = $this->attr . " multiple='multiple'";
			$this->value = ( is_array( $this->value ) && ! empty( $this->value ) ? $this->value : array() );
		} else {
			$this->value = array( $this->value );
		}

		$h .= "<select {$atts} name='{$this->name}' id='{$this->id}' class='{$this->class}' {$this->attr}>";
		if ( $this->blank ) {
			$h .= "<option value=''>{$this->blank}</option>";
		}
		if ( is_array( $this->options ) && ! empty( $this->options ) ) {
			foreach ( $this->options as $key => $value ) {
				$slt = ( in_array( $key, $this->value ) ? "selected" : null );
				$h   .= "<option {$slt} value='{$key}'>{$value}</option>";
			}
		}
		$h .= "</select>";

		return $h;
	}

	private function textArea() {
		$holderClass = explode(' ', $this->holderClass);
		$h = null;
		$h .= "<textarea
                    class='{$this->class} rt-textarea'
                    id='{$this->id}'
                    name='{$this->name}'
                    placeholder='{$this->placeholder}'
                    {$this->attr}
                    >{$this->value}</textarea>";

		return $h;
	}

	private function image() {
		$holderClass = explode(' ', $this->holderClass);
		$h   = null;
		$h   .= "<div class='rt-image-holder'>";
		$h   .= "<input type='hidden' name='{$this->name}' value='{$this->value}' id='{$this->id}' class='hidden-image-id' />";
		$img = null;
		$c   = "hidden";
		if ( $id = absint( $this->value ) ) {
			$aImg = wp_get_attachment_image_src( $id, 'thumbnail' );
			$img  = "<img src='{$aImg[0]}' >";
			$c    = null;
		}

		$h .= "<div class='rt-image-preview'>{$img}<span class='dashicons dashicons-plus-alt rtAddImage'></span><span class='dashicons dashicons-trash rtRemoveImage {$c}'></span></div>";

		$h .= "</div>";

		return $h;
	}

	private function imageSize() {
		$width    = ( ! empty( $this->value[0] ) ? absint( $this->value[0] ) : null );
		$height   = ( ! empty( $this->value[1] ) ? absint( $this->value[1] ) : null );
		$cropV    = ( ! empty( $this->value[2] ) ? $this->value[2] : 'soft' );
		$h        = null;
		$h        .= "<div class='rt-image-size-holder'>";
		$h        .= "<div class='rt-image-size-width rt-image-size'>";
		$h        .= "<label>Width</label>";
		$h        .= "<input type='number' name='{$this->name}[]' value='{$width}' />";
		$h        .= "</div>";
		$h        .= "<div class='rt-image-size-height rt-image-size'>";
		$h        .= "<label>Height</label>";
		$h        .= "<input type='number' name='{$this->name}[]' value='{$height}' />";
		$h        .= "</div>";
		$h        .= "<div class='rt-image-size-crop rt-image-size'>";
		$h        .= "<label>Crop</label>";
		$h        .= "<select name='{$this->name}[]' class='rt-select2'>";
		$cropList = Options::imageCropType();
		foreach ( $cropList as $crop => $cropLabel ) {
			$cSl = ( $crop == $cropV ? "selected" : null );
			$h   .= "<option value='{$crop}' {$cSl}>{$cropLabel}</option>";
		}
		$h .= "</select>";
		$h .= "</div>";
		$h .= "</div>";

		return $h;
	}

	private function checkbox() {
		$holderClass = explode(' ', $this->holderClass);
		$this->alignment .= (in_array('pro-field', $holderClass)) && !rtTPG()->hasPro() ? ' disabled' : '';
		$h = null;
		if ( $this->multiple ) {
			$this->name  = $this->name . "[]";
			$this->value = ( is_array( $this->value ) && ! empty( $this->value ) ? $this->value : array() );
		}
		if ( $this->multiple ) {
			$h .= "<div class='checkbox-group {$this->alignment}' id='{$this->id}'>";
			if ( is_array( $this->options ) && ! empty( $this->options ) ) {
				foreach ( $this->options as $key => $value ) {
					$checked = ( in_array( $key, $this->value ) ? "checked" : null );

					$h       .= "<label for='{$this->id}-{$key}'>
                                <input type='checkbox' id='{$this->id}-{$key}' {$checked} name='{$this->name}' value='{$key}'>{$value}
                                </label>";
				}
			}
			$h .= "</div>";
		} else {
			$checked = ( $this->value ? "checked" : null );
			$h       .= "<label><input type='checkbox' {$checked} id='{$this->id}' name='{$this->name}' value='1' />{$this->option}</label>";
		}

		return $h;
	}

	private function switchField() {
		$h = null;
		$checked = $this->value ? "checked" : null;
		$h .= "<label class='rttm-switch'><input type='checkbox' {$checked} id='{$this->id}' name='{$this->name}' value='1' /><span class='rttm-switch-slider round'></span></label>";

		return $h;
	}

	private function checkboxFilter() {

		global $post;

		$pt = get_post_meta($post->ID, 'tpg_post_type', true);
		$advFilters = Options::rtTPAdvanceFilters();

		$holderClass = explode(' ', $this->holderClass);

		$h = null;
		if ( $this->multiple ) {
			$this->name  = $this->name . "[]";
			$this->value = ( is_array( $this->value ) && ! empty( $this->value ) ? $this->value : array() );
		}
		if ( $this->multiple ) {
			$h .= "<div class='checkbox-group {$this->alignment}' id='{$this->id}'>";
			if ( is_array( $this->options ) && ! empty( $this->options ) ) {
				foreach ( $this->options as $key => $value ) {
					$checked = ( in_array( $key, $this->value ) ? "checked" : null );

					$h .= '<div class="checkbox-filter-field">';

					$h       .= "<label for='{$this->id}-{$key}'>
                                <input type='checkbox' id='{$this->id}-{$key}' {$checked} name='{$this->name}' value='{$key}'>{$value}
                                </label>";

					//foreach($advFilters['post_filter']['options'] as $key => $filter){

					if($key == 'tpg_taxonomy'){
						$h .= "<div class='rt-tpg-filter taxonomy tpg_taxonomy tpg-hidden'>";

						if(isset($pt) && $pt){
							$taxonomies = Fns::rt_get_all_taxonomy_by_post_type($pt);
							$taxA = get_post_meta($post->ID, 'tpg_taxonomy');
							$post_filter = get_post_meta($post->ID, 'post_filter');

							$h .= "<div class='taxonomy-field'>";
							if(is_array($post_filter) && !empty($post_filter) && in_array('tpg_taxonomy', $post_filter) && !empty($taxonomies)) {
								$h .= Fns::rtFieldGenerator(
									array(
										'tpg_taxonomy' => array(
											'type' => 'checkbox',
											'label' => '',
											'id' => 'post-taxonomy',
											"multiple" => true,
											'options' => $taxonomies
										)
									)
								);
							}else{
								$h .= '<div class="field-holder">No Taxonomy found</div>';
							}
							$h .= "</div>";
							$h .= "<div class='rt-tpg-filter-item term-filter-item tpg-hidden'>";
							$h .= '<div class="field-holder">';
							$h .= '<div class="field-label">Terms</div>';
							$h .= '<div class="field term-filter-holder">';
							if(is_array($taxA) && !empty($taxA)){
								foreach($taxA as $tax){

									$h .="<div class='term-filter-item-container {$tax}'>";
									$h .= Fns::rtFieldGenerator(
										array(
											'term_'.$tax => array(
												'type' => 'select',
												'label' => ucfirst(str_replace('_', ' ', $tax)),
												'class' => 'rt-select2 full',
												'holderClass' => "term-filter-item {$tax}",
												'value' => get_post_meta($post->ID, 'term_'.$tax),
												"multiple" => true,
												'options' => Fns::rt_get_all_term_by_taxonomy($tax)
											)
										)
									);
									$h .= Fns::rtFieldGenerator(
										array(
											'term_operator_'.$tax => array(
												'type' => 'select',
												'label' => 'Operator',
												'class' => 'rt-select2 full',
												'holderClass' => "term-filter-item-operator {$tax}",
												'value' => get_post_meta($post->ID, 'term_operator_'.$tax, true),
												'options' => Options::rtTermOperators()
											)
										)
									);
									$h .= "</div>";
								}
							}
							$h .= "</div>";
							$h .= "</div>";

							$h .= Fns::rtFieldGenerator(
								array(
									'taxonomy_relation' => array(
										'type' => 'select',
										'label' => 'Relation',
										'class' => 'rt-select2',
										'holderClass' => "term-filter-item-relation ". (count($taxA) > 1 ? null : "hidden"),
										'value' => get_post_meta($post->ID, 'taxonomy_relation', true),
										'options' => Options::rtTermRelations()
									)
								)
							);

							$h .= "</div>";
						}else{

							$h .= "<div class='taxonomy-field'>";
							$h .= "</div>";
							$h .= "<div class='rt-tpg-filter-item'>";
							$h .= '<div class="field-holder">';
							$h .= '<div class="field-label">Terms</div>';
							$h .= '<div class="field term-filter-holder">';
							$h .= "</div>";
							$h .= "</div>";
							$h .= "</div>";
							$h .= Fns::rtFieldGenerator(
								array(
									'taxonomy_relation' => array(
										'type' => 'select',
										'label' => 'Relation',
										'class' => 'rt-select2',
										'holderClass' => "term-filter-item-relation tpg-hidden",
										'default'   => 'OR',
										'options' => Options::rtTermRelations()
									)
								)
							);
						}
						$h .= "</div>";
					} else if($key == 'order') {
						$h .= "<div class='rt-tpg-filter {$key} tpg-hidden'>";
						$h .= "<div class='rt-tpg-filter-item'>";
						$h .="<div class='field-holder'>";
						$h .="<div class='field'>";
						$h .= Fns::rtFieldGenerator(
							array(
								'order_by' => array(
									'type' => 'select',
									'label' => 'Order by',
									'class' => 'rt-select2 filter-item',
									'value' => get_post_meta($post->ID, 'order_by', true),
									'options' => Options::rtPostOrderBy(false, true),
									'description' => __('If "Meta value", "Meta value Number" or "Meta value datetime" is chosen then meta key is required.', 'the-post-grid')
								)
							)
						);
						$h .= Fns::rtFieldGenerator(
							array(
								'tpg_meta_key' => array(
									'type' => 'text',
									'label' => 'Meta key',
									'class' => 'rt-select2 filter-item',
									'holderClass' => 'tpg-hidden',
									'value' => get_post_meta($post->ID, 'tpg_meta_key', true)
								)
							)
						);
						$h .= Fns::rtFieldGenerator(
							array(
								'order' => array(
									'type' => 'radio',
									'label' => 'Order',
									'class' => 'rt-select2 filter-item',
									'alignment' => 'vertical',
									'default' => 'DESC',
									'value' => get_post_meta($post->ID, 'order', true),
									'options' => Options::rtPostOrders()
								)
							)
						);
						$h .="</div>";
						$h .="</div>";
						$h .= "</div>";
						$h .= "</div>";
					} else if($key == 'author') {
						$h .= "<div class='rt-tpg-filter {$key} tpg-hidden'>";
						$h .= "<div class='rt-tpg-filter-item'>";
						$h .= Fns::rtFieldGenerator(
							array(
								$key => array(
									'type' => 'select',
									'label' => '',
									'class' => 'rt-select2 filter-item full',
									'value' => get_post_meta($post->ID, $key),
									"multiple" => true,
									'options' => Fns::rt_get_users()
								)
							)
						);
						$h .= "</div>";
						$h .= "</div>";
					} else if($key == 'tpg_post_status'){
						$h .= "<div class='rt-tpg-filter {$key} tpg-hidden'>";
						$h .= "<div class='rt-tpg-filter-item'>";
						$h .= Fns::rtFieldGenerator(
							array(
								$key => array(
									'type' => 'select',
									'label' => '',
									'class' => 'rt-select2 filter-item full',
									'default' => array('publish'),
									'value' => get_post_meta($post->ID, $key),
									"multiple" => true,
									'options' => Options::rtTPGPostStatus()
								)
							)
						);
						$h .= "</div>";
						$h .= "</div>";
					} else if($key == 's'){
						$h .= "<div class='rt-tpg-filter {$key} tpg-hidden'>";
						$h .= "<div class='rt-tpg-filter-item'>";
						$h .= Fns::rtFieldGenerator(
							array(
								$key => array(
									'type' => 'text',
									'label' => 'Keyword',
									'class' => 'filter-item full',
									'value' => get_post_meta($post->ID, $key, true)
								)
							)
						);
						$h .= "</div>";
						$h .= "</div>";
					} else if($key == 'date_range'){
						$range_start = get_post_meta($post->ID, 'date_range_start', true);
						$range_end = get_post_meta($post->ID, 'date_range_end', true);
						$range_value = [
							'start' => $range_start,
							'end' => $range_end
						];
						$h .= "<div class='rt-tpg-filter {$key} tpg-hidden'>";
						$h .= "<div class='rt-tpg-filter-item'>";
						$h .= Fns::rtFieldGenerator(
							array(
								$key=> array(
									'type' => 'date_range',
									'label' => '',
									'class' => 'filter-item full rt-date-range',
									'value' => $range_value,
									'description' => "Date format should be 'yyyy-mm-dd'",
								)
							)
						);
						$h .= "</div>";
						$h .= "</div>";
					}
					//}

					$h .= '</div>';
				}
			}
			$h .= "</div>";
		} else {
			$checked = ( $this->value ? "checked" : null );
			$h       .= "<label><input type='checkbox' {$checked} id='{$this->id}' name='{$this->name}' value='1' />{$this->option}</label>";
		}

		return $h;
	}

	private function radioField() {
		$holderClass = explode(' ', $this->holderClass);
		$this->alignment .= (in_array('pro-field', $holderClass)) && !rtTPG()->hasPro() ? ' disabled' : '';
		$h = null;
		$h .= "<div class='radio-group {$this->alignment}' id='{$this->id}'>";
		if ( is_array( $this->options ) && ! empty( $this->options ) ) {
			foreach ( $this->options as $key => $value ) {
				$checked = ( $key == $this->value ? "checked" : null );
				/*if(empty($checked)) {
					$checked = ( $key == $this->default ? "checked" : null );
				}*/
				$h       .= "<label for='{$this->name}-{$key}'>
                            <input type='radio' id='{$this->id}-{$key}' {$checked} name='{$this->name}' value='{$key}'>{$value}
                            </label>";
			}
		}
		$h .= "</div>";

		return $h;
	}

	/**
	 * Radio Image
	 *
	 * @return String
	 */
	private function radioImage() {
		$h = null;
		$id = 'rttpg-' . $this->name;

		$h .= sprintf("<div class='rttpg-radio-image %s' id='%s'>", esc_attr($this->alignment), esc_attr($id));
		$selected_value = $this->value;

		if ( is_array($this->options) && !empty($this->options) ) {
			foreach ($this->options as $key => $value) {
				$checked = ( $key == $selected_value ? "checked" : null);
				$title = isset( $value['title'] ) && $value['title'] ? esc_html( $value['title'] ) : '';
				$link = isset( $value['layout_link'] ) && $value['layout_link'] ? $value['layout_link'] : '';
				$linkHtml = empty($link) ? esc_html($title) : '<a href="'.esc_url($link).'" target="_blank">'.esc_html($title).'</a>';
				$layout = isset( $value['layout'] ) ?  $value['layout'] : '';
				$taghtml = isset($value['tag']) ? '<div class="rt-tpg-layout-tag"><span>'.$value['tag'].'</span></div>' : '';
				$h .= sprintf('<div class="rt-tpg-radio-layout %7$s"><label data-type="%7$s" class="radio-image %7$s"  for="%2$s">
                            <input type="radio" id="%2$s" %3$s name="%4$s" value="%2$s">
                            <div class="rttpg-radio-image-wrap">
                                <img src="%5$s" title="%6$s" alt="%2$s">
                                <div class="rttpg-checked"><span class="dashicons dashicons-yes"></span></div>
                                %9$s
                            </div>
                        </label>
                        <div class="rttpg-demo-name">%8$s</div>
                        </div>',
					'',
					esc_attr( $key ),
					esc_attr($checked),
					esc_attr($this->name),
					esc_url($value['img']),
					esc_attr($title),
					esc_attr($layout),
					$linkHtml,
					$taghtml
				);
			}
		}
		$h .= "</div>";
		return $h;
	}

	private function dateRange() {
		$h          = null;
		$this->name = ( $this->name ? $this->name : "date-range-" . rand( 0, 1000 ) );
		$h          .= "<div class='date-range-container' id='{$this->id}'>";
		$h          .= "<div class='date-range-content start'><span>" . __( "Start", 'the-post-grid' ) . "</span><input
                            type='text'
                            class='date-range date-range-start {$this->class}'
                            id='{$this->id}-start'
                            value='{$this->value['start']}'
                            name='{$this->name}_start'
                            placeholder='{$this->placeholder}'
                            {$this->attr}
                            /></div>";
		$h          .= "<div class='date-range-content end'><span>" . __( "End", 'the-post-grid' ) . "</span><input
                            type='text'
                            class='date-range date-range-end {$this->class}'
                            id='{$this->id}-end'
                            value='{$this->value['end']}'
                            name='{$this->name}_end'
                            placeholder='{$this->placeholder}'
                            {$this->attr}
                            /></div>";
		$h          .= "</div>";

		return $h;
	}

}