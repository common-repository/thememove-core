<?php
/**
 * Add custom control for CMB2.
 *
 * @package ThemeMove_Core
 */

add_action( 'admin_enqueue_scripts', 'tmc_requires_cmb2_css' );
function tmc_requires_cmb2_css() {
	wp_enqueue_style( 'cmb2-custom-types', plugins_url( 'css/cmb2-custom-types.css', __FILE__ ), array( 'cmb2-styles' ), TMC_VER );
}

require_once trailingslashit( dirname( __FILE__ ) ) . 'class-cmb2-type-buttonset.php';
require_once trailingslashit( dirname( __FILE__ ) ) . 'class-cmb2-type-number.php';
require_once trailingslashit( dirname( __FILE__ ) ) . 'class-cmb2-type-post-search.php';
require_once trailingslashit( dirname( __FILE__ ) ) . 'class-cmb2-type-radio-image.php';
require_once trailingslashit( dirname( __FILE__ ) ) . 'class-cmb2-type-rgba-picker.php';
require_once trailingslashit( dirname( __FILE__ ) ) . 'class-cmb2-type-select2.php';
require_once trailingslashit( dirname( __FILE__ ) ) . 'class-cmb2-type-slider.php';
require_once trailingslashit( dirname( __FILE__ ) ) . 'class-cmb2-type-switch.php';
require_once trailingslashit( dirname( __FILE__ ) ) . 'class-cmb2-type-tabs.php';
