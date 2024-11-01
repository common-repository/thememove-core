<?php
/**
 * Issues Box
 *
 * @package ThemeMove_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

?>
<div class="tmc-box tmc-box--red tmc-box--import-issues">
	<div class="tmc-box__header">
		<span class="tmc-box__icon"><i class="fad fa-exclamation-triangle"></i></span>
		<span><?php esc_html_e( 'Issues Detected', 'thememove-core' ); ?></span>
	</div>
	<div class="tmc-box__body">

		<?php
		/**
		 * Hook: tmc_box_import_issues_before_content
		 */
		do_action( 'tmc_box_import_issues_before_content' );
		?>

		<ol>
			<?php foreach ( $import_issues as $issue ) : ?>
				<li><?php echo wp_kses_post( $issue ); ?></li>
			<?php endforeach; ?>
		</ol>

		<?php
		/**
		 * Hook: tmc_box_import_issues_after_content
		 */
		do_action( 'tmc_box_import_issues_after_content' );
		?>

	</div>
	<div class="tmc-box__footer">
		<span style="color: #dc433f">
			<?php esc_html_e( 'Please solve all issues listed above before importing demo data.', 'thememove-core' ); ?>
		</span>
	</div>
</div>
