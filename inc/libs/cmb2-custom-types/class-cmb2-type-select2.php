<?php
/**
 * Add Select2 for CMB2 plugins
 *
 * @package ThemeMove_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'CMB2_Type_Select2' ) ) {

	/**
	 * CMB2_Type_Tabs class
	 */
	class CMB2_Type_Select2 {

		/**
		 * Constructor
		 */
		public function __construct() {
			add_filter( 'cmb2_render_tmc_select', array( $this, 'render_tmc_select' ), 10, 5 );
			add_filter( 'cmb2_render_tmc_multiselect', array( $this, 'render_tmc_multiselect' ), 10, 5 );
			add_filter( 'cmb2_sanitize_tmc_multiselect', array( $this, 'tmc_multiselect_sanitize' ), 10, 4 );
			add_filter( 'cmb2_types_esc_tmc_multiselect', array( $this, 'tmc_multiselect_escaped_value' ), 10, 3 );
			add_filter( 'cmb2_repeat_table_row_types', array( $this, 'tmc_multiselect_table_row_class' ), 10, 1 );
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
		public function render_tmc_select( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {
			if ( version_compare( CMB2_VERSION, '2.2.2', '>=' ) ) {
				$field_type_object->type = new CMB2_Type_Select( $field_type_object );
			}
			echo $field_type_object->select( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				array(
					'class'            => 'tmc_select2 tmc_select',
					'desc'             => $field_type_object->_desc( true ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					'options'          => '<option></option>' . $field_type_object->concat_items(), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					'data-placeholder' => $field->args( 'attributes', 'placeholder' ) ? $field->args( 'attributes', 'placeholder' ) : $field->args( 'description' ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				)
			);
		}

		/**
		 * Render multi-value select input field
		 *
		 * @param string $field             Instance of the filed.
		 * @param mixed  $escaped_value     Escaped value.
		 * @param int    $object_id         Object ID.
		 * @param string $object_type       Object type.
		 * @param object $field_type_object Filed Type object.
		 */
		public function render_tmc_multiselect( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {
			if ( version_compare( CMB2_VERSION, '2.2.2', '>=' ) ) {
				$field_type_object->type = new CMB2_Type_Select( $field_type_object );
			}
			$a     = $field_type_object->parse_args(
				'tmc_multiselect',
				array(
					'multiple'         => 'multiple',
					'style'            => 'width: 99%',
					'class'            => 'tmc_select2 tmc_multiselect',
					'name'             => $field_type_object->_name() . '[]',
					'id'               => $field_type_object->_id(),
					'desc'             => $field_type_object->_desc( true ),
					'options'          => $this->get_tmc_multiselect_options( $escaped_value, $field_type_object ),
					'data-placeholder' => $field->args( 'attributes', 'placeholder' ) ? $field->args( 'attributes', 'placeholder' ) : $field->args( 'description' ),
				)
			);
			$attrs = $field_type_object->concat_attrs( $a, array( 'desc', 'options' ) );
			echo sprintf( '<select%s>%s</select>%s', $attrs, $a['options'], $a['desc'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Return list of options for tmc_multiselect
		 *
		 * Return the list of options, with selected options at the top preserving their order. This also handles the
		 * removal of selected options which no longer exist in the options array.
		 *
		 * @param mixed  $field_escaped_value Escaped value.
		 * @param object $field_type_object   Filed Type object.
		 */
		public function get_tmc_multiselect_options( $field_escaped_value = array(), $field_type_object ) {

			$options = (array) $field_type_object->field->options();
			// If we have selected items, we need to preserve their order.
			if ( ! empty( $field_escaped_value ) ) {
				$options = $this->sort_array_by_array( $options, $field_escaped_value );
			}
			$selected_items = '';
			$other_items    = '';
			foreach ( $options as $option_value => $option_label ) {
				// Clone args & modify for just this item.
				$option = array(
					'value' => $option_value,
					'label' => $option_label,
				);
				// Split options into those which are selected and the rest.
				if ( in_array( $option_value, (array) $field_escaped_value, true ) ) {
					$option['checked'] = true;
					$selected_items   .= $field_type_object->select_option( $option );
				} else {
					$other_items .= $field_type_object->select_option( $option );
				}
			}

			return $selected_items . $other_items;
		}

		/**
		 * Sort an array by the keys of another array
		 *
		 * @param array $array      Sorted array.
		 * @param array $order_array  Order array.
		 */
		public function sort_array_by_array( array $array, array $order_array ) {
			$ordered = array();
			foreach ( $order_array as $key ) {
				if ( array_key_exists( $key, $array ) ) {
					$ordered[ $key ] = $array[ $key ];
					unset( $array[ $key ] );
				}
			}

			return $ordered + $array;
		}

		/**
		 * Handle sanitization for repeatable fields
		 *
		 * @param boolean $check       Check.
		 * @param mixed   $meta_value  Meta value.
		 * @param int     $object_id   Object id.
		 * @param array   $field_args  Order array.
		 */
		public function tmc_multiselect_sanitize( $check, $meta_value, $object_id, $field_args ) {
			if ( ! is_array( $meta_value ) || ! $field_args['repeatable'] ) {
				return $check;
			}
			foreach ( $meta_value as $key => $val ) {
				$meta_value[ $key ] = array_map( 'sanitize_text_field', $val );
			}

			return $meta_value;
		}

		/**
		 * Handle escaping for repeatable fields
		 *
		 * @param boolean $check       Check.
		 * @param mixed   $meta_value  Meta value.
		 * @param array   $field_args  Order array.
		 */
		public function tmc_multiselect_escaped_value( $check, $meta_value, $field_args ) {
			if ( ! is_array( $meta_value ) || ! $field_args['repeatable'] ) {
				return $check;
			}
			foreach ( $meta_value as $key => $val ) {
				$meta_value[ $key ] = array_map( 'esc_attr', $val );
			}

			return $meta_value;
		}

		/**
		 * Add 'table-layout' class to multi-value select field
		 *
		 * @param boolean $check Check.
		 */
		public function tmc_multiselect_table_row_class( $check ) {
			$check[] = 'tmc_multiselect';

			return $check;
		}

		/**
		 * Enqueue scripts and styles
		 */
		public function setup_admin_scripts() {
			$asset_path = apply_filters( 'tmc_cmb2_field_select2_asset_path', plugins_url( '', __FILE__ ) );

			wp_register_script( 'select2', plugins_url( 'js/select2.min.js', __FILE__ ), array( 'jquery-ui-sortable' ), TMC_VER, true );
			wp_enqueue_script( 'tmc-select2-init',
				plugins_url( 'js/cmb2-select2.js', __FILE__ ),
				array(
					'cmb2-scripts',
					'select2',
				),
				TMC_VER,
				true
			);

			wp_enqueue_style( 'select2', $asset_path . '/css/select2.min.css', array(), TMC_VER );
		}
	}

	new CMB2_Type_Select2();
}
