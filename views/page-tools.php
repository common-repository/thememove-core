<?php
/**
 * Tools page
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
		 * Action: tmc_page_tools_before_tabs
		 */
		do_action( 'tmc_page_tools_before_tabs' );
		?>

		<!-- Nav tab wrapper -->
		<?php echo wp_kses_post( $tabs_html ); ?>
		<!-- /Nav tab wrapper -->

		<?php
		/**
		 * Action: tmc_page_tools_after_tabs
		 */
		do_action( 'tmc_page_tools_after_tabs' );
		?>

		<?php
		/**
		 * Action: tmc_page_tools_before_content
		 */
		do_action( 'tmc_page_tools_before_content' );
		?>

		<?php if ( 'child_theme' === $current_tab ) : ?>
		<!-- Child Theme Generator -->
			<?php include_once TMC_DIR . 'views/box-child-theme.php'; ?>
		<!-- /Child Theme Generator -->
		<?php elseif ( 'system_info' === $current_tab ) : ?>
		<!-- System Info -->
			<?php include_once TMC_DIR . 'views/box-system-info.php'; ?>
		<!-- /System Info -->
		<?php elseif ( 'export' === $current_tab ) : ?>
		<!-- System Info -->
			<?php include_once TMC_DIR . 'views/box-export.php'; ?>
		<!-- /System Info -->
		<?php endif; ?>

		<?php
		/**
		 * Action: tmc_tools_after_main_content
		 */
		do_action( 'tmc_page_tools_after_content' );
		?>

	</div>
</div>
