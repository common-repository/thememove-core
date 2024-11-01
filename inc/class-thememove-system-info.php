<?php
/**
 * ThemeMove System Info
 *
 * @package ThemeMove_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ThemeMove Child Theme Generator Class
 */
final class ThemeMove_System_Info {

	/**
	 * Instance
	 *
	 * @var ThemeMove_System_Info The single instance of the class.
	 */
	private static $instance = null;

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @return ThemeMove_System_Info An instance of the class.
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
		add_action( 'admin_post_download_sysinfo', array( $this, 'download_sysinfo' ) );
	}

	/**
	 * Get system information.
	 *
	 * @return string A string containing the info to output.
	 */
	public static function get_sysinfo() {
		$return = '### Begin System Info (Generated ' . date( 'Y-m-d H:i:s' ) . ') ###' . "\n\n";

		// Site Info.
		$return .= "---------- Site Info ----------\n\n";
		$return .= 'Site URL:                 ' . site_url() . "\n";
		$return .= 'Home URL:                 ' . home_url() . "\n";
		$return .= 'Multisite:                ' . ( is_multisite() ? 'Yes' : 'No' ) . "\n";

		$return = apply_filters( 'tmc_sysinfo_after_site_info', $return );

		// Site's host.
		$host = self::get_host();
		if ( $host ) {
			$return .= "\n---------- Hosting Provider ----------\n\n";
			$return .= "Host:                     {$host}\n";

			$return = apply_filters( 'tmc_sysinfo_after_host_info', $return );
		}

		// User's browser information.
		if ( ! class_exists( 'Browser' ) ) {
			require_once TMC_DIR . 'inc/libs/browser.php';
		}
		$browser = new Browser();
		$return .= "\n---------- User Browser ----------\n\n";
		$return .= $browser;

		$return = apply_filters( 'tmc_sysinfo_after_user_browser', $return );

		// WordPress Configuration.
		$theme_data   = wp_get_theme();
		$theme        = $theme_data['Name'] . ' - ' . $theme_data['Version'];
		$parent_theme = $theme_data->parent();

		$return .= "\n---------- WordPress Configuration ----------\n\n";
		$return .= 'Version:                  ' . get_bloginfo( 'version' ) . "\n";
		$return .= 'Language:                 ' . ( ! empty( $locale ) ? $locale : 'en_US' ) . "\n";
		$return .= 'Permalink Structure:      ' . ( get_option( 'permalink_structure' ) ? get_option( 'permalink_structure' ) : 'Default' ) . "\n";
		$return .= 'Active Theme:             ' . $theme . "\n";
		if ( ! empty( $parent_theme ) ) {
			$return .= 'Parent Theme:             ' . $parent_theme['Name'] . ' - ' . $parent_theme['Version'] . "\n";
		}
		$return .= 'Show On Front:            ' . get_option( 'show_on_front' ) . "\n";

		// Only show page specs if frontpage is set to 'page'.
		if ( 'page' === get_option( 'show_on_front' ) ) {
			$front_page_id = intval( get_option( 'page_on_front' ) );
			$blog_page_id  = intval( get_option( 'page_for_posts' ) );
			$return       .= 'Page On Front:            ' . ( 0 !== $front_page_id ? get_the_title( $front_page_id ) . ' (#' . $front_page_id . ')' : 'Unset' ) . "\n";
			$return       .= 'Page For Posts:           ' . ( 0 !== $blog_page_id ? get_the_title( $blog_page_id ) . ' (#' . $blog_page_id . ')' : 'Unset' ) . "\n";
		}
		$return .= 'ABSPATH:                  ' . ABSPATH . "\n";

		// Make sure wp_remote_post() is working.
		$request['cmd'] = '_notify-validate';

		$params = array(
			'sslverify'  => false,
			'timeout'    => 60,
			'user-agent' => 'TMC_VER/' . TMC_VER,
			'body'       => $request,
		);

		$response = wp_remote_post( 'https://www.paypal.com/cgi-bin/webscr', $params );

		if ( ! is_wp_error( $response ) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 ) {
			$wp_remote_post = '✅wp_remote_post() works';
		} else {
			$wp_remote_post = '❌wp_remote_post() does not work';
		}

		$return .= 'Remote Post:              ' . $wp_remote_post . "\n";
		$return .= 'WP_DEBUG:                 ' . ( defined( 'WP_DEBUG' ) ? WP_DEBUG ? '✅Enabled' : '❌Disabled' : 'Not set' ) . "\n";
		$return .= 'Memory Limit:             ' . WP_MEMORY_LIMIT . "\n";

		$return = apply_filters( 'tmc_sysinfo_after_wordpress_config', $return );

		// Must-use plugins.
		// NOTE: MU plugins can't show updates!
		$muplugins = get_mu_plugins();
		if ( count( $muplugins ) > 0 ) {
			$return .= "\n---------- Must-Use Plugins ----------\n\n";

			foreach ( $muplugins as $plugin => $plugin_data ) {
				$return .= $plugin_data['Name'] . "\n";
			}
		}

		$return = apply_filters( 'tmc_sysinfo_after_mu_plugins', $return );

		// WordPress active plugins.
		$updates = get_plugin_updates();
		$return .= "\n---------- WordPress Active Plugins ----------\n\n";

		$plugins        = get_plugins();
		$active_plugins = get_option( 'active_plugins', array() );

		foreach ( $plugins as $plugin_path => $plugin ) {
			if ( ! in_array( $plugin_path, $active_plugins, true ) ) {
				continue;
			}

			$update  = ( array_key_exists( $plugin_path, $updates ) ) ? ' (⏫needs update - ' . $updates[ $plugin_path ]->update->new_version . ' ⏫)' : '';
			$return .= $plugin['Name'] . ': ' . $plugin['Version'] . $update . "\n";
		}

		$return = apply_filters( 'tmc_sysinfo_after_wordpress_plugins', $return );

		// WordPress inactive plugins.
		$return .= "\n---------- WordPress Inactive Plugins ----------\n\n";

		foreach ( $plugins as $plugin_path => $plugin ) {
			if ( in_array( $plugin_path, $active_plugins, true ) ) {
				continue;
			}

			$update  = ( array_key_exists( $plugin_path, $updates ) ) ? ' (needs update - ' . $updates[ $plugin_path ]->update->new_version . ')' : '';
			$return .= $plugin['Name'] . ': ' . $plugin['Version'] . $update . "\n";
		}

		$return = apply_filters( 'tmc_sysinfo_after_wordpress_plugins_inactive', $return );

		if ( is_multisite() ) {
			// WordPress Multisite active plugins.
			$return        .= "\n---------- Network Active Plugins ----------\n\n";
			$plugins        = wp_get_active_network_plugins();
			$active_plugins = get_site_option( 'active_sitewide_plugins', array() );

			foreach ( $plugins as $plugin_path ) {
				$plugin_base = plugin_basename( $plugin_path );

				if ( ! array_key_exists( $plugin_base, $active_plugins ) ) {
					continue;
				}

				$update  = ( array_key_exists( $plugin_path, $updates ) ) ? ' (needs update - ' . $updates[ $plugin_path ]->update->new_version . ')' : '';
				$plugin  = get_plugin_data( $plugin_path );
				$return .= $plugin['Name'] . ': ' . $plugin['Version'] . $update . "\n";
			}

			$return = apply_filters( 'tmc_sysinfo_after_wordpress_ms_plugins', $return );
		}

		// Server configuration (really just versioning).
		global $wpdb;
		$return .= "\n---------- Webserver Configuration ----------\n\n";
		$return .= 'PHP Version:              ' . PHP_VERSION . "\n";
		$return .= 'MySQL Version:            ' . $wpdb->db_version() . "\n";

		if ( isset( $_SERVER['SERVER_SOFTWARE'] ) ) {
			$return .= 'Webserver Info:           ' . sanitize_text_field( wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) ) . "\n";
		}

		$return = apply_filters( 'tmc_sysinfo_after_webserver_config', $return );

		// PHP configs... now we're getting to the important stuff.
		$return .= "\n---------- PHP Configuration ----------\n\n";
		$return .= 'Memory Limit:             ' . ini_get( 'memory_limit' ) . "\n";
		$return .= 'Upload Max Size:          ' . ini_get( 'upload_max_filesize' ) . "\n";
		$return .= 'Post Max Size:            ' . ini_get( 'post_max_size' ) . "\n";
		$return .= 'Upload Max Filesize:      ' . ini_get( 'upload_max_filesize' ) . "\n";
		$return .= 'Time Limit:               ' . ( 300 <= ini_get( 'max_execution_time' ) ? ini_get( 'max_execution_time' ) : '❌' . ini_get( 'max_execution_time' ) . ' (We recommend setting max_execution_time to at least 300).' ) . "\n";
		$return .= 'Max Input Vars:           ' . ini_get( 'max_input_vars' ) . "\n";
		$return .= 'Display Errors:           ' . ( ini_get( 'display_errors' ) ? 'On (' . ini_get( 'display_errors' ) . ')' : 'N/A' ) . "\n";

		// PHP extensions and such.
		$return .= "\n---------- PHP Extensions ----------\n\n";
		$return .= 'cURL:                     ' . ( function_exists( 'curl_init' ) ? '✅Supported' : '❌Not Supported (Your server does not have cURL. Please contact your hosting provider to enable it)' ) . "\n";
		$return .= 'DOMDocument:              ' . ( class_exists( 'DOMDocument' ) ? '✅Supported' : '❌Not Supported (Your server does not have DOMDocument. Please contact your hosting provider to enable it)' ) . "\n";
		$return .= 'fsockopen:                ' . ( function_exists( 'fsockopen' ) ? '✅Supported' : '❌Not Supported (Your server does not have fsockopen. Please contact your hosting provider to enable it)' ) . "\n";
		$return .= 'XMLReader:                ' . ( class_exists( 'XMLReader' ) ? '✅Installed' : '❌Not Installed (Your server does not have XMLReader extension. Please contact your hosting provider to enable it)' ) . "\n";
		$return .= 'ZipArchive:               ' . ( class_exists( 'ZipArchive' ) ? '✅Installed' : '❌Not Installed (Your server does not have ZipArchive extension. Please contact your hosting provider to enable it)' ) . "\n";
		$return .= 'SOAP Client:              ' . ( class_exists( 'SoapClient' ) ? 'Installed' : 'Not Installed' ) . "\n";
		$return .= 'Suhosin:                  ' . ( extension_loaded( 'suhosin' ) ? 'Installed' : 'Not Installed' ) . "\n";

		$return = apply_filters( 'tmc_sysinfo_after_php_config', $return );

		$return .= "\n### End System Info ###";

		return $return;
	}

	/**
	 * Get host information
	 */
	private static function get_host() {

		$host = false;

		if ( defined( 'WPE_APIKEY' ) ) {
			$host = 'WP Engine';
		} elseif ( defined( 'PAGELYBIN' ) ) {
			$host = 'Pagely';
		} elseif ( DB_HOST === 'localhost:/tmp/mysql5.sock' ) {
			$host = 'ICDSoft';
		} elseif ( DB_HOST === 'mysqlv5' ) {
			$host = 'NetworkSolutions';
		} elseif ( strpos( DB_HOST, 'ipagemysql.com' ) !== false ) {
			$host = 'iPage';
		} elseif ( strpos( DB_HOST, 'ipowermysql.com' ) !== false ) {
			$host = 'IPower';
		} elseif ( strpos( DB_HOST, '.gridserver.com' ) !== false ) {
			$host = 'MediaTemple Grid';
		} elseif ( strpos( DB_HOST, '.pair.com' ) !== false ) {
			$host = 'pair Networks';
		} elseif ( strpos( DB_HOST, '.stabletransit.com' ) !== false ) {
			$host = 'Rackspace Cloud';
		} elseif ( strpos( DB_HOST, '.sysfix.eu' ) !== false ) {
			$host = 'SysFix.eu Power Hosting';
		} elseif ( isset( $_SERVER['SERVER_NAME'] ) && strpos( sanitize_text_field( wp_unslash( $_SERVER['SERVER_NAME'] ) ), 'Flywheel' ) !== false ) {
			$host = 'Flywheel';
		} else {

			// Adding a general fallback for data gathering.
			if ( isset( $_SERVER['SERVER_NAME'] ) ) {
				$server_name = sanitize_text_field( wp_unslash( $_SERVER['SERVER_NAME'] ) );
			}

			$host = 'DBH: ' . DB_HOST . ', SRV: ' . $server_name;
		}

		return $host;
	}

	/**
	 * Download system information file. Its name is tmc-system-info.txt
	 */
	public function download_sysinfo() {

		if ( ! ThemeMove_Core::verify_nonce( 'download_sysinfo' ) ) {
			wp_die( esc_html__( 'Invalid nonce', 'thememove-core' ) );
		}

		nocache_headers();
		header( 'Content-Type: text/plain' );
		header( 'Content-Disposition: attachment; filename="tmc-system-info.txt"' );

		if ( isset( $_POST['sysinfo'] ) && ! empty( $_POST['sysinfo'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			echo esc_html( wp_strip_all_tags( $_POST['sysinfo'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
		}

		die();
	}
}
