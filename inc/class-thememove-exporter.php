<?php
/**
 * ThemeMove Exporter
 *
 * @package ThemeMove_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ThemeMove Exporter Class
 */
class ThemeMove_Exporter {

	/**
	 * Instance
	 *
	 * @var ThemeMove_Exporter The single instance of the class.
	 */
	private static $instance = null;

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @return ThemeMove_Exporter An instance of the class.
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
		// Export content.
		add_filter( 'export_wp_filename', array( $this, 'export_wp_filename' ) );
		add_action( 'admin_post_export_content', array( $this, 'export_content' ) );

		// Export Widgets.
		add_action( 'admin_post_export_widgets', array( $this, 'export_widgets' ) );

		// Export Customizer.
		add_action( 'admin_post_export_customizer_settings', array( $this, 'export_customizer_settings' ) );

		// Export Menu Locations.
		add_action( 'admin_post_export_menus', array( $this, 'export_menus' ) );

		// Export Page Options.
		add_action( 'admin_post_export_page_options', array( $this, 'export_page_options' ) );

		// Media Package.
		add_action( 'admin_post_export_media_package', array( $this, 'export_media_package' ) );

		// Placeholder Media Package.
		add_action( 'admin_post_export_placeholder_media_package', array( $this, 'export_placeholder_media_package' ) );

		// WooCommerce Settings.
		add_action( 'admin_post_export_woocommerce_settings', array( $this, 'export_woocommerce_settings' ) );

		// Slider Revolution.
		add_action( 'admin_post_export_slider_revolution', array( $this, 'export_slider_revolution' ) );

		// Elementor Settings.
		add_action( 'admin_post_export_elementor_settings', array( $this, 'export_elementor_settings' ) );

		// Elfsight Instagram Feed Widgets.
		add_action( 'admin_post_export_elfsight_instagram_feed', array( $this, 'export_elfsight_instagram_feed' ) );
	}

	/**
	 * Export items
	 */
	public static function get_export_items() {

		$export_items = array(
			array(
				'name'        => esc_html__( 'Content', 'thememove-core' ),
				'action'      => 'export_content',
				'icon'        => 'fab fa-wordpress',
				'description' => esc_html__( 'Create an XML file containing your posts, pages, comments, custom fields, categories, and tags', 'thememove-core' ),
			),
			array(
				'name'        => esc_html__( 'Widgets', 'thememove-core' ),
				'action'      => 'export_widgets',
				'icon'        => 'fad fa-shapes',
				'description' => esc_html__( 'Create a text file containing your widgets', 'thememove-core' ),
			),
			array(
				'name'        => esc_html__( 'Customizer Settings', 'thememove-core' ),
				'action'      => 'export_customizer_settings',
				'icon'        => 'fad fa-tools',
				'description' => esc_html__( 'Create a text file containing your customizer settings (in Appearance > Customize)', 'thememove-core' ),
			),
			array(
				'name'        => esc_html__( 'Menus', 'thememove-core' ),
				'action'      => 'export_menus',
				'icon'        => 'fad fa-layer-group',
				'description' => esc_html__( 'Create a text file containing your menus', 'thememove-core' ),
			),
			array(
				'name'        => esc_html__( 'Page Options', 'thememove-core' ),
				'action'      => 'export_page_options',
				'icon'        => 'fad fa-file-alt',
				'description' => esc_html__( 'Create a text file containing the Homepage & Post Page settings', 'thememove-core' ),
			),
		);

		if ( class_exists( 'ZipArchive' ) ) {
			$export_items[] = array(
				'name'              => esc_html__( 'Media Package', 'thememove-core' ),
				'action'            => 'export_media_package',
				'icon'              => 'fad fa-images',
				'input_file_name'   => true,
				'default_file_name' => 'media-01',
				'description'       => esc_html__( 'Create a zip package containing all files in the wp-content/uploads directory', 'thememove-core' ),
			);

			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				$skip_prefix    = apply_filters( 'tmc_placeholder_skip_prefix', '__' );
				$export_items[] = array(
					'name'              => esc_html__( 'Placeholder Images', 'thememove-core' ),
					'action'            => 'export_placeholder_media_package',
					'icon'              => 'fal fa-images',
					'input_file_name'   => true,
					'default_file_name' => 'placeholder-media-01',
					// translators: %s: Placeholder skip prefix.
					'description'       => sprintf( esc_html__( 'Create a zip package containing all images that have been generated at placeholder.com and all other media files in the wp-content/uploads directory. NOTE: You can skip generating placeholder by adding the prefix %1$s in the file name, eg. %2$slogo.jpg. It will take a long time to create placeholder images.', 'thememove-core' ), esc_html( $skip_prefix ), esc_html( $skip_prefix ) ),
				);
			}
		}

		if ( class_exists( 'WooCommerce' ) && version_compare( WC_VERSION, '3.3.0', '>=' ) ) {
			$export_items[] = array(
				'name'        => esc_html__( 'WooCommerce', 'thememove-core' ),
				'action'      => 'export_woocommerce_settings',
				'icon'        => esc_url( TMC_URL . 'assets/images/woocommerce-logo.png' ),
				'description' => esc_html__( 'Create a text file containing all WooCommerce settings in Appearance > Customize > WooCommerce', 'thememove-core' ),
			);
		}

		if ( class_exists( 'Essential_Grid' ) ) {
			$export_items[] = array(
				'name'            => esc_html__( 'Essential Grid', 'thememove-core' ),
				'action'          => 'export_essential_grid',
				'icon'            => esc_url( TMC_URL . 'assets/images/ess-grid-logo.png' ),
				'export_page_url' => esc_url(
					add_query_arg(
						array(
							'page' => 'essential-grid-import-export',
						),
						admin_url( '/admin.php' )
					)
				),
				'description'     => esc_html__( 'Create a text file containing all Essential Grid settings', 'thememove-core' ),
			);
		}

		if ( class_exists( 'RevSliderAdmin' ) ) {
			$export_items[] = array(
				'name'        => esc_html__( 'Slider Revolution', 'thememove-core' ),
				'action'      => 'export_slider_revolution',
				'icon'        => esc_url( TMC_URL . 'assets/images/rev-slider-logo.png' ),
				'description' => esc_html__( 'Create a text file containing data of Slider Revolution plugin', 'thememove-core' ),
			);
		}

		if ( did_action( 'elementor/loaded' ) ) {
			$export_items[] = array(
				'name'        => esc_html__( 'Elementor Settings', 'thememove-core' ),
				'action'      => 'export_elementor_settings',
				'icon'        => esc_url( TMC_URL . 'assets/images/elementor-logo.jpg' ),
				'description' => esc_html__( 'Create a text file containing all settings of Elementor plugin', 'thememove-core' ),
			);
		}

		if ( class_exists( 'ElfsightInstagramFeedPlugin' ) ) {
			$export_items[] = array(
				'name'        => esc_html__( 'Instagram Feed Widgets', 'thememove-core' ),
				'action'      => 'export_elfsight_instagram_feed',
				'icon'        => esc_url( TMC_URL . 'assets/images/elfsight-instagram-feed-logo.png' ),
				'description' => esc_html__( 'Create a text file containing all Insgatram Feed Widgets', 'thememove-core' ),
			);
		}

		return apply_filters( 'tmc_export_items', $export_items );
	}

	/**
	 * Allowed File Extensions.
	 * Used when export the media package.
	 *
	 * @return array The array of allowed file extensions.
	 */
	protected function get_allowed_exts() {
		$mime_types   = wp_get_mime_types();
		$allowed_exts = array();

		foreach ( $mime_types as $key => $mime_type ) {
			if ( strpos( $key, '|' ) !== false ) {
				$types = explode( '|', $key );

				foreach ( $types as $type ) {
					$allowed_exts[] = $type;
				}
			} else {
				$allowed_exts[] = $key;
			}
		}

		$allowed_exts = apply_filters( 'tmc_export_allowed_exts', $allowed_exts );

		return $allowed_exts;
	}

	/**
	 * Save export file
	 *
	 * @param string $file_name File Name.
	 * @param string $file_content File Content.
	 */
	public function save_file( $file_name, $file_content ) {
		ob_get_clean();

		header( 'Content-Type: application/text', true, 200 );
		header( "Content-Disposition: attachment; filename={$file_name}" );
		header( 'Pragma: no-cache' );
		header( 'Expires: 0' );

		ob_end_clean();
		flush();

		// Output file contents.
		echo $file_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		exit;
	}

	/**
	 * Change the content export filename.
	 *
	 * @return string WP filename.
	 */
	public function export_wp_filename() {
		return 'content.xml';
	}

	/**
	 * Export content
	 */
	public function export_content() {

		if ( ! ThemeMove_Core::verify_nonce( 'export_content' ) ) {
			wp_die( esc_html__( 'Invalid nonce', 'thememove-core' ) );
		}

		require_once ABSPATH . 'wp-admin/includes/export.php';
		export_wp();
	}

	/**
	 * Export widgets
	 * Copy from Widget Importer & Exporter plugin: https://wordpress.org/plugins/widget-importer-exporter/
	 */
	public function export_widgets() {

		if ( ! ThemeMove_Core::verify_nonce( 'export_widgets' ) ) {
			wp_die( esc_html__( 'Invalid nonce', 'thememove-core' ) );
		}

		// Get all available widgets site supports.
		$available_widgets = $this->available_widgets();

		// Get all widget instances for each widget.
		$widget_instances = array();

		// Loop widgets.
		foreach ( $available_widgets as $widget_data ) {

			// Get all instances for this ID base.
			$instances = get_option( 'widget_' . $widget_data['id_base'] );

			if ( ! empty( $instances ) ) {
				foreach ( $instances as $instance_id => $instance_data ) {

					// Key is ID (not _multiwidget).
					if ( is_numeric( $instance_id ) ) {
						$unique_instance_id                      = $widget_data['id_base'] . '-' . $instance_id;
						$widget_instances[ $unique_instance_id ] = $instance_data;
					}
				}
			}
		}

		// Gather sidebars with their widget instances.
		$sidebars_widgets          = get_option( 'sidebars_widgets' );
		$sidebars_widget_instances = array();
		foreach ( $sidebars_widgets as $sidebar_id => $widget_ids ) {

			// Skip inactive widgets.
			if ( 'wp_inactive_widgets' === $sidebar_id ) {
				continue;
			}

			// Skip if no data or not an array (array_version).
			if ( ! is_array( $widget_ids ) || empty( $widget_ids ) ) {
				continue;
			}

			// Loop widget IDs for this sidebar.
			foreach ( $widget_ids as $widget_id ) {

				// Is there an instance for this widget ID?
				if ( isset( $widget_instances[ $widget_id ] ) ) {

					// Add to array.
					$sidebars_widget_instances[ $sidebar_id ][ $widget_id ] = $widget_instances[ $widget_id ];
				}
			}
		}

		$data = apply_filters( 'tmc_export_widgets_data', $sidebars_widget_instances );
		$this->save_file( 'widgets.json', wp_json_encode( $data ) );
	}

	/**
	 * Available widgets
	 *
	 * Gather site's widgets into array with ID base, name, etc.
	 * Used by export and import functions.
	 *
	 * @return array Widget information
	 */
	private function available_widgets() {
		global $wp_registered_widget_controls;

		$widget_controls = $wp_registered_widget_controls;

		$available_widgets = array();

		foreach ( $widget_controls as $widget ) {

			if ( ! empty( $widget['id_base'] ) && ! isset( $available_widgets[ $widget['id_base'] ] ) ) {
				$available_widgets[ $widget['id_base'] ]['id_base'] = $widget['id_base'];
				$available_widgets[ $widget['id_base'] ]['name']    = $widget['name'];
			}
		}

		return $available_widgets;
	}

	/**
	 * Export customizer settings.
	 */
	public function export_customizer_settings() {

		if ( ! ThemeMove_Core::verify_nonce( 'export_customizer_settings' ) ) {
			wp_die( esc_html__( 'Invalid nonce', 'thememove-core' ) );
		}

		$data = get_theme_mods();
		unset( $data['nav_menu_locations'] );

		$data = apply_filters( 'tmc_export_customizer_settings_data', $data );
		$this->save_file( 'customizer.json', wp_json_encode( $data ) );
	}

	/**
	 * Export Menus
	 */
	public function export_menus() {

		if ( ! ThemeMove_Core::verify_nonce( 'export_menus' ) ) {
			wp_die( esc_html__( 'Invalid nonce', 'thememove-core' ) );
		}

		global $wpdb;
		$data        = array();
		$locations   = get_nav_menu_locations();
		$terms_table = $wpdb->prefix . 'terms';

		foreach ( (array) $locations as $location => $menu_id ) {
			$menu_slug = $wpdb->get_results( "SELECT * FROM $terms_table where term_id={$menu_id}", ARRAY_A ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching
			if ( ! empty( $menu_slug ) ) {
				$data[ $location ] = $menu_slug[0]['slug'];
			}
		}

		$data = apply_filters( 'tmc_export_menus_data', $data );
		$this->save_file( 'menus.json', wp_json_encode( $data ) );
	}

	/**
	 * Export page options.
	 */
	public function export_page_options() {

		if ( ! ThemeMove_Core::verify_nonce( 'export_page_options' ) ) {
			wp_die( esc_html__( 'Invalid nonce', 'thememove-core' ) );
		}

		$data = array();

		$data['show_on_front'] = get_option( 'show_on_front' );

				// Get front page title.
		$front_page_id = intval( get_option( 'page_on_front' ) );
		if ( 0 !== $front_page_id ) {
			$data['page_on_front'] = get_the_title( $front_page_id );
		}

		// Get blog page title.
		$blog_page_id = intval( get_option( 'page_for_posts' ) );
		if ( 0 !== $blog_page_id ) {
			$data['page_for_posts'] = get_the_title( $blog_page_id );
		}

		$data = apply_filters( 'tmc_export_page_options_data', $data );
		$this->save_file( 'page-options.json', wp_json_encode( $data ) );
	}

	/**
	 * Export media package.
	 */
	public function export_media_package() {

		if ( ! ThemeMove_Core::verify_nonce( 'export_media_package' ) ) {
			wp_die( esc_html__( 'Invalid nonce', 'thememove-core' ) );
		}

		$file_name = isset( $_POST['media-package-file-name'] ) ? TMC_THEME_SLUG . '-' . sanitize_text_field( wp_unslash( $_POST['media-package-file-name'] ) ) . '.zip' : 'media-01.zip'; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$file_name = apply_filters( 'tmc_export_archive_file_name', $file_name );

		$source    = WP_CONTENT_DIR . '/uploads';
		$file_path = WP_CONTENT_DIR . '/' . $file_name;

		// Check the source folder is writeable or not?
		if ( ! is_writeable( WP_CONTENT_DIR ) ) {
			wp_die( __( 'Could not write files into \'wp-content\' directory, permission denined.', 'thememove-core' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		// Action: tmc_before_create_media_package.
		do_action( 'tmc_before_create_media_package' );

		if ( ! $this->create_media_package( $source, $file_path ) ) {
			wp_die( esc_html__( 'Could not create the media package, please try again.', 'thememove-core' ) );
		}

		// Action: tmc_after_create_media_package.
		do_action( 'tmc_after_create_media_package' );

		// Download zip archive.
		if ( file_exists( $file_path ) ) {
			ob_get_clean();

			header( 'Content-Type: application/zip', true, 200 );
			header( "Content-Disposition: attachment; filename={$file_name}" );
			header( 'Content-Length: ' . filesize( $file_path ) );
			header( 'Pragma: no-cache' );
			header( 'Expires: 0' );

			ob_end_clean();
			flush();

			if ( ! readfile( "{$file_path}" ) ) { // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_readfile
				// translators: $s: Archive file name.
				wp_die( sprintf( esc_html__( 'Could not read file %s.', 'thememove-core' ), esc_html( $file_path ) ) );
			}

			// Delete the file in wp-content directory.
			unlink( $file_path );
			exit;
		} else {
			// translators: $s: Archive file name.
			wp_die( sprintf( esc_html__( 'The file %s does not exists.', 'thememove-core' ), esc_html( $file_path ) ) );
		}
	}

	/**
	 * Export placeholder media package.
	 */
	public function export_placeholder_media_package() {

		if ( ! ThemeMove_Core::verify_nonce( 'export_placeholder_media_package' ) ) {
			wp_die( esc_html__( 'Invalid nonce', 'thememove-core' ) );
		}

		$placeholder_dir_name = isset( $_POST['placeholder-images-file-name'] ) ? TMC_THEME_SLUG . '-' . sanitize_text_field( wp_unslash( $_POST['placeholder-images-file-name'] ) ) : 'placeholder-media-01'; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$placeholder_dir_name = apply_filters( 'tmc_export_placeholder_dir_name', $placeholder_dir_name );

		$file_name = $placeholder_dir_name . '.zip';
		$file_name = apply_filters( 'tmc_export_placeholder_archive_file_name', $file_name );

		$source           = WP_CONTENT_DIR . '/uploads';
		$placeholder_path = WP_CONTENT_DIR . '/' . $placeholder_dir_name;
		$file_path        = WP_CONTENT_DIR . '/' . $file_name;

		// Check the source folder is writeable or not?
		if ( ! is_writeable( WP_CONTENT_DIR ) ) {
			wp_die( __( 'Could not write files into \'wp-content\' directory, permission denined.', 'thememove-core' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		// Action: tmc_before_generate_placeholder_images.
		do_action( 'tmc_before_generate_placeholder_images' );

		if ( ! $this->generate_placeholder_images( $source, $placeholder_path ) ) {
			wp_die( esc_html__( 'Could not create the placholder images directory, please try again.', 'thememove-core' ) );
		}

		// Action: tmc_after_generate_placeholder_images.
		do_action( 'tmc_after_generate_placeholder_images' );

		// Action: tmc_before_create_placeholder_media_package.
		do_action( 'tmc_before_create_placeholder_media_package' );

		if ( ! $this->create_media_package( $placeholder_path, $file_path ) ) {
			wp_die( esc_html__( 'Could not create the placeholder media package, please try again.', 'thememove-core' ) );
		}

		// Action: tmc_after_create_placeholder_media_package.
		do_action( 'tmc_after_create_placeholder_media_package' );

		// Download zip archive.
		if ( file_exists( $file_path ) ) {
			ob_get_clean();

			header( 'Content-Type: application/zip', true, 200 );
			header( "Content-Disposition: attachment; filename={$file_name}" );
			header( 'Content-Length: ' . filesize( $file_path ) );
			header( 'Pragma: no-cache' );
			header( 'Expires: 0' );

			ob_end_clean();
			flush();

			if ( ! readfile( "{$file_path}" ) ) { // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_readfile
				// translators: $s: Archive file name.
				wp_die( sprintf( esc_html__( 'Could not read file %s.', 'thememove-core' ), esc_html( $file_path ) ) );
			}

			// Delete the placeholder file & folder in wp-content directory.
			unlink( $file_path );

			require_once ABSPATH . 'wp-admin/includes/file.php';
			WP_Filesystem();
			global $wp_filesystem;

			$wp_filesystem->rmdir( $placeholder_path, true );
			exit;
		} else {
			// translators: $s: Archive file name.
			wp_die( sprintf( esc_html__( 'The file %s does not exists.', 'thememove-core' ), esc_html( $file_path ) ) );
		}
	}

	/**
	 * Copy files in wp-content/uploads to a new folder and skip all generated files & hidden files.
	 * Eg: image-DDDxDDD.ext or image-DDDxDDD@2x.ext or .DS_Store, etc...
	 *
	 * @param string $source Source folder.
	 * @param string $dest Destination folder.
	 *
	 * @return boolean Create zip package successful or not?
	 */
	protected function create_media_package( $source, $dest ) {

		if ( ! class_exists( 'ZipArchive' ) ) {
			wp_die( __( 'Could not create zip file. The extension \'ZipArchive\' is not enabled.', 'thememove-core' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		$source = str_replace( '\\', '/', realpath( $source ) );

		if ( is_dir( $source ) ) {

			if ( file_exists( $dest ) ) {
				unlink( $dest );
			}

			$zip = new ZipArchive();

			if ( ! $zip->open( $dest, ZIPARCHIVE::CREATE ) ) {
				return false;
			}

			$files = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $source ), RecursiveIteratorIterator::SELF_FIRST );

			foreach ( $files as $file ) {
				$file      = str_replace( '\\', '/', $file );
				$file_name = basename( $file );

				if ( '.' === $file_name || '..' === $file_name ) {
					continue;
				}

				if ( is_file( $file ) ) {
					$file_path     = realpath( $file );
					$relative_path = substr( $file_path, strlen( $source ) + 1 );
					$file_dir      = end( explode( '/', dirname( $file, 1 ) ) );

					// Only add files in year/month folders.
					if ( is_numeric( $file_dir ) ) {
						// Skip unnecessary files.
						if ( substr( $file_name, 0, 1 ) !== '.' && preg_match( '/(-\d{1,}x\d{1,}+|@2x)\.\w{3,}$/', $file_name ) === 0 ) {
							// Only add files that have allowed extensions.
							$ext = end( explode( '.', $file_name ) );
							if ( in_array( $ext, $this->get_allowed_exts(), true ) ) {
								$zip->addFile( $file_path, $relative_path );
							}
						}
					}
				}
			}

			return $zip->close();
		}

		return false;
	}

	/**
	 * Generate placeholder images.
	 *
	 * @param string $source Source folder.
	 * @param string $dest Destination folder.
	 *
	 * @return boolean Create placeholder directory successful or not?
	 */
	protected function generate_placeholder_images( $source, $dest ) {

		$source = str_replace( '\\', '/', realpath( $source ) );

		require_once ABSPATH . 'wp-admin/includes/file.php';
		WP_Filesystem();
		global $wp_filesystem;

		if ( is_dir( $source ) ) {

			if ( file_exists( $dest ) ) {
				$wp_filesystem->rmdir( $dest, true );
			}

			if ( ! wp_mkdir_p( $dest ) ) {
				return false;
			}

			$files  = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $source ), RecursiveIteratorIterator::SELF_FIRST );
			$result = false;

			foreach ( $files as $file ) {
				$file      = str_replace( '\\', '/', $file );
				$file_name = basename( $file );

				if ( '.' === $file_name || '..' === $file_name ) {
					continue;
				}
				$file_path     = realpath( $file );
				$relative_path = substr( $file_path, strlen( $source ) + 1 );
				$file_dir      = end( explode( '/', dirname( $file, 1 ) ) );

				if ( is_dir( $file ) ) {
					// Only crate year/month folders.
					if ( is_numeric( $file_dir ) ) {
						wp_mkdir_p( "{$dest}/{$relative_path}" );
					}
				} elseif ( is_file( $file ) ) {

					// Only add files in year/month folders.
					if ( is_numeric( $file_dir ) ) {
						// Skip unnecessary files.
						if ( substr( $file_name, 0, 1 ) !== '.' && preg_match( '/(-\d{1,}x\d{1,}+|@2x)\.\w{3,}$/', $file_name ) === 0 ) {
							// Only copy files with allowed extensions.
							$ext = end( explode( '.', $file_name ) );
							if ( in_array( $ext, $this->get_allowed_exts(), true ) ) {
								// Only generate placehoder for image files.
								if ( strpos( mime_content_type( $file ), 'image' ) !== false ) {
									$prefix = apply_filters( 'tmc_placeholder_prefix', '' );
									// Only process all image if we don't have a prefix.
									$process_all_images = empty( $prefix );

									// If you work on all images.
									if ( $process_all_images ) {
										$result = $this->download_image( $file, $relative_path, $dest );
									} else {
										// Or if you don't want to generate all images, you have to find all images that have $prefix.
										if ( substr( $file_name, 0, strlen( $prefix ) ) === $prefix ) {
											$result = $this->download_image( $file, $relative_path, $dest );
										}
									}
								} else { // Copy all other media files.
									$result = copy( $file_path, "{$dest}/{$relative_path}" );
								}
							}
						}
					}
				}
			}

			return $result;
		}
	}

	/**
	 * Download image from placeholder.com
	 *
	 * @param string $file File path.
	 * @param string $relative_path Relative path.
	 * @param string $dest Destination directory.
	 */
	protected function download_image( $file, $relative_path, $dest ) {
		$result      = false;
		$skip_prefix = apply_filters( 'tmc_placeholder_skip_prefix', '__' );

		$file_path     = realpath( $file );
		$file_name     = basename( $file );
		$ext           = end( explode( '.', $file_name ) );
		list( $w, $h ) = getimagesize( $file );

		if ( ! empty( $skip_prefix ) ) {
			// Skip all images that have $skip_prefix.
			if ( substr( $file_name, 0, strlen( $skip_prefix ) ) !== $skip_prefix ) {
				$result = copy( "https://via.placeholder.com/{$w}x{$h}.${ext}", "{$dest}/{$relative_path}" );
			}
		}

		return $result;
	}

	/**
	 * Export WooCommerce Settings (in Appearance > Customize > WooCommerce)
	 */
	public function export_woocommerce_settings() {

		if ( ! ThemeMove_Core::verify_nonce( 'export_woocommerce_settings' ) ) {
			wp_die( esc_html__( 'Invalid nonce', 'thememove-core' ) );
		}

		if ( ! class_exists( 'WooCommerce' ) ) {
			wp_die( __( 'Could not export WooCommerce settings. The plugin \'WooCommerce\' is not installed.', 'thememove-core' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			if ( version_compare( WC_VERSION, '3.3.0', '<' ) ) {
				// translators: %s: WooCommerce Version.
				wp_die( sprintf( esc_html__( 'Could not export WooCommerce settings. ThemeMove Core requires at least WooCommerce 3.3.0. You are using version %s', 'thememove-core' ), esc_html( WC_VERSION ) ) );
			}
		}

		$data = array();

		// Store Notice.
		$data['woocommerce_demo_store']        = get_option( 'woocommerce_demo_store', 'no' );
		$data['woocommerce_demo_store_notice'] = get_option( 'woocommerce_demo_store_notice', esc_html__( 'This is a demo store for testing purposes — no orders shall be fulfilled.', 'thememove-core' ) );

		// Product Catalog.
		$data['woocommerce_shop_page_display']        = get_option( 'woocommerce_shop_page_display', '' );
		$data['woocommerce_category_archive_display'] = get_option( 'woocommerce_category_archive_display', '' );
		$data['woocommerce_default_catalog_orderby']  = get_option( 'woocommerce_default_catalog_orderby', 'menu_order' );
		$data['woocommerce_catalog_columns']          = get_option( 'woocommerce_catalog_columns', 4 );
		$data['woocommerce_catalog_rows']             = get_option( 'woocommerce_catalog_rows', 4 );

		// Product Images.
		$data['woocommerce_single_image_width']               = get_option( 'woocommerce_single_image_width', 600 );
		$data['woocommerce_thumbnail_image_width']            = get_option( 'woocommerce_thumbnail_image_width', 300 );
		$data['woocommerce_thumbnail_cropping']               = get_option( 'woocommerce_thumbnail_cropping', '1:1' );
		$data['woocommerce_thumbnail_cropping_custom_width']  = get_option( 'woocommerce_thumbnail_cropping_custom_width', 4 );
		$data['woocommerce_thumbnail_cropping_custom_height'] = get_option( 'woocommerce_thumbnail_cropping_custom_height', 3 );

		// Check out.
		$data['woocommerce_checkout_company_field']                      = get_option( 'woocommerce_checkout_company_field', 'optional' );
		$data['woocommerce_checkout_address_2_field']                    = get_option( 'woocommerce_checkout_address_2_field', 'optional' );
		$data['woocommerce_checkout_phone_field']                        = get_option( 'woocommerce_checkout_phone_field', 'required' );
		$data['woocommerce_checkout_highlight_required_fields']          = get_option( 'woocommerce_checkout_highlight_required_fields', 'yes' );
		$data['wp_page_for_privacy_policy']                              = get_option( 'wp_page_for_privacy_policy', '' );
		$data['woocommerce_terms_page_id']                               = get_option( 'woocommerce_terms_page_id', '' );
		$data['woocommerce_checkout_privacy_policy_text']                = get_option( 'woocommerce_checkout_privacy_policy_text', esc_html__( 'Your personal data will be used to process your order, support your experience throughout this website, and for other purposes described in our [privacy_policy].', 'thememove-core' ) );
		$data['woocommerce_checkout_terms_and_conditions_checkbox_text'] = get_option( 'woocommerce_checkout_terms_and_conditions_checkbox_text', esc_html__( 'I have read and agree to the website [terms]', 'thememove-core' ) );

		// WooCommerce Pages.
		$woocommerce_shop_page_id      = intval( get_option( 'woocommerce_shop_page_id' ) );
		$woocommerce_cart_page_id      = intval( get_option( 'woocommerce_cart_page_id' ) );
		$woocommerce_checkout_page_id  = intval( get_option( 'woocommerce_checkout_page_id' ) );
		$woocommerce_myaccount_page_id = intval( get_option( 'woocommerce_myaccount_page_id' ) );

		if ( 0 !== $woocommerce_shop_page_id ) {
			$data['woocommerce_shop_page'] = get_the_title( $woocommerce_shop_page_id );
		}

		if ( 0 !== $woocommerce_cart_page_id ) {
			$data['woocommerce_cart_page'] = get_the_title( $woocommerce_cart_page_id );
		}

		if ( 0 !== $woocommerce_checkout_page_id ) {
			$data['woocommerce_checkout_page'] = get_the_title( $woocommerce_checkout_page_id );
		}

		if ( 0 !== $woocommerce_myaccount_page_id ) {
			$data['woocommerce_myaccount_page'] = get_the_title( $woocommerce_myaccount_page_id );
		}

		$data = apply_filters( 'tmc_export_woocommerce_settings_data', $data );
		$this->save_file( 'woocommerce-settings.json', wp_json_encode( $data ) );
	}

	/**
	 * Export Slider Revolution.
	 */
	public function export_slider_revolution() {

		if ( ! ThemeMove_Core::verify_nonce( 'export_slider_revolution' ) ) {
			wp_die( esc_html__( 'Invalid nonce', 'thememove-core' ) );
		}

		if ( ! class_exists( 'RevSliderAdmin' ) ) {
			wp_die( __( 'Could not export Slider Revolution settings. The plugin \'Slider Revolution\' is not installed.', 'thememove-core' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		global $wpdb;

		$tables = array(
			"{$wpdb->prefix}revslider_css",
			"{$wpdb->prefix}revslider_layer_animations",
			"{$wpdb->prefix}revslider_navigations",
			"{$wpdb->prefix}revslider_sliders",
			"{$wpdb->prefix}revslider_slides",
			"{$wpdb->prefix}revslider_static_slides",
		);

		require_once TMC_DIR . 'inc/libs/database-helper.php';

		tmc_export_database( DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, $tables, 'rev_sliders.txt' );
	}

	/**
	 * Export Elementor Settings.
	 */
	public function export_elementor_settings() {

		if ( ! ThemeMove_Core::verify_nonce( 'export_elementor_settings' ) ) {
			wp_die( esc_html__( 'Invalid nonce', 'thememove-core' ) );
		}

		if ( ! did_action( 'elementor/loaded' ) ) {
			wp_die( __( 'Could not export Elementor settings. The plugin \'Elementor\' is not installed.', 'thememove-core' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		$options = array(
			'elementor_container_width',
			'elementor_space_between_widgets',
			'elementor_stretched_section_container',
			'elementor_page_title_selector',
			'elementor_viewport_lg',
			'elementor_viewport_md',
			'elementor_global_image_lightbox',
			'elementor_cpt_support',
			'elementor_default_generic_fonts',
			'elementor_scheme_color',
			'elementor_scheme_typography',
			'elementor_scheme_color-picker',
			'elementor_pro_theme_builder_conditions',
		);

		$data = array();

		foreach ( $options as $option ) {
			$setting = get_option( $option );

			if ( false !== $setting ) {
				$data[ $option ] = $setting;
			}
		}

		$data = apply_filters( 'tmc_export_elementor_settings_data', $data );
		$this->save_file( 'elementor-settings.json', wp_json_encode( $data ) );
	}

	/**
	 * Export Elfsight Instagram Feed Widgets.
	 */
	public function export_elfsight_instagram_feed() {

		if ( ! ThemeMove_Core::verify_nonce( 'export_elfsight_instagram_feed' ) ) {
			wp_die( esc_html__( 'Invalid nonce', 'thememove-core' ) );
		}

		if ( ! class_exists( 'ElfsightInstagramFeedPlugin' ) ) {
			wp_die( __( 'Could not export Instagram Feed widgets. The plugin \'Elfsight Instagram Feed\' is not installed.', 'thememove-core' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		global $wpdb;

		$tables = array(
			"{$wpdb->prefix}elfsight_instagram_feed_widgets",
		);

		require_once TMC_DIR . 'inc/libs/database-helper.php';

		tmc_export_database( DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, $tables, 'instagram_feed_widgets.txt' );
	}
}
