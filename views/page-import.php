<?php
/**
 * Import page
 *
 * @package ThemeMove_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

$import_issues        = ThemeMove_Importer::get_import_issues();
$ignore_import_issues = apply_filters( 'tmc_ignore_import_issues', false );
?>
<div class="tmc-wrap">
	<?php require_once TMC_DIR . 'views/header.php'; ?>
	<div class="tmc-body">

		<?php
		/**
		 * Action: tmc_page_import_before_content
		 */
		do_action( 'tmc_page_import_before_content' );
		?>

		<!-- Important Notes -->
		<?php require_once TMC_DIR . 'views/box-import-notes.php'; ?>
		<!-- /Important Notes -->

		<?php if ( ! empty( $import_issues ) && ! $ignore_import_issues ) : ?>
			<!-- Issues -->
			<?php require_once TMC_DIR . 'views/box-import-issues.php'; ?>
			<!-- /Issues -->
		<?php else : ?>
			<!-- Import Demos -->
			<?php require_once TMC_DIR . 'views/box-import-demos.php'; ?>
			<!-- /Import Demos -->
		<?php endif; ?>

		<?php
		/**
		 * Action: tmc_page_import_after_content
		 */
		do_action( 'tmc_page_import_after_content' );
		?>
	</div>
</div>
