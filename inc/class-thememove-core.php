<?php
/**
 * ThemeMove Core Class
 *
 * @package ThemeMove_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * It should be a final class
 */
final class ThemeMove_Core {

	/**
	 * Instance
	 *
	 * @var ThemeMove_Core The single instance of the class.
	 */
	private static $instance = null;

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @return ThemeMove_Core An instance of the class.
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
		add_action( 'plugins_loaded', array( $this, 'init' ) );
		add_action( 'plugins_loaded', array( $this, 'load_language_file' ) );

		// Delete all transients when switching theme.
		add_action( 'switch_theme', array( $this, 'delete_license_key' ), 10, 3 );

		// Child Theme Generator.
		require_once TMC_DIR . 'inc/class-thememove-child-theme-generator.php';
		ThemeMove_Child_Theme_Generator::instance();

		// Exporter.
		require_once TMC_DIR . 'inc/class-thememove-exporter.php';
		ThemeMove_Exporter::instance();

		// Importer.

		require_once ABSPATH . '/wp-admin/includes/class-wp-importer.php';
		require_once TMC_DIR . 'inc/libs/WordPress Importer/WXRImporter.php';
		require_once TMC_DIR . 'inc/libs/WordPress Importer/WPImporterLogger.php';
		require_once TMC_DIR . 'inc/libs/WordPress Importer/WPImporterLoggerCLI.php';
		require_once TMC_DIR . 'inc/class-wxrimporter.php';
		require_once TMC_DIR . 'inc/class-thememove-importer.php';
		require_once TMC_DIR . 'inc/class-thememove-import-logger.php';
		require_once TMC_DIR . 'inc/class-thememove-content-importer.php';
		require_once TMC_DIR . 'inc/class-thememove-widgets-importer.php';
		ThemeMove_Importer::instance();

		// License.
		require_once TMC_DIR . 'inc/class-thememove-license.php';
		ThemeMove_License::instance();

		// Plugins.
		require_once TMC_DIR . 'inc/class-thememove-plugins.php';
		ThemeMove_Plugins::instance();

		// System Info.
		require_once TMC_DIR . 'inc/class-thememove-system-info.php';
		ThemeMove_System_Info::instance();

		// Updater.
		require_once TMC_DIR . 'inc/class-thememove-updater.php';
		ThemeMove_Updater::instance();
	}

	/**
	 * Initialize the plugin
	 */
	public function init() {

		// Admin menu.
		if ( current_user_can( 'manage_options' ) ) {
			add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		}

		// Load scripts.
		add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_scripts' ) );

		// Refresh trasient.
		add_action( 'wp_ajax_refresh_transients', array( $this, 'refresh_transients' ) );
		add_action( 'wp_ajax_nopriv_refresh_transients', array( $this, 'refresh_transients' ) );

		// Enabling shortcodes in widgets.
		add_filter( 'widget_text', 'do_shortcode' );

		// Load custom CMB2 Types.
		if ( defined( 'CMB2_LOADED' ) ) {
			require_once TMC_DIR . 'inc/libs/cmb2-custom-types/cmb2-custom-types.php';
		}
	}

	/**
	 * Loads textdomain
	 */
	public function load_language_file() {
		$lang_dir = TMC_DIR . '/languages/';
		load_plugin_textdomain( 'thememove-core', false, $lang_dir );
	}

	/**
	 * Add admin menu
	 */
	public function add_admin_menu() {
		$has_new_version = ThemeMove_Updater::has_new_version();
		$patches_count   = ThemeMove_Updater::count_patches();
		$update_count    = 0;

		if ( $patches_count > 0 && $has_new_version ) {
			$update_count = ++$patches_count;
		} elseif ( $patches_count > 0 ) {
			$update_count = $patches_count;
		} elseif ( $has_new_version ) {
			$update_count = 1;
		}

		/* translators: %s: The number of update */
		$update_html  = $update_count ? sprintf( __( '<span class="update-plugins"><span class="plugin-count">%s</span></span>', 'thememove-core' ), $update_count ) : '';
		$menu_title   = $update_count ? __( 'ThemeMove ', 'thememove-core' ) . $update_html : esc_html__( 'ThemeMove', 'thememove-core' );
		$update_title = $update_count ? __( 'Update ', 'thememove-core' ) . $update_html : esc_html__( 'Update', 'thememove-core' );

		add_menu_page(
			esc_html__( 'ThemeMove', 'thememove-core' ),
			$menu_title,
			'manage_options',
			'thememove-core',
			array( $this, 'render_welcome_page' ),
			TMC_URL . 'assets/images/icon.png',
			6
		);

		add_submenu_page( 'thememove-core', esc_html__( 'Welcome - ThemeMove Core', 'thememove-core' ), esc_html__( 'Welcome', 'thememove-core' ), 'manage_options', 'thememove-core' );
		add_submenu_page( 'thememove-core', esc_html__( 'Update - ThemeMove Core', 'thememove-core' ), $update_title, 'manage_options', 'thememove-core-update', array( $this, 'render_update_page' ) );
		add_submenu_page( 'thememove-core', esc_html__( 'Import Demo Data - ThemeMove Core', 'thememove-core' ), esc_html__( 'Import Demo Data', 'thememove-core' ), 'manage_options', 'thememove-core-import', array( $this, 'render_import_page' ) );
		add_submenu_page( 'thememove-core', esc_html__( 'Tools - ThemeMove Core', 'thememove-core' ), esc_html__( 'Tools', 'thememove-core' ), 'manage_options', 'thememove-core-tools', array( $this, 'render_tools_page' ) );
	}

	/**
	 * Add welcome page
	 */
	public function render_welcome_page() {
		include_once TMC_DIR . 'views/page-welcome.php';
	}

	/**
	 * Update page
	 */
	public function render_update_page() {
		include_once TMC_DIR . 'views/page-update.php';
	}

	/**
	 * Tools page
	 */
	public function render_tools_page() {
		$current_tab = ( isset( $_GET['tab'] ) && ! empty( $_GET['tab'] ) ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'child_theme'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
		$tabs        = array(
			'child_theme' => esc_html__( 'Child Theme Generator', 'thememove-core' ),
			'system_info' => esc_html__( 'System Info', 'thememove-core' ),
			'export'      => esc_html__( 'Export', 'thememove-core' ),
		);

		$tabs = apply_filters( 'tmc_render_tools_page_tabs', $tabs );

		$tabs_html = '<div class="nav-tab-wrapper tmc-nav-tab-wrapper">';

		foreach ( $tabs as $tab => $name ) {
			$class      = ( $tab === $current_tab ) ? ' nav-tab-active' : '';
			$tabs_html .= '<a class="nav-tab' . $class . '" href="?page=thememove-core-tools&tab=' . $tab . '">' . $name . '</a>';
		}

		$tabs_html .= '</div>';

		include_once TMC_DIR . 'views/page-tools.php';
	}

	/**
	 * Import page
	 */
	public function render_import_page() {
		include_once TMC_DIR . 'views/page-import.php';
	}

	/**
	 * Load scripts
	 *
	 * @param string $hook Hook.
	 */
	public function load_admin_scripts( $hook ) {
		if ( strpos( $hook, 'thememove-core' ) ) {

			$screen    = get_current_screen();
			$screen_id = $screen->id;

			// Font Awesome.
			wp_enqueue_script( 'font-awesome', 'https://kit.fontawesome.com/8bbdf860ba.js', array(), '1.0' ); //phpcs:ignore WordPress.WP.EnqueuedResourceParameters.NotInFooter

			// Lottie.
			wp_enqueue_script( 'lottie', TMC_URL . 'assets/libs/lottie/lottie.min.js', array(), '5.5.6', true );

			if ( false !== strpos( $screen_id, 'thememove-core-import' ) ) {
				// Enqueue Magnific Popup.
				wp_enqueue_style( 'magnific-popup', TMC_URL . 'assets/libs/magnific-popup/magnific-popup.css', array(), TMC_VER );
				wp_enqueue_script( 'magnific-popup', TMC_URL . 'assets/libs/magnific-popup/jquery.magnific-popup.min.js', array(), '1.1.0', true );
			}

			if ( false !== strpos( $screen_id, 'thememove-core-import' ) || false !== strpos( $screen_id, 'thememove-core-tools' ) ) {
				// Hint CSS.
				wp_enqueue_style( 'hint', TMC_URL . 'assets/libs/hint/hint.min.css', array(), TMC_VER );
			}

			// ThemeMove Core style & script.
			wp_enqueue_style( 'thememove-core', TMC_URL . 'assets/css/thememove-core.css', array(), TMC_VER );
			wp_enqueue_script( 'thememove-core', TMC_URL . 'assets/js/thememove-core.js', array( 'jquery' ), TMC_VER, true );
			wp_localize_script(
				'thememove-core',
				'tmcVars',
				array(
					'ajax_url'      => admin_url( 'admin-ajax.php' ),
					'animation_url' => TMC_URL . 'assets/animation/',
				)
			);
		}
	}

	/**
	 * Check theme support
	 *
	 * @return boolean Current theme supports ThemeMove Core or not?
	 */
	public static function is_theme_support() {
		return current_theme_supports( 'thememove-core' );
	}

	/**
	 * Get theme information
	 *
	 * @return boolean Theme information.
	 */
	public static function get_theme_info() {
		return apply_filters(
			'tmc_info',
			array(
				'api_url'     => 'https://thememove.com',
				'desc'        => 'Thank you for using our theme, please reward it a full five-star &#9733;&#9733;&#9733;&#9733;&#9733; rating.',
				'docs_url'    => 'https://document.thememove.com/' . TMC_THEME_SLUG,
				'download_id' => '',
				'faqs_url'    => 'https://thememove.ticksy.com/articles/',
				'patches_url' => 'https://patches.thememove.com/',
				'support_url' => 'https://thememove.ticksy.com/',
				'thumbnail'   => TMC_URL . 'assets/images/tm-logo.png',
			)
		);
	}

	/**
	 * Send request to thememove.com
	 *
	 * @param string $action Action name (activate_license, deactivate_license, check_license or get_version).
	 * @param string $license License key.
	 * @param string $nonce Nonce.
	 *
	 * @return string Result JSON.
	 */
	public static function send_request( $action, $license = '', $nonce = '' ) {

		if ( ! self::verify_nonce( $action, $nonce ) ) {
			return array(
				'error' => esc_html__( 'Invalid nonce', 'thememove-core' ),
			);
		}

		$args = array(
			'timeout' => 20,
		);
		if ( empty( $license ) ) {
			$license = isset( $_POST['license'] ) ? sanitize_text_field( wp_unslash( $_POST['license'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		}
		$url         = site_url( '/' );
		$item_name   = rawurlencode( TMC_THEME_NAME );
		$theme_info  = self::get_theme_info();
		$request_url = "{$theme_info['api_url']}/?edd_action={$action}&license={$license}&url={$url}&item_name={$item_name}";

		$request = wp_remote_post( $request_url, $args );
		$result  = json_decode( wp_remote_retrieve_body( $request ), true );

		if ( 'activate_license' === $action ) {
			update_option( 'tmc_license_key', $license );
		}

		return $result;
	}

	/**
	 * Delete license key when switch the theme.
	 *
	 * @param string   $new_name  Name of the new theme.
	 * @param WP_Theme $new_theme WP_Theme instance of the new theme.
	 * @param WP_Theme $old_theme WP_Theme instance of the old theme.
	 */
	public function delete_license_key( $new_name, $new_theme, $old_theme ) {
		// Only delete license key if the new theme is not a child theme of the old one.
		if ( $new_theme->Template !== $old_theme->Template ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			delete_option( 'tmc_license_key ' );
			delete_transient( 'tmc_' . TMC_THEME_SLUG . '_license_info' );
			delete_transient( 'tmc_' . TMC_THEME_SLUG . '_version' );
			delete_transient( 'tmc_' . TMC_THEME_SLUG . '_patches_' . TMC_THEME_VERSION );
		}
	}

	/**
	 * Refresh transient.
	 */
	public function refresh_transients() {
		delete_transient( 'tmc_' . TMC_THEME_SLUG . '_license_info' );
		delete_transient( 'tmc_' . TMC_THEME_SLUG . '_version' );
		delete_transient( 'tmc_' . TMC_THEME_SLUG . '_patches_' . TMC_THEME_VERSION );

		// Set new transient.
		ThemeMove_License::get_license_info();
		ThemeMove_Updater::get_version();
		ThemeMove_Updater::get_patches();

		wp_send_json_success();
	}

	/**
	 * Check nonce
	 *
	 * @param string $action Action name.
	 * @param string $nonce Nonce.
	 */
	public static function verify_nonce( $action = '', $nonce = '' ) {

		if ( ! $nonce && isset( $_REQUEST['_wpnonce'] ) ) {
			$nonce = sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) );
		}

		return wp_verify_nonce( $nonce, $action );
	}
}
