<?php
/**
 * ThemeMove License
 *
 * @package ThemeMove_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ThemeMove License Class
 */
final class ThemeMove_License {
	/**
	 * Instance
	 *
	 * @var ThemeMove_License The single instance of the class.
	 */
	private static $instance = null;

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @return ThemeMove_License An instance of the class.
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
		// Activate license.
		add_action( 'wp_ajax_activate_license', array( $this, 'activate_license' ) );
		add_action( 'wp_ajax_nopriv_activate_license', array( $this, 'activate_license' ) );

		// Deactivate license.
		add_action( 'wp_ajax_deactivate_license', array( $this, 'deactivate_license' ) );
		add_action( 'wp_ajax_nopriv_deactivate_license', array( $this, 'deactivate_license' ) );
	}

	/**
	 * Activate license
	 */
	public function activate_license() {
		$result = ThemeMove_Core::send_request( 'activate_license' );

		if ( is_array( $result ) && isset( $result['success'] ) && $result['success'] ) {
			delete_transient( 'tmc_' . TMC_THEME_SLUG . '_version' );
			delete_transient( 'tmc_' . TMC_THEME_SLUG . '_license_info' );
			delete_transient( 'tmc_' . TMC_THEME_SLUG . '_patches_' . TMC_THEME_VERSION );

			wp_send_json_success( $result );
		}

		if ( isset( $result['success'] ) && ! $result['success'] ) {
			$theme_info = ThemeMove_Core::get_theme_info();
			switch ( $result['error'] ) {
				case 'expired':
					$license_key = self::get_license_key();
					$renew_url   = "{$theme_info['api_url']}/checkout/?edd_license_key={$license_key}&download_id={$theme_info['download_id']}";
					$message     = sprintf(
						/* translators: %1$s: Expires date, %2$s: Renew URL */
						__( 'Your license key expired on %1$s. You should renew it <a href="%2$s" target="_blank">here</a>.', 'thememove-core' ),
						date_i18n( get_option( 'date_format' ), strtotime( $result['expires'], current_time( 'timestamp' ) ) ),
						esc_url( $renew_url )
					);
					break;
				case 'revoked':
					$message = sprintf(
						/* translators: %s: Support link */
						__( 'Your license key has been disabled. Please <a href="%s" target="_blank">contact us</a> to solve this issue.', 'thememove-core' ),
						$theme_info['support_url']
					);
					break;
				case 'item_name_mismatch':
					/* translators: %s: Theme name */
					$message = sprintf( esc_html__( 'This appears to be an invalid license key for %s.', 'thememove-core' ), TMC_THEME_NAME );
					break;
				case 'no_activations_left':
					$message = sprintf(
						/* translators: %s: Support link */
						__( 'Your license key has reached its activation limit. Manage your licensed sites <a href="%s" target="_blank">here</a>.', 'thememove-core' ),
						"{$theme_info['api_url']}/dashboard/purchases/?action=manage_licenses&payment_id={$result['payment_id']}"
					);
					break;
				case 'invalid':
				case 'site_inactive':
				default:
					$message = sprintf(
						/* translators: %s: Licenses page URL */
						__( 'Wrong license key. Please find your license key in <a href="%s" target="_blank">Licenses page</a> and try again.', 'thememove-core' ),
						"{$theme_info['api_url']}/dashboard/licenses/"
					);
					break;
			}

			$result['message'] = $message;
		}

		wp_send_json_error( $result );
	}

	/**
	 * Deactivate license
	 */
	public function deactivate_license() {

		$result = ThemeMove_Core::send_request( 'deactivate_license' );

		if ( is_array( $result ) && isset( $result['success'] ) && $result['success'] ) {
			delete_option( 'tmc_license_key' );
			delete_transient( 'tmc_' . TMC_THEME_SLUG . '_license_info' );

			wp_send_json_success( $result );
		}

		wp_send_json_error( $result );
	}

	/**
	 * Get license key
	 *
	 * @return string License key from database.
	 */
	public static function get_license_key() {
		return get_option( 'tmc_license_key', '' );
	}

	/**
	 * Get license information
	 *
	 * @return string License information from database.
	 */
	public static function get_license_info() {
		$license_key  = self::get_license_key();
		$license_info = get_transient( 'tmc_' . TMC_THEME_SLUG . '_license_info' );

		if ( false === $license_info ) {
			$license_info = ThemeMove_Core::send_request( 'check_license', $license_key, wp_create_nonce( 'check_license' ) );

			if ( is_array( $license_info ) ) {
				$expiration = apply_filters( 'tmc_transient_expiration', DAY_IN_SECONDS );
				set_transient( 'tmc_' . TMC_THEME_SLUG . '_license_info', $license_info, $expiration );
			}
		}

		return $license_info;
	}

	/**
	 * Get renew URL
	 */
	public static function get_renew_url() {
		$renew_url = get_option( 'tmc_renew_url' );

		if ( false === $renew_url ) {
			$license_key = self::get_license_key();
			$theme_info  = ThemeMove_Core::get_theme_info();

			if ( $license_key && $theme_info['download_id'] ) {
				$renew_url = "{$theme_info['api_url']}/checkout/?edd_license_key={$license_key}&download_id={$theme_info['download_id']}";
				update_option( 'tmc_renew_url', $renew_url );
			}
		}

		return $renew_url;
	}

	/**
	 * Get license status.
	 */
	public static function get_license_status() {
		$status = 'invalid';

		// Get license key in database.
		$license_key = self::get_license_key();

		// Check this license key valid or invalid.
		if ( $license_key ) {
			$license_info = self::get_license_info();

			if ( ! empty( $license_info ) ) {
				$success = isset( $license_info['success'] ) ? $license_info['success'] : false;
				$status  = isset( $license_info['license'] ) ? $license_info['license'] : 'invalid';

				if ( ! $success ) {
					$status = 'invalid';
				}
			}
		}

		return $status;
	}
}
