<?php
/**
 * Add Slider for CMB2 plugins
 *
 * @package ThemeMove_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'CMB2_Type_Slider' ) ) {

	/**
	 * CMB2_Type_Slider class
	 */
	class CMB2_Type_Slider {

		/**
		 * Constructor
		 */
		public function __construct() {
			add_action( 'cmb2_render_slider', array( $this, 'cmb2_render_slider' ), 10, 5 );
			add_action( 'admin_enqueue_scripts', array( $this, 'setup_admin_scripts' ) );
		}

		/**
		 * Add admin scripts
		 */
		public function setup_admin_scripts() {
			wp_enqueue_script( 'cmb2-slider-js', plugins_url( 'js/cmb2-slider.js', __FILE__ ), array(), TMC_VER, true );
		}

		/**
		 * Render HTML
		 *
		 * @param string $field             Instance of the filed.
		 * @param mixed  $escaped_value     Escaped value.
		 * @param int    $object_id         Object ID.
		 * @param string $object_type       Object type.
		 * @param object $field_type_object Filed Type object.
		 */
		public function cmb2_render_slider( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {

			$slider  = '<div class="tmc-cmb2-slider"></div>';
			$slider .= $field_type_object->input(
				array(
					'type'       => 'hidden',
					'class'      => 'cmb2-slider-value',
					'readonly'   => 'readonly',
					'data-start' => abs( $escaped_value ),
					'data-min'   => $field->min(),
					'data-step'  => $field->step(),
					'data-max'   => $field->max(),
					'desc'       => '',
				)
			);

			$slider .= '<span class="cmb2-slider-value-display">' . $field->value_label() . ' <span class="cmb2-slider-value-text"></span></span>';
			$slider .= $field_type_object->_desc( true );
			echo $slider; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	new CMB2_Type_Slider();
}
