<?php
/**
 * Update Box
 *
 * @package ThemeMove_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

$version         = ThemeMove_Updater::get_version();
$latest_version  = ThemeMove_Updater::get_latest_version( $version );
$has_new_version = ThemeMove_Updater::has_new_version( $latest_version );
$license_status  = ThemeMove_License::get_license_status();
?>
<div class="tmc-box tmc-box--orange tmc-box--half tmc-box--update">
	<div class="tmc-box__header">
		<?php esc_html_e( 'Update', 'thememove-core' ); ?>
		<a href="#" id="go-to-changelog"><?php esc_html_e( 'Changelog', 'thememove-core' ); ?></a>
		<a class="tmc-box--update__refresh" href="#" data-none="<?php echo esc_attr( wp_create_nonce( 'refresh_transients' ) ); ?>"><i class="fal fa-sync"></i> <?php esc_html_e( 'Refresh', 'thememove-core' ); ?></a>
	</div>
	<div class="tmc-box__body">

		<?php
		/**
		 * Hook: tmc_box_update_before_content
		 */
		do_action( 'tmc_box_update_before_content' );
		?>

		<div class="tmc-update">
			<i class="fal fa-file-archive tmc-update__icon"></i>
			<div class="tmc-update__text">
				<span class="tmc-update__subtitle"><?php esc_html_e( 'Installed Version', 'thememove-core' ); ?></span>
				<span class="tmc-update__version"><?php echo esc_html( TMC_THEME_VERSION ); ?></span>
			</div>
		</div>
		<div class="tmc-update">
			<i class="fal fa-bell tmc-update__icon"></i>
			<div class="tmc-update__text">
				<span class="tmc-update__subtitle"><?php esc_html_e( 'Latest Available Version', 'thememove-core' ); ?></span>
				<span class="tmc-update__version">
					<?php echo esc_html( $latest_version ); ?>
				</span>
			</div>
		</div>
		<div class="tmc-update">
			<div class="tmc-update__text">
				<?php
				if ( $has_new_version ) {
					echo wp_kses_post( __( 'The latest version of this theme is available,<br/>update today!', 'thememove-core' ) );
				} else {
					esc_html_e( 'Your theme is up to date!', 'thememove-core' );
				}
				?>
			</div>
		</div>
		<div class="tmc-update">
			<div class="tmc-update__text">
				<?php if ( ! current_user_can( 'update_themes' ) ) : ?>
					<span><?php echo wp_kses_post( __( 'You do not have permission<br/> to update the theme.', 'thememove-core' ) ); ?></span>
					<?php
				else :
					if ( $has_new_version ) :
						if ( 'valid' === $license_status ) :
							?>
					<a class="button tmc-update-btn"
						href="<?php echo esc_url( wp_nonce_url( admin_url( 'update.php?action=upgrade-theme&theme=' . TMC_THEME_SLUG ), 'upgrade-theme_' . TMC_THEME_SLUG ) ); ?>"><?php esc_html_e( 'Update', 'thememove-core' ); ?><i class="fal fa-cloud-download"></i></a>
							<?php elseif ( 'expired' === $license_status ) : ?>
					<strong><?php echo wp_kses_post( __( 'Please renew your license key<br/> to update the theme.', 'thememove-core' ) ); ?></strong>
							<?php else : ?>
					<strong><?php echo wp_kses_post( __( 'Please enter your license key<br/> to update the theme.', 'thememove-core' ) ); ?></strong>
								<?php
						endif;
					endif;
				endif;
				?>
			</div>
		</div>

		<?php
		/**
		 * Hook: tmc_box_update_after_content
		 */
		do_action( 'tmc_box_update_after_content' );
		?>

	</div>
	<div class="tmc-box__bg-image">&nbsp;</div>
</div>
