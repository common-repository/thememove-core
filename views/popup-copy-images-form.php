<?php
/**
 * Copy images form
 *
 * @package ThemeMove_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}
?>
<i class="fal fa-spinner-third tm-spin tmc-loading__icon"></i>
<form action="" method="POST" id="copy-images-form">
	<h4 class="tmc-popup__title animated fadeInRight"><?php esc_html_e( 'Copy images', 'thememove-core' ); ?></h4>
	<p class="tmc-error-text">&nbsp;</p>
	<?php if ( isset( $media_package_local ) ) : ?>
		<input type="hidden" name="media_package_local" value="<?php echo esc_attr( $media_package_local ); ?>">
	<?php endif; ?>
	<?php if ( isset( $selected_steps_str ) && ! empty( $selected_steps_str ) ) : ?>
		<input type="hidden" name="selected_steps" value="<?php echo esc_attr( $selected_steps_str ); ?>">
	<?php endif; ?>
	<input type="hidden" name="demo_slug" id="demo_slug" value="<?php echo esc_attr( $demo_slug ); ?>">
	<input type="hidden" name="_wpnonce" id="_wpnonce" value="<?php echo esc_attr( wp_create_nonce( 'copy_images' ) ); ?>">
	<div class="tmc-popup__footer animated fadeInRight">
		<i class="tmc-popup__note"><?php esc_html_e( 'Please do not close this window until the process is completed', 'thememove-core' ); ?></i>
		<a href="#" class="tmc-popup__close-button"><?php esc_html_e( 'Close', 'thememove-core' ); ?></a>
	</div>
</form>
