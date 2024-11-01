<?php
/**
 * Add Tabs for CMB2 plugins
 *
 * @package ThemeMove_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'CMB2_Type_Radio_Image' ) ) {

	/**
	 * CMB2_Type_Radio_Image class
	 */
	class CMB2_Type_Radio_Image {

		/**
		 * Constructor
		 */
		public function __construct() {
			add_action( 'cmb2_render_radio_image', array( $this, 'callback' ), 10, 5 );
			add_filter( 'cmb2_list_input_attributes', array( $this, 'attributes' ), 10, 4 );
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
		public function callback( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {
			echo $field_type_object->radio(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Get input attributes
		 *
		 * @param string $args     Agruments.
		 * @param mixed  $defaults Default valute.
		 * @param int    $field    Field.
		 * @param string $cmb      Metabox.
		 */
		public function attributes( $args, $defaults, $field, $cmb ) {
			if ( 'radio_image' === $field->args['type'] && isset( $field->args['images'] ) ) {
				foreach ( $field->args['images'] as $field_id => $image ) {
					if ( $field_id === $args['value'] ) {
						$image         = trailingslashit( $field->args['images_path'] ) . $image;
						$args['label'] = '<img src="' . $image . '" alt="' . $args['value'] . '" title="' . $args['label'] . '" /><br/><span>' . $args['label'] . '</span>';
					}
				}
			}

			return $args;
		}
	}

	new CMB2_Type_Radio_Image();
}
