<?php
/**
 * Patcher Box
 *
 * @package ThemeMove_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

$patches = ThemeMove_Updater::get_patches();

?>
<div class="tmc-box tmc-box--gray tmc-box--patches">
	<div class="tmc-box__header">
		<span class="tmc-box__icon"><i class="fal fa-puzzle-piece"></i></span>
		<span><?php esc_html_e( 'Patches', 'thememove-core' ); ?></span>
		<a class="tmc-box--update__refresh" href="#" data-none="<?php echo esc_attr( wp_create_nonce( 'refresh_transients' ) ); ?>"><i class="fal fa-sync"></i> <?php esc_html_e( 'Refresh', 'thememove-core' ); ?></a>
	</div>
	<div class="tmc-box__body">

		<?php
		/**
		 * Hook: tmc_box_patches_before_content
		 */
		do_action( 'tmc_box_patches_before_content' );
		?>

		<?php if ( 'valid' === $license_status ) : ?>
				<?php if ( ! empty( $patches ) ) : ?>
					<p class="tmc-error-text">&nbsp;</p>
					<table class="wp-list-table widefat striped tmc-table">
						<thead>
							<tr>
								<th><?php esc_html_e( 'Patch #', 'thememove-core' ); ?></th>
								<th><?php esc_html_e( 'Description', 'thememove-core' ); ?></th>
								<th class="tmc-patch-status"><?php esc_html_e( 'Status', 'thememove-core' ); ?></th>
								<th class="tmc-patch-action">&nbsp;</th>
							</tr>
						</thead>
						<?php foreach ( $patches as $key => $patch ) : ?>
						<tr>
							<td>
								<span class="tmc-patch-key">#<?php echo esc_html( $key ); ?>&nbsp;&nbsp;<a href="<?php echo esc_url( ThemeMove_Updater::get_patch_url( $key ) ); ?>" target="_blank"><i class="fad fa-cloud-download"></i></a></span>
								<?php if ( isset( $patch['date'] ) ) : ?>
									<span class="tmc-patch-date"><?php echo esc_html( $patch['date'] ); ?></span>
								<?php endif; ?>
							</td>
							<td>
								<?php if ( isset( $patch['desc'] ) ) : ?>
									<span><?php echo esc_html( $patch['desc'] ); ?></span>
								<?php endif; ?>
							</td>
							<td class="tmc-patch-status">
								<?php if ( ThemeMove_Updater::is_patch_applied( $key ) ) : ?>
									<i class="far fa-check"></i>
								<?php endif; ?>
							</td>
							<td class="tmc-patch-action">
								<?php if ( ThemeMove_Updater::is_patch_applied( $key ) ) : ?>
									<a href="#" class="tmc-reapply tmc-apply-patch" data-key="<?php echo esc_attr( $key ); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'apply_patch' ) ); ?>">
										<i class="fal fa-undo"></i><?php esc_attr_e( 'Reapply', 'thememove-core' ); ?>
									</a>
								<?php else : ?>
									<a href="#" class="button tmc-apply-patch" data-key="<?php echo esc_attr( $key ); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'apply_patch' ) ); ?>">
										<?php esc_attr_e( 'Apply Patch', 'thememove-core' ); ?>
									</a>
								<?php endif; ?>
							</td>
						</tr>
						<?php endforeach; ?>
					</table>
				<?php else : ?>
					<p><?php esc_html_e( 'There is no patch for the current version.', 'thememove-core' ); ?></p>
				<?php endif; ?>
			<?php elseif ( 'expired' === $license_status ) : ?>
				<i class="tmc-warning-text">
				<?php
				if ( $renew_url ) {
					echo sprintf(
						/* translators: %s Link to checkout cart */
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

		<?php
		/**
		 * Hook: tmc_box_patches_after_content
		 */
		do_action( 'tmc_box_patches_after_content' );
		?>

	</div>
</div>
