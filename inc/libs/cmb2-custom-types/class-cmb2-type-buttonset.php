<?php
/**
 * Add Buttonset field for CMB2 plugins
 *
 * @package ThemeMove_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'CMB2_Type_Buttonset' ) ) {

	/**
	 * CMB2_Type_Buttonset class
	 */
	class CMB2_Type_Buttonset {

		/**
		 * Constructor
		 */
		public function __construct() {
			add_action( 'cmb2_render_buttonset', array( $this, 'cmb2_render_buttonset' ), 10, 5 );
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
		public function cmb2_render_buttonset( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {

			$buttonset         = '<div class="cmb2-buttonset">';
			$conditional_value = ( isset( $field->args['attributes']['data-conditional-value'] ) ? 'data-conditional-value="' . esc_attr( $field->args['attributes']['data-conditional-value'] ) . '"' : '' );
			$conditional_id    = ( isset( $field->args['attributes']['data-conditional-id'] ) ? ' data-conditional-id="' . esc_attr( $field->args['attributes']['data-conditional-id'] ) . '"' : '' );
			$default_value     = $field->args['attributes']['default'];

			foreach ( $field->options() as $value => $item ) {
				$selected_input = ( ( '' === $escaped_value ? $default_value : $escaped_value ) === $value ) ? 'checked="checked"' : '';
				$selected_label = ( ( '' === $escaped_value ? $default_value : $escaped_value ) === $value ) ? ' selected' : '';

				$buttonset .= '<input ' . $conditional_value . $conditional_id . ' type="radio" id="' . $field->args['_name'] . esc_attr( $value ) . '" name="' . $field->args['_name'] . '" value="' . esc_attr( $value ) . '" ' . $selected_input . ' class="cmb2-buttonset-item">
				<label class="cmb2-buttonset-label state-default' . $selected_label . '" for="' . $field->args['_name'] . esc_attr( $value ) . '"><span class="buttonset-text">' . esc_html( $item ) . '</span></label>';
			}

			$buttonset .= '</div>';
			$buttonset .= $field_type_object->_desc( true );
			echo $buttonset; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Load admin scripts
		 */
		public function setup_admin_scripts() {
			wp_enqueue_script( 'cmb2-buttonset-js', plugins_url( 'js/cmb2-buttonset.js', __FILE__ ), array(), TMC_VER, true );
		}
	}

	new CMB2_Type_Buttonset();
}
