<?php
/**
 * Add Tabs for CMB2 plugins
 *
 * @package ThemeMove_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'CMB2_Type_Tabs' ) ) {

	/**
	 * CMB2_Type_Tabs class
	 */
	class CMB2_Type_Tabs {

		/**
		 * Settings
		 *
		 * @var array Control settings array.
		 */
		private $setting = array();

		/**
		 * Control ID.
		 *
		 * @var int Control ID.
		 */
		private $object_id = 0;

		/**
		 * CMB2_Tabs constructor.
		 */
		public function __construct() {
			add_action( 'admin_enqueue_scripts', array( $this, 'setup_admin_scripts' ) );
			add_action( 'cmb2_render_tabs', array( $this, 'render' ), 10, 5 );
			add_filter( 'cmb2_sanitize_tabs', array( $this, 'save' ), 10, 4 );
		}

		/**
		 * Load admin scripts
		 */
		public function setup_admin_scripts() {
			// Enqueue jQuery UI Core.
			wp_enqueue_script( 'jquery-ui-core' );
			wp_enqueue_script( 'jquery-ui-tabs' );
			wp_enqueue_script( 'tmc-cmb2-tabs', plugins_url( 'js/cmb2-tabs.js', __FILE__ ), array(), TMC_VER, true );
			wp_enqueue_script( 'tmc-cmb2-cookie', plugins_url( 'js/js.cookie.min.js', __FILE__ ), array(), TMC_VER, true );
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
		public function render( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {
			$this->setting   = $field->args( 'tabs' );
			$this->object_id = $object_id;
			// Set layout.
			$layout = empty( $this->setting['layout'] ) ? 'ui-tabs-horizontal' : "ui-tabs-{$this->setting['layout']}";
			?>
			<div class="tmc-cmb2-tabs <?php echo esc_attr( $layout ); ?>">
				<?php
				// Render field.
				echo $this->get_tabs(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				?>
			</div>
			<?php
		}

		/**
		 * Render tabs
		 *
		 * @return string
		 */
		public function get_tabs() {
			ob_start();
			?>

			<ul>
				<?php foreach ( $this->setting['tabs'] as $key => $tab ) : ?>
					<li>
						<a href="#<?php echo esc_attr( $tab['id'] ); ?>"><?php echo esc_html( $tab['title'] ); ?></a>
					</li>
				<?php endforeach; ?>
			</ul>

			<?php foreach ( $this->setting['tabs'] as $key => $tab ) : ?>
				<div id="<?php echo esc_attr( $tab['id'] ); ?>">
					<?php
					// Render fields from tab.
					$this->render_fields( $this->setting['config'], $tab['fields'], $this->object_id );
					?>
				</div>
				<?php
			endforeach;

			return ob_get_clean();
		}

		/**
		 * Render fields from tab
		 *
		 * @param array $args Agrument.
		 * @param array $fields Fields.
		 * @param int   $object_id Object id.
		 */
		public function render_fields( $args, $fields, $object_id ) {

			// set options to cmb2.
			$setting_fields = array_merge( $args, array( 'fields' => $fields ) );
			$cmb2           = new \CMB2( $setting_fields, $object_id );
			foreach ( $fields as $key_field => $field ) {
				if ( $cmb2->is_options_page_mb() ) {
					$cmb2->object_type( $args['object_type'] );
				}
				// cmb2 render field.
				$cmb2->render_field( $field );
			}
		}

		/**
		 * Hook: Save field values
		 *
		 * @param array $override_value New value.
		 * @param array $value Old value.
		 * @param int   $post_id Post ID.
		 * @param array $data Tabs configuration.
		 */
		public static function save( $override_value, $value, $post_id, $data ) {

			foreach ( $data['tabs']['tabs'] as $tab ) {
				$setting_fields = array_merge( $data['tabs']['config'], array( 'fields' => $tab['fields'] ) );
				$cmb2           = new \CMB2( $setting_fields, $post_id );
				if ( $cmb2->is_options_page_mb() ) {
					$cmb2_options = cmb2_options( $post_id );
					$values       = $cmb2->get_sanitized_values( $_POST ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
					foreach ( $values as $key => $value ) {
						$cmb2_options->update( $key, $value );
					}
				} else {
					$cmb2->save_fields();
				}
			}
		}
	}

	new CMB2_Type_Tabs();
}
