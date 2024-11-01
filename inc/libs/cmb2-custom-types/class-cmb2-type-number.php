<?php
/**
 * Add Number field for CMB2 plugins
 *
 * @package ThemeMove_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'CMB2_Type_Number' ) ) {

	/**
	 * CMB2_Type_Number class
	 */
	class CMB2_Type_Number {

		/**
		 * Constructor
		 */
		public function __construct() {
			add_action( 'cmb2_render_number', array( $this, 'tmc_cmb2_render_number' ), 10, 5 );
			add_filter( 'cmb2_sanitize_number', array( $this, 'tmc_cmb2_sanitize_number' ), 10, 2 );
			add_action( 'admin_enqueue_scripts', array( $this, 'setup_admin_scripts' ) );
		}

		/**
		 * Render the 'number' custom field type
		 *
		 * @param string $field             Instance of the filed.
		 * @param mixed  $escaped_value     Escaped value.
		 * @param int    $object_id         Object ID.
		 * @param string $object_type       Object type.
		 * @param object $field_type_object Filed Type object.
		 */
		public function tmc_cmb2_render_number( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {

			$options = $field_type_object->field->args( 'options' );

			$value = $escaped_value ? $escaped_value : $field_type_object->field->args( 'default' );

			?>
			<div class="cmb2_number">
				<?php if ( isset( $options['prefix'] ) && $options['prefix'] ) { ?>
					<span><?php echo esc_attr( $options['prefix'] ); ?></span>
				<?php } ?>
				<input type="button" value="-" class="minus"/>
				<input type="number"
					<?php echo isset( $options['min'] ) ? 'min="' . esc_attr( $options['min'] ) . '"' : ''; ?>
					<?php echo isset( $options['max'] ) ? 'max="' . esc_attr( $options['max'] ) . '"' : ''; ?>
					<?php echo isset( $options['step'] ) ? 'step="' . esc_attr( $options['step'] ) . '"' : ''; ?>
						class="cmb_text_small"
						name="<?php echo esc_attr( $field_type_object->_name() ); ?>"
						id="<?php echo esc_attr( $field_type_object->_id() ); ?>"
						value="<?php echo esc_attr( $value ); ?>"
				>
				<input type="button" value="+" class="plus"/>
				<?php if ( isset( $options['suffix'] ) && $options['suffix'] ) { ?>
					<span><?php echo esc_attr( $options['suffix'] ); ?></span>
				<?php } ?>
			</div>
			<?php if ( $field_type_object->_desc() ) { ?>
				<p class="cmb2-metabox-description">
					<?php echo $field_type_object->_desc(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</p>
				<?php
			}
		}

		/**
		 * Sanitize the field
		 *
		 * @param int $num Number.
		 * @param int $new Sanitized number.
		 *
		 * @return mixed
		 */
		public function tmc_cmb2_sanitize_number( $num, $new ) {
			$new = preg_replace( '/[^0-9]/', '', $new );

			return $new;
		}

		/**
		 * Load admin scripts
		 */
		public function setup_admin_scripts() {
			wp_enqueue_script( 'cmb2-number-js', plugins_url( 'js/cmb2-number.js', __FILE__ ), array(), TMC_VER, true );
		}
	}

	new CMB2_Type_Number();
}
