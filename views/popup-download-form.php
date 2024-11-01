<?php
/**
 * Download form
 *
 * @package ThemeMove_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}
?>
<i class="fal fa-spinner-third tm-spin tmc-loading__icon"></i>
<form action="" method="POST" id="download-media-package-form">
	<h4 class="tmc-popup__title animated fadeInRight"><?php esc_html_e( 'Download media package', 'thememove-core' ); ?></h4>
	<p class="tmc-error-text">&nbsp;</p>
	<div class="tmc-progress-bar animated fadeInRight">
		<span class="tmc-progress-bar__text"><?php esc_html_e( 'Initializing', 'thememove-core' ); ?></span>
		<div class="tmc-progress-bar__wrapper">
			<div class="tmc-progress-bar__inner">&nbsp;</div>
		</div>
	</div>
	<?php if ( isset( $selected_steps_str ) && ! empty( $selected_steps_str ) ) : ?>
		<input type="hidden" name="selected_steps" value="<?php echo esc_attr( $selected_steps_str ); ?>">
	<?php endif; ?>
	<input type="hidden" name="media_package_url" id="media_package_url" value="<?php echo esc_attr( $media_package_url ); ?>">
	<input type="hidden" name="demo_slug" id="demo_slug" value="<?php echo esc_attr( $demo_slug ); ?>">
	<input type="hidden" name="_wpnonce" id="_wpnonce" value="<?php echo esc_attr( wp_create_nonce( 'download_media_package' ) ); ?>">
	<div class="tmc-popup__footer animated fadeInRight">
		<i class="tmc-popup__note"><?php esc_html_e( 'Please do not close this window until the process is completed', 'thememove-core' ); ?></i>
		<a href="#" class="tmc-popup__close-button"><?php esc_html_e( 'Close', 'thememove-core' ); ?></a>
	</div>
</form>
