<?php
/**
 * Plugin Name: ThemeMove Core
 * Description: A simple plugin for ThemeMove's themes on WordPress.org and ThemeMove Club.
 * Author:      ThemeMove
 * Author URI:  https://thememove.com
 * Version:     1.3.0
 * License:     GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: thememove-core
 * Domain Path: /languages
 *
 * ThemeMove Core is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * ThemeMove Core is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with ThemeMove Core. If not, see {License URI}.
 *
 * @package ThemeMove_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

$theme = wp_get_theme();
// If current theme is a child theme, get its parent theme.
if ( ! empty( $theme['Template'] ) ) {
	$theme = wp_get_theme( $theme['Template'] );
}

define( 'TMC_VER', '1.3.0' );
define( 'TMC_SITE_URI', site_url() );
define( 'TMC_DIR', plugin_dir_path( __FILE__ ) );
define( 'TMC_URL', plugin_dir_url( __FILE__ ) );
define( 'TMC_THEME_NAME', $theme['Name'] );
define( 'TMC_THEME_SLUG', $theme['Template'] );
define( 'TMC_THEME_VERSION', $theme['Version'] );
define( 'TMC_THEME_DIR', get_template_directory() );
define( 'TMC_THEME_URI', get_template_directory_uri() );

if ( version_compare( phpversion(), '5.6', '<' ) ) {
	wp_die( esc_html__( 'ThemeMove Core requires PHP version 5.6 or greater.', 'thememove-core' ) );
}

require_once TMC_DIR . 'inc/class-thememove-core.php';
ThemeMove_Core::instance();
