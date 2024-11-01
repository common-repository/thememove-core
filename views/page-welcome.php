<?php
/**
 * Welcome page
 *
 * @package ThemeMove_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}
?>
<div class="tmc-wrap">
	<?php require_once TMC_DIR . 'views/header.php'; ?>
	<div class="tmc-body">

		<?php
		/**
		 * Action: tmc_page_welcome_before_content
		 */
		do_action( 'tmc_page_welcome_before_content' );
		?>

		<!-- License Key -->
		<?php require_once TMC_DIR . 'views/box-license.php'; ?>
		<!-- /License Key -->
		<!-- Row -->
		<div class="tmc-row">
			<!-- Update -->
			<?php require_once TMC_DIR . 'views/box-update.php'; ?>
			<!-- /Update -->
			<!-- Support -->
			<?php require_once TMC_DIR . 'views/box-support.php'; ?>
			<!-- /Support -->
		</div>
		<!-- /Row-->
		<!-- Plugins -->
		<?php require_once TMC_DIR . 'views/box-plugins.php'; ?>
		<!-- /Plugins -->
		<!-- Import Demo -->
		<?php require_once TMC_DIR . 'views/box-import.php'; ?>
		<!-- /Import Demo -->
		<!-- Changelog-->
		<?php require_once TMC_DIR . 'views/box-changelog.php'; ?>
		<!-- /Changelog -->
	</div>

	<?php
	/**
	 * Action: tmc_page_welcome_after_content
	 */
	do_action( 'tmc_page_welcome_after_content' );
	?>

</div>
