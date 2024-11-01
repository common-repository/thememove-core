<?php
/**
 * ThemeMove Child Theme Generator
 *
 * @package ThemeMove_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ThemeMove Child Theme Generator Class
 */
final class ThemeMove_Child_Theme_Generator {

	/**
	 * Instance
	 *
	 * @var ThemeMove_Child_Theme_Generator The single instance of the class.
	 */
	private static $instance = null;

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @return ThemeMove_Child_Theme_Generator An instance of the class.
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
		require_once ABSPATH . 'wp-admin/includes/file.php';
		WP_Filesystem();

		// Generate child theme.
		add_action( 'wp_ajax_generate_child_theme', array( $this, 'generate_child_theme' ) );
		add_action( 'wp_ajax_nopriv_generate_child_theme', array( $this, 'generate_child_theme' ) );
	}

	/**
	 * Generate child theme
	 */
	public function generate_child_theme() {

		if ( ! ThemeMove_Core::verify_nonce( 'generate_child_theme' ) ) {
			wp_send_json_error( esc_html__( 'Invalid nonce', 'thememove-core' ) );
		}

		$theme_name = TMC_THEME_NAME . esc_html__( ' Child', 'thememove-core' );
		$theme_desc = '';
		$theme_uri  = '';
		$author     = '';
		$author_uri = '';
		$version    = '';

		if ( isset( $_POST['theme-name'] ) && ! empty( $_POST['theme-name'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$theme_name = sanitize_text_field( wp_unslash( $_POST['theme-name'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		}

		if ( isset( $_POST['theme-desc'] ) && ! empty( $_POST['theme-desc'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$theme_desc = sanitize_text_field( wp_unslash( $_POST['theme-desc'] ) );  // phpcs:ignore WordPress.Security.NonceVerification.Missing
		}

		if ( isset( $_POST['theme-uri'] ) && ! empty( $_POST['theme-uri'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$theme_uri = sanitize_text_field( wp_unslash( $_POST['theme-uri'] ) );  // phpcs:ignore WordPress.Security.NonceVerification.Missing
		}

		if ( isset( $_POST['author'] ) && ! empty( $_POST['author'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$author = sanitize_text_field( wp_unslash( $_POST['author'] ) );  // phpcs:ignore WordPress.Security.NonceVerification.Missing
		}

		if ( isset( $_POST['author-uri'] ) && ! empty( $_POST['author-uri'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$author_uri = sanitize_text_field( wp_unslash( $_POST['author-uri'] ) );  // phpcs:ignore WordPress.Security.NonceVerification.Missing
		}

		if ( isset( $_POST['version'] ) && ! empty( $_POST['version'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$version = sanitize_text_field( wp_unslash( $_POST['version'] ) );  // phpcs:ignore WordPress.Security.NonceVerification.Missing
		}

		$slug            = sanitize_title( $theme_name );
		$child_theme_dir = get_theme_root() . '/' . $slug;

		$child_theme = array(
			'theme_name' => $theme_name,
			'theme_desc' => $theme_desc,
			'theme_uri'  => $theme_uri,
			'author'     => $author,
			'author_uri' => $author_uri,
			'version'    => $version,
			'parent'     => TMC_THEME_SLUG,
			'slug'       => $slug,
		);

		// If the child theme directory is already exists.
		if ( is_dir( $child_theme_dir ) ) {
			// translators: %s: Child theme name.
			wp_send_json_error( sprintf( esc_html__( 'Error: Could not create directory %s. It already exists.', 'thememove-core' ), $slug ) );
		}

		// Create a new directory.
		if ( ! wp_mkdir_p( $child_theme_dir ) ) {
			// translators: %s: Child theme name.
			wp_send_json_error( sprintf( esc_html__( 'Error: Could not create directory %s. This folder is read-only.', 'thememove-core' ), $slug ) );
		}

		// Create style.css.
		if ( ! $this->create_style_css( $child_theme ) ) {
			wp_send_json_error( esc_html__( 'Error: Could not write style.css, permission denined.', 'thememove-core' ) );
		}

		// Create functions.php.
		if ( ! $this->create_functions_php( $child_theme ) ) {
			wp_send_json_error( esc_html__( 'Error: Could not write functions.php, permission denined.', 'thememove-core' ) );
		}

		// Create screenshot.png.
		if ( ! $this->create_screenshot_png( $child_theme ) ) {
			wp_send_json_error( esc_html__( 'Error: Could not create screenshot.png, permission denined.', 'thememove-core' ) );
		}

		// Copy theme mods.
		if ( isset( $_POST['copy-theme-mods'] ) && 'yes' === $_POST['copy-theme-mods'] ) {  // phpcs:ignore WordPress.Security.NonceVerification.Missing
			if ( ! $this->copy_theme_mods( $child_theme ) ) {
				wp_send_json_error( esc_html__( 'Error: Could not copy theme mods, please try again.', 'thememove-core' ) );
			}
		}

		wp_send_json_success(
			esc_url(
				wp_nonce_url(
					add_query_arg(
						array(
							'action'     => 'activate',
							'stylesheet' => $child_theme['slug'],
						),
						admin_url( '/themes.php' )
					),
					'switch-theme_' . $child_theme['slug']
				)
			)
		);
	}

	/**
	 * Create style.css
	 *
	 * @param array $child_theme Child theme data.
	 * @return boolean File creation successful or not.
	 */
	private function create_style_css( $child_theme ) {
		$content  = '';
		$content .= "/*\n";
		$content .= "Theme Name: {$child_theme['theme_name']}\n";
		$content .= "Description: {$child_theme['theme_desc']}\n";
		$content .= "Theme URI: {$child_theme['theme_uri']}\n";
		$content .= "Author: {$child_theme['author']}\n";
		$content .= "Author URI: {$child_theme['author_uri']}\n";
		$content .= "Template: {$child_theme['parent']}\n";
		$content .= "Version: {$child_theme['version']}\n";
		$content .= "Text Domain: {$child_theme['slug']}\n";
		$content .= "*/\n\n";
		$content .= '/* ';
		$content .= esc_html__( 'Write here your own personal stylesheet', 'thememove-core' );
		$content .= " */\n";

		$content = apply_filters( 'tmc_child_theme_style_css_content', $content );

		$style_css = get_theme_root() . '/' . $child_theme['slug'] . '/style.css';

		global $wp_filesystem;
		return $wp_filesystem->put_contents( $style_css, $content, FS_CHMOD_FILE );
	}

	/**
	 * Create functions.php
	 *
	 * @param array $child_theme Child theme data.
	 *
	 * @return boolean File creation successful or not.
	 */
	private function create_functions_php( $child_theme ) {
		$prefix = str_replace( '-', '_', $child_theme['slug'] );

		$content  = '';
		$content .= "<?php\n";
		$content .= "/**\n";
		$content .= ' * ' . esc_html__( 'This file is part of ', 'thememove-core' );
		$content .= "{$child_theme['theme_name']}, a child theme of " . TMC_THEME_NAME . "\n * \n";
		$content .= ' * ' . esc_html__( 'All functions of this file will be loaded before of parent theme functions.', 'thememove-core' ) . "\n";
		$content .= ' * ' . esc_html__( 'Learn more at ', 'thememove-core' ) . 'https://codex.wordpress.org/Child_Themes.' . "\n * \n";
		$content .= ' * ' . esc_html__( 'Note: this function loads the parent stylesheet before, then child theme stylesheet', 'thememove-core' ) . "\n";
		$content .= ' * ' . esc_html__( '(leave it in place unless you know what you are doing.)', 'thememove-core' ) . "\n */\n";
		$content .= "function ${prefix}_enqueue_child_styles() {\n";
		$content .= '	$parent_style = \'parent-style\';' . "\n";
		$content .= '	wp_enqueue_style ( ' . '$parent_style' . ", get_template_directory_uri() . '/style.css' );\n"; // phpcs:ignore Generic.Strings.UnnecessaryStringConcat.Found
		$content .= "	wp_enqueue_style (\n";
		$content .= "		'child-style', \n";
		$content .= "		get_stylesheet_directory_uri() . '/style.css',\n";
		$content .= '		array( ' . '$parent_style' . " ),\n"; // phpcs:ignore Generic.Strings.UnnecessaryStringConcat.Found
		$content .= "		wp_get_theme()->get('Version')\n";
		$content .= "	);\n";
		$content .= "}\n";
		$content .= "add_action( 'wp_enqueue_scripts', '{$prefix}_enqueue_child_styles' );\n\n";
		$content .= '/* ';
		$content .= esc_html__( 'Write here your own functions', 'thememove-core' ) . " */\n";

		$content = apply_filters( 'tmc_child_theme_functions_php_content', $content );

		$functions_php = get_theme_root() . '/' . $child_theme['slug'] . '/functions.php';

		global $wp_filesystem;
		return $wp_filesystem->put_contents( $functions_php, $content, FS_CHMOD_FILE );
	}

	/**
	 * Copy screenshot.png
	 *
	 * @param array $child_theme Child theme data.
	 *
	 * @return boolean File creation successful or not.
	 */
	private function create_screenshot_png( $child_theme ) {

		$theme = wp_get_theme();
		if ( ! empty( $theme['Template'] ) ) {
			$theme = wp_get_theme( $theme['Template'] );
		};

		$file_name         = basename( $theme->get_screenshot() );
		$parent_screenshot = get_theme_root() . '/' . TMC_THEME_SLUG . '/' . $file_name;
		$child_screenshot  = get_theme_root() . '/' . $child_theme['slug'] . '/' . $file_name;

		return copy( $parent_screenshot, $child_screenshot );
	}

	/**
	 * Copy theme mods
	 *
	 * @param array $child_theme Child theme data.
	 *
	 * @return boolean Theme mods was coppied successful or not.
	 */
	private function copy_theme_mods( $child_theme ) {
		$result = false;

		// Get parent theme settings.
		$mods = $this->get_theme_mods( TMC_THEME_SLUG );

		// Handle custom CSS.
		$custom_css = wp_get_custom_css( TMC_THEME_SLUG );
		$result     = wp_update_custom_css_post(
			$custom_css,
			array(
				'stylesheet' => $child_theme['slug'],
			)
		);

		// If ok, set id in child theme mods.
		if ( ! is_wp_error( $result ) ) {
			$post_id                    = $result->ID;
			$mods['custom_css_post_id'] = $post_id;
		}

		// Set new mods based on parent.
		$result = $this->set_theme_mods( $child_theme['slug'], $mods );

		// Handle randomized custom headers.
		$headers = get_posts(
			array(
				'post_type'  => 'attachment',
				'meta_key'   => '_wp_attachment_is_custom_header', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value' => TMC_THEME_SLUG, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
				'orderby'    => 'none',
				'nopaging'   => true,
			)
		);

		foreach ( $headers as $header ) {
			$result = add_post_meta( $header->ID, '_wp_attachment_is_custom_header', $child_theme['slug'] );
		}

		return $result;
	}

	/**
	 * Get theme mods.
	 *
	 * @param string $theme Theme slug.
	 *
	 * @return array Theme mods
	 */
	private function get_theme_mods( $theme ) {
		$mods = get_option( 'theme_mods_' . $theme );

		// Get widgets from active sidebars_widgets array.
		$mods['sidebars_widgets']['data'] = retrieve_widgets();

		return $mods;
	}

	/**
	 * Set theme mods
	 *
	 * @param string $theme Theme slug.
	 * @param array  $mods Theme mods.
	 *
	 * @return boolean Set theme mods successful or not.
	 */
	private function set_theme_mods( $theme, $mods ) {
		$widgets = $mods['sidebars_widgets']['data'];

		// Copy widgets to temp array with time stamp.
		$mods['sidebars_widgets']['time'] = time();

		// Copy temp array to child mods.
		return update_option( 'theme_mods_' . $theme, $mods );
	}
}
