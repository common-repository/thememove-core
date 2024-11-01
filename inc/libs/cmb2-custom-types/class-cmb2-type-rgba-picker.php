<?php
/**
 * Add RGBA color picker for CMB2 plugins
 *
 * @package ThemeMove_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'CMB2_Type_RGBA_Picker' ) ) {

	/**
	 * CMB2_Type_RGBA_Picker class
	 */
	class CMB2_Type_RGBA_Picker {

		/**
		 * Constructor
		 */
		public function __construct() {
			add_action( 'cmb2_render_rgba_colorpicker', array( $this, 'render_color_picker' ), 10, 5 );
			add_action( 'admin_enqueue_scripts', array( $this, 'setup_admin_scripts' ) );
		}

		/**
		 * Hook: Render field
		 *
		 * @param string $field             Instance of the filed.
		 * @param mixed  $escaped_value     Escaped value.
		 * @param int    $object_id         Object ID.
		 * @param string $object_type       Object type.
		 * @param object $field_type_object Filed Type object.
		 */
		public function render_color_picker( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {
			echo $field_type_object->input( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				array(
					'class'              => 'cmb2-colorpicker color-picker',
					'data-default-color' => $field->args( 'default' ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					'data-alpha'         => 'true',
				)
			);
		}

		/**
		 * Load admin scripts
		 */
		public function setup_admin_scripts() {
			wp_enqueue_script( 'wp-color-picker-alpha', plugins_url( 'js/cmb2-rgba-picker.js', __FILE__ ), array( 'wp-color-picker' ), '2.0', true );
			wp_enqueue_style( 'wp-color-picker' );
		}
	}

	new CMB2_Type_RGBA_Picker();
}
