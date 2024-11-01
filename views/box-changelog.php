<?php
/**
 * Changelog Box
 *
 * @package ThemeMove_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

$changelog = ThemeMove_Updater::get_changelog( $version );

if ( ! empty( $changelog ) ) :
	?>
<div class="tmc-box tmc-box--gray tmc-box--changelog">
	<div class="tmc-box__header">
		<span class="tmc-box__icon"><i class="fal fa-clipboard-list-check"></i></span>
		<span><?php esc_html_e( 'Changelog', 'thememove-core' ); ?></span>
	</div>
	<div class="tmc-box__body">

		<?php
		/**
		 * Hook: tmc_box_changelog_before_content
		 */
		do_action( 'tmc_box_changelog_before_content' );
		?>

		<div class="tmc-box__changelog">
			<?php echo wp_kses_post( $changelog ); ?>
		</div>

		<?php
		/**
		 * Hook: tmc_box_changelog_after_content
		 */
		do_action( 'tmc_box_changelog_after_content' );
		?>

	</div>
</div>
<?php endif; ?>
