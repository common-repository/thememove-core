<?php
/**
 * ThemeMove Updater
 *
 * @package ThemeMove_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ThemeMove Updater Class
 */
final class ThemeMove_Updater {

	/**
	 * Instance
	 *
	 * @var ThemeMove_Updater The single instance of the class.
	 */
	private static $instance = null;

	/**
	 * Dropbox folder name
	 *
	 * @var string The folder name after downloading & unzipping from Dropbox.
	 */
	private $remote_destination;

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @return ThemeMove_Updater An instance of the class.
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
		add_filter( 'pre_set_site_transient_update_themes', array( $this, 'check_for_update' ), 10, 1 );

		// Apply patch.
		add_action( 'wp_ajax_apply_patch', array( $this, 'apply_patch' ) );
		add_action( 'wp_ajax_nopriv_apply_patch', array( $this, 'apply_patch' ) );

		// Rename theme folder after upgrade.
		add_action( 'upgrader_clear_destination', array( $this, 'get_remote_destination' ), 10, 4 );
		add_action( 'upgrader_process_complete', array( $this, 'rename_theme_folder_after_upgrade' ), 8 );
	}

	/**
	 * Call API from thememove to get the latest version information
	 *
	 * @param object $transient Update theme transient.
	 *
	 * @return array|boolean  If an update is available, returns the update parameters, if no update is needed returns false, if
	 *                        the request fails returns false.
	 */
	public function check_for_update( $transient ) {

		if ( empty( $transient->checked ) ) {
			return $transient;
		}

		$update_data     = self::get_version();
		$has_new_version = self::has_new_version( $update_data['new_version'] );

		if ( is_array( $update_data ) && $has_new_version ) {
			// Set update data to update themes transient.
			$update_data['theme']                  = TMC_THEME_SLUG;
			$transient->response[ TMC_THEME_SLUG ] = $update_data;
		}

		return $transient;
	}

	/**
	 * Get folder name after download the package
	 *
	 * @param mixed  $removed            Whether the destination was cleared. true on success, WP_Error on failure.
	 * @param string $local_destination  The local package destination.
	 * @param string $remote_destination The remote package destination.
	 * @param array  $theme              Theme slug.
	 *
	 * @return string Folder name.
	 */
	public function get_remote_destination( $removed, $local_destination, $remote_destination, $theme ) {
		$this->remote_destination = $remote_destination;
		return $this->remote_destination;
	}

	/**
	 * Rename theme folder after upgrade
	 */
	public function rename_theme_folder_after_upgrade() {
		// Only rename in wp-content/themes folder.
		if ( get_theme_root() === dirname( $this->remote_destination ) && file_exists( $this->remote_destination ) ) {
			rename( $this->remote_destination, TMC_THEME_DIR );
		}
	}

	/**
	 * Check update
	 *
	 * @return  array Version from remote site.
	 */
	public static function get_version() {
		$license_key = ThemeMove_License::get_license_key();
		$version     = get_transient( 'tmc_' . TMC_THEME_SLUG . '_version' );

		if ( false === $version ) {
			$version = ThemeMove_Core::send_request( 'get_version', $license_key, wp_create_nonce( 'get_version' ) );

			if ( is_array( $version ) ) {
				$expiration = apply_filters( 'tmc_transient_expiration', DAY_IN_SECONDS );
				set_transient( 'tmc_' . TMC_THEME_SLUG . '_version', $version, $expiration );
			}
		}

		return $version;
	}

	/**
	 * Get latest version
	 *
	 * @param array $version Version information.
	 * @return string The latest version.
	 */
	public static function get_latest_version( $version = array() ) {

		if ( empty( $version ) ) {
			$version = self::get_version();
		}

		$latest_version = TMC_THEME_VERSION;

		if ( isset( $version['new_version'] ) ) {
			$latest_version = $version['new_version'];
		}

		return $latest_version;
	}

	/**
	 * Check current theme has new version or not?
	 *
	 * @param string $latest_version Latest version.
	 * @return boolean Has new version or not?
	 */
	public static function has_new_version( $latest_version = '' ) {
		if ( empty( $latest_version ) ) {
			$latest_version = self::get_latest_version();
		}
		return version_compare( $latest_version, TMC_THEME_VERSION, '>' );
	}

	/**
	 * Get changelog
	 *
	 * @param array $version Version array (get from remote site).
	 * @return string Changelog.
	 */
	public static function get_changelog( $version = array() ) {

		if ( empty( $version ) ) {
			$version = self::get_version();
		}

		$changelog = '';

		if ( isset( $version['sections'] ) ) {
			$sections = unserialize( $version['sections'] ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_unserialize

			if ( isset( $sections['changelog'] ) ) {
				$changelog = $sections['changelog'];
			}
		}

		return $changelog;
	}

	/**
	 * Get patches from the patches server
	 *
	 * @return string Patches string in JSON.
	 */
	public static function get_patches() {
		$patches = get_transient( 'tmc_' . TMC_THEME_SLUG . '_patches_' . TMC_THEME_VERSION );

		// Get data from remote url.
		if ( false === $patches ) {
			$args = array(
				'timeout' => 20,
			);

			$license     = ThemeMove_License::get_license_key();
			$item_name   = strtolower( rawurlencode( TMC_THEME_NAME ) );
			$version     = rawurlencode( TMC_THEME_VERSION );
			$url         = site_url( '/' );
			$theme_info  = ThemeMove_Core::get_theme_info();
			$request_url = "{$theme_info['patches_url']}/?action=get_patches&license={$license}&item_name={$item_name}&version={$version}&url={$url}";

			$request = wp_remote_post( $request_url, $args );
			$patches = json_decode( wp_remote_retrieve_body( $request ), true );

			$expiration = apply_filters( 'tmc_transient_expiration', DAY_IN_SECONDS );
			set_transient( 'tmc_' . TMC_THEME_SLUG . '_patches_' . TMC_THEME_VERSION, $patches, $expiration );
		}

		return $patches;
	}

	/**
	 * Get URL of the patch
	 *
	 * @param string $key Patch ID.
	 */
	public static function get_patch_url( $key ) {
			$license    = ThemeMove_License::get_license_key();
			$item_name  = strtolower( rawurlencode( TMC_THEME_NAME ) );
			$version    = rawurlencode( TMC_THEME_VERSION );
			$url        = site_url( '/' );
			$theme_info = ThemeMove_Core::get_theme_info();
			$patch_url  = "{$theme_info['patches_url']}/?action=download_patch&license={$license}&item_name={$item_name}&version={$version}&key={$key}&url={$url}";

			return $patch_url;
	}

	/**
	 * Count patches for the current version
	 *
	 * @return int
	 */
	public static function count_patches() {
		$count   = 0;
		$patches = self::get_patches();

		if ( ! empty( $patches ) ) {
			foreach ( $patches as $key => $patch ) {
				// If there is a patch has been not applied.
				if ( ! self::is_patch_applied( $key ) ) {
					$count++;
				}
			}
		}

		return $count;
	}

	/**
	 * Check if a patch has been applied
	 *
	 * @param string $key Patch key.
	 *
	 * @return boolean
	 */
	public static function is_patch_applied( $key ) {
		$applied_patches = get_option( 'tmc_' . TMC_THEME_SLUG . '_applied_patches' );
		return is_array( $applied_patches ) && in_array( $key, $applied_patches, true );
	}

	/**
	 * Apply a patch
	 */
	public function apply_patch() {

		if ( ! ThemeMove_Core::verify_nonce( 'apply_patch' ) ) {
			wp_send_json_error( esc_html__( 'Invalid nonce', 'thememove-core' ) );
		}

		if ( ! class_exists( 'ZipArchive' ) ) {
			wp_send_json_error( esc_html__( 'The extension ZipArchive is not enabled.', 'thememove-core' ) );
		}

		if ( isset( $_POST['key'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$key = sanitize_text_field( wp_unslash( $_POST['key'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		}

		if ( ! empty( $key ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
			WP_Filesystem();

			$item_name  = strtolower( rawurlencode( TMC_THEME_NAME ) );
			$theme_info = ThemeMove_Core::get_theme_info();
			$file_url   = "{$theme_info['patches_url']}/patches/{$item_name}/{$key}.zip";

			// Download patch.
			if ( is_writable( TMC_THEME_DIR ) ) {
				$package = download_url( $file_url, 1800 );

				if ( ! is_wp_error( $package ) ) {
					$unzip = unzip_file( $package, TMC_THEME_DIR );

					if ( ! is_wp_error( $unzip ) ) {

						// Delete temp file.
						@unlink( $package ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged

						// Mark this patch has been applied.
						$option_name     = 'tmc_' . TMC_THEME_SLUG . '_applied_patches';
						$applied_patches = get_option( $option_name );

						if ( false === $applied_patches ) {
							update_option( $option_name, array( $key ) );
						} else {
							if ( ! in_array( $key, $applied_patches, true ) ) {
								$applied_patches[] = $key;
								update_option( $option_name, $applied_patches );
							}
						}

						wp_send_json_success();
					} else {
						wp_send_json_error( $unzip->get_error_message() );
					}
				} else {
					wp_send_json_error( $package->get_error_message() );
				}
			}
		}
	}
}
