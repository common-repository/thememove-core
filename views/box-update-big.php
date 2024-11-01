<?php
/**
 * Update Box (Big)
 *
 * @package ThemeMove_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

?>
<div class="tmc-box tmc-box--gray tmc-box--update-big">
	<div class="tmc-box__header">
		<span class="tmc-box__icon"><i class="fal fa-sync"></i></span>
		<span><?php esc_html_e( 'Update Available', 'thememove-core' ); ?></span>
		<a class="tmc-box--update__refresh" href="#" data-none="<?php echo esc_attr( wp_create_nonce( 'refresh_transients' ) ); ?>"><i class="fal fa-sync"></i> <?php esc_html_e( 'Refresh', 'thememove-core' ); ?></a>
	</div>
	<div class="tmc-box__body">

		<?php
		/**
		 * Hook: tmc_box_update_big_before_content
		 */
		do_action( 'tmc_box_update_big_before_content' );
		?>

		<?php
		/* translators: %s Theme name */
		echo sprintf( __( 'There is a new version of %s available.', 'thememove-core' ), esc_html( TMC_THEME_NAME ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		?>
		<div class="tmc-update__actions">
			<?php if ( 'valid' === $license_status ) : ?>
				<a class="button"
							href="<?php echo esc_url( wp_nonce_url( admin_url( 'update.php?action=upgrade-theme&theme=' . TMC_THEME_SLUG ), 'upgrade-theme_' . TMC_THEME_SLUG ) ); ?>"><?php esc_html_e( 'Update', 'thememove-core' ); ?><i class="fal fa-cloud-download"></i></a>
			<?php elseif ( 'expired' === $license_status ) : ?>
				<i class="tmc-warning-text">
				<?php
				if ( $renew_url ) {
					echo sprintf(
						/* translators: %s Link checkout cart */
						wp_kses_post( __( 'Please <a href="%s" target="_blank">renew your license key</a> to update the theme.', 'thememove-core' ) ),
						esc_url( $renew_url )
					);
				} else {
					echo sprintf(
						/* translators: %s Link to welcome page */
						esc_html__( 'Please renew your license key to update the theme.', 'thememove-core' ),
						esc_url( add_query_arg( 'page', 'thememove-core', admin_url( '/' ) ) )
					);
				}
				?>
				</i>
			<?php else : ?>
				<i class="tmc-warning-text">
				<?php
				echo sprintf(
					/* translators: %s Link to welcome page */
					wp_kses_post( __( 'Please <a href="%s">enter your license key</a> to update the theme.', 'thememove-core' ) ),
					esc_url( add_query_arg( 'page', 'thememove-core', admin_url( '/' ) ) )
				);
				?>
				</i>
			<?php endif; ?>
		</div>

		<?php
		/**
		 * Hook: tmc_box_update_big_after_content
		 */
		do_action( 'tmc_box_update_big_after_content' );
		?>

	</div>
</div>
