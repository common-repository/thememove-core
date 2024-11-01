<?php
/**
 * Add Switch field for CMB2 plugins
 *
 * @package ThemeMove_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'CMB2_Type_Switch' ) ) {

	/**
	 * CMB2_Type_Switch class
	 */
	class CMB2_Type_Switch {

		/**
		 * Constructor
		 */
		public function __construct() {
			add_action( 'cmb2_render_switch', array( $this, 'cmb2_render_switch' ), 10, 5 );
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
		public function cmb2_render_switch( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {

			$switch            = '<div class="cmb2-switch">';
			$conditional_value = ( isset( $field->args['attributes']['data-conditional-value'] ) ? 'data-conditional-value="' . esc_attr( $field->args['attributes']['data-conditional-value'] ) . '"' : '' );
			$conditional_id    = ( isset( $field->args['attributes']['data-conditional-id'] ) ? ' data-conditional-id="' . esc_attr( $field->args['attributes']['data-conditional-id'] ) . '"' : '' );
			$label_on          = ( isset( $field->args['label'] ) ? esc_attr( $field->args['label']['on'] ) : 'On' );
			$label_off         = ( isset( $field->args['label'] ) ? esc_attr( $field->args['label']['off'] ) : 'Off' );
			$switch           .= '<input ' . $conditional_value . $conditional_id . ' type="radio" id="' . $field->args['_id'] . '1" value="1"  ' . ( 1 === intval( $escaped_value ) ? 'checked="checked"' : '' ) . ' name="' . esc_attr( $field->args['_name'] ) . '" />
				<input ' . $conditional_value . $conditional_id . ' type="radio" id="' . $field->args['_id'] . '2" value="0" ' . ( ( '' === $escaped_value || 0 === intval( $escaped_value ) ) ? 'checked="checked"' : '' ) . ' name="' . esc_attr( $field->args['_name'] ) . '" />
				<label for="' . $field->args['_id'] . '1" class="cmb2-enable ' . ( 1 === intval( $escaped_value ) ? 'selected' : '' ) . '"><span>' . $label_on . '</span></label>
				<label for="' . $field->args['_id'] . '2" class="cmb2-disable ' . ( ( '' === $escaped_value || 0 === intval( $escaped_value ) ) ? 'selected' : '' ) . '"><span>' . $label_off . '</span></label>';

			$switch .= '</div>';
			$switch .= $field_type_object->_desc( true );
			echo $switch; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Load admin scripts
		 */
		public function setup_admin_scripts() {
			wp_enqueue_script( 'cmb2-switch-js', plugins_url( 'js/cmb2-switch.js', __FILE__ ), array(), TMC_VER, true );
		}
	}

	new CMB2_Type_Switch();
}
