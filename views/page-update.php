<?php
/**
 * Update page
 *
 * @package ThemeMove_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

$version         = ThemeMove_Updater::get_version();
$latest_version  = ThemeMove_Updater::get_latest_version( $version );
$has_new_version = ThemeMove_Updater::has_new_version( $latest_version );
$license_key     = ThemeMove_License::get_license_key();
$license_status  = ThemeMove_License::get_license_status();
$renew_url       = ThemeMove_License::get_renew_url();
?>
<div class="tmc-wrap">
	<?php require_once TMC_DIR . 'views/header.php'; ?>
	<div class="tmc-body">

		<?php
		/**
		 * Action: tmc_page_update_before_content
		 */
		do_action( 'tmc_page_update_before_content' );
		?>

		<?php if ( $has_new_version ) : ?>
			<!-- Update -->
			<?php include_once TMC_DIR . 'views/box-update-big.php'; ?>
			<!-- /Update -->
		<?php endif; ?>
			<!-- Patches -->
			<?php require_once TMC_DIR . 'views/box-patches.php'; ?>
			<!-- /Patches -->
		<!-- Changelog-->
		<?php require_once TMC_DIR . 'views/box-changelog.php'; ?>
		<!-- /Changelog -->

		<?php
		/**
		 * Action: tmc_page_update_after_content
		 */
		do_action( 'tmc_page_update_after_content' );
		?>

	</div>
</div>
