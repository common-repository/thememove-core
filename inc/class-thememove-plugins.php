<?php
/**
 * ThemeMove Plugins
 *
 * @package ThemeMove_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ThemeMove Plugins Class
 */
class ThemeMove_Plugins {
	/**
	 * Instance
	 *
	 * @var ThemeMove_Plugins The single instance of the class.
	 */
	private static $instance = null;

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @return ThemeMove_Plugins An instance of the class.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		// Process plugin actions.
		add_action( 'wp_ajax_process_plugin_actions', array( $this, 'process_plugin_actions' ) );
		add_action( 'wp_ajax_nopriv_process_plugin_actions', array( $this, 'process_plugin_actions' ) );
	}

	/**
	 * Get action link for a plugin
	 *
	 * @param object $plugin Plugin slug.
	 * @return string Action link.
	 */
	public static function get_plugin_action( $plugin ) {
		$tgmpa_instance             = TGM_Plugin_Activation::$instance;
		$installed_plugins          = get_plugins();
		$actions                    = '';
		$plugin['sanitized_plugin'] = $plugin['name'];

		// Plugin in wordpress.org.
		if ( ! $plugin['version'] ) {
			$plugin['version'] = $tgmpa_instance->does_plugin_have_update( $plugin['slug'] );
		}

		if ( ! isset( $installed_plugins[ $plugin['file_path'] ] ) ) {
			// Display Install link.
			$actions = sprintf(
				/* translators: %1$s: Install plugin URL, %2$s: Plugin name */
				__( '<a href="%1$s" title="Install %2$s">Install</a>', 'thememove-core' ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.urlencode_urlencode
				esc_url(
					wp_nonce_url(
						add_query_arg(
							array(
								'page'          => rawurlencode( TGM_Plugin_Activation::$instance->menu ),
								'plugin'        => rawurlencode( $plugin['slug'] ),
								'tgmpa-install' => 'install-plugin',
							),
							$tgmpa_instance->get_tgmpa_url()
						),
						'tgmpa-install',
						'tgmpa-nonce'
					)
				),
				$plugin['sanitized_plugin']
			);
		} elseif ( version_compare( $installed_plugins[ $plugin['file_path'] ]['Version'], $plugin['version'], '<' ) ) {
			// Display update link.
			$actions = sprintf(
				/* translators: %1$s: Active plugin URL, %2$s: Plugin name */
				__( '<a href="%1$s" title="Update %2$s">Update</a>', 'thememove-core' ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.urlencode_urlencode
				wp_nonce_url(
					add_query_arg(
						array(
							'page'         => rawurlencode( TGM_Plugin_Activation::$instance->menu ),
							'plugin'       => rawurlencode( $plugin['slug'] ),
							'tgmpa-update' => 'update-plugin',
						),
						$tgmpa_instance->get_tgmpa_url()
					),
					'tgmpa-update',
					'tgmpa-nonce'
				),
				$plugin['sanitized_plugin']
			);
		} elseif ( is_plugin_inactive( $plugin['file_path'] ) ) {
			// Display Active link.
			$actions = sprintf(
				/* translators: %1$s: #, %2$s: Plugin name,%3$s: Plugin slug, %4$s: Download URL, %5$s: Nonce */
				__( '<a href="%1$s" title="Activate %2$s" data-slug="%3$s" data-source="%4$s" data-plugin-action="activate-plugin" data-nonce="%5$s" class="tmc-plugin-link tmc-plugin-link--activate">Activate</a>', 'thememove-core' ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.urlencode_urlencode
				'#',
				$plugin['name'],
				$plugin['slug'],
				$plugin['file_path'],
				wp_create_nonce( 'activate-plugin' )
			);
		} elseif ( is_plugin_active( $plugin['file_path'] ) ) {
			// Display deactivate link.
			$actions = sprintf(
				/* translators: %1$s: #, %2$s: Plugin name,%3$s: Plugin slug, %4$s: Download URL, %5$s: Nonce */
				__( '<a href="%1$s" title="Deactivate %2$s" data-slug="%3$s" data-source="%4$s" data-plugin-action="deactivate-plugin" data-nonce="%5$s" class="tmc-plugin-link tmc-plugin-link--deactivate">Deactivate</a>', 'thememove-core' ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.urlencode_urlencode
				'#',
				$plugin['name'],
				$plugin['slug'],
				$plugin['file_path'],
				wp_create_nonce( 'deactivate-plugin' )
			);
		}

		if ( 'thememove-core' === $plugin['slug'] ) {
			$actions = '';
		}

		return $actions;
	}

	/**
	 * Install, Update, Activate, Deactivate plugin
	 */
	public function process_plugin_actions() {
		$slug          = '';
		$nonce         = '';
		$source        = '';
		$plugin_action = '';

		if ( ! class_exists( 'TGM_Plugin_Activation' ) ) {
			wp_send_json_error( esc_html__( 'TGM_Plugin_Activation does not exist', 'thememove-core' ) );
		}

		// Get action (install, update, activate or deactivate).
		if ( isset( $_POST['plugin_action'] ) ) {  // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$plugin_action = sanitize_text_field( wp_unslash( $_POST['plugin_action'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		}

		if ( ! ThemeMove_Core::verify_nonce( $plugin_action ) ) {
			wp_send_json_error( esc_html__( 'Invalid nonce', 'thememove-core' ) );
		}

		// Get plugin slug.
		if ( isset( $_POST['slug'] ) ) {  // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$slug = sanitize_text_field( wp_unslash( $_POST['slug'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		}

		// Get plugin source.
		if ( isset( $_POST['source'] ) ) {  // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$source = sanitize_text_field( wp_unslash( $_POST['source'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		}

		if ( empty( $source ) ) {
			wp_send_json_error( esc_html__( 'Installation package not available.', 'thememove-core' ) );
		}

		if ( ! class_exists( 'Plugin_Upgrader', false ) ) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		}
		wp_cache_flush();

		// Create a new instance of Plugin_Upgrader.
		$upgrader = new Plugin_Upgrader();

		if ( 'activate-plugin' === $plugin_action ) {
			activate_plugins( $source );
			$nonce = wp_create_nonce( 'deactivate-plugin' );
		}

		if ( 'deactivate-plugin' === $plugin_action ) {
			deactivate_plugins( $source );
			$nonce = wp_create_nonce( 'activate-plugin' );
		}

		wp_send_json_success( $nonce );
	}
}
