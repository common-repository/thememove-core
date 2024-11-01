<?php
/**
 * Show demo files need to import
 *
 * @package ThemeMove_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}
?>
<i class="fal fa-spinner-third tm-spin tmc-loading__icon"></i>
<form action="" method="POST" id="demo-steps-form">
	<h4 class="tmc-popup__title animated fadeInRight"><?php esc_html_e( 'Choose what to import', 'thememove-core' ); ?></h4>
	<p class="tmc-error-text">&nbsp;</p>
	<ul class="tmc-demo-steps">
		<li class="tmc-demo-steps__item animated fadeInRight">
			<input type="checkbox" name="all_demo_steps" id="tmc-all-demo-steps" class="tmc-demo-steps__checkbox" checked="true">
			<span class="tmc-demo-steps__svg">
				<svg width="18px" height="18px" viewBox="0 0 18 18">
						<path d="M1,9 L1,3.5 C1,2 2,1 3.5,1 L14.5,1 C16,1 17,2 17,3.5 L17,14.5 C17,16 16,17 14.5,17 L3.5,17 C2,17 1,16 1,14.5 L1,9 Z"></path>
						<polyline points="1 9 7 14 15 4"></polyline>
					</svg>
			</span>
			<label for="tmc-all-demo-steps" class="tmc-demo-steps__label"><?php esc_html_e( 'All', 'thememove-core' ); ?></label>
		</li>
		<?php foreach ( $demo_steps as $key => $val ) : ?>
		<li class="tmc-demo-steps__item animated fadeInRight">
			<input type="checkbox" name="<?php echo esc_attr( $key ); ?>" id="<?php echo esc_attr( $key ); ?>" class="tmc-demo-steps__checkbox" checked="true">
			<span class="tmc-demo-steps__svg">
				<svg width="18px" height="18px" viewBox="0 0 18 18">
						<path d="M1,9 L1,3.5 C1,2 2,1 3.5,1 L14.5,1 C16,1 17,2 17,3.5 L17,14.5 C17,16 16,17 14.5,17 L3.5,17 C2,17 1,16 1,14.5 L1,9 Z"></path>
						<polyline points="1 9 7 14 15 4"></polyline>
					</svg>
			</span>
			<label for="<?php echo esc_attr( $key ); ?>" class="tmc-demo-steps__label"><?php echo esc_html( $val ); ?></label>
		</li>
		<?php endforeach; ?>
	</ul>
	<input type="hidden" name="demo_slug" id="demo_slug" value="<?php echo esc_attr( $demo_slug ); ?>">
	<input type="hidden" name="selected_steps" id="selected-steps" value="">
	<input type="hidden" name="_wpnonce" id="_wpnonce" value="<?php echo esc_attr( wp_create_nonce( 'import_demo' ) ); ?>">
	<div class="tmc-popup__footer animated fadeInRight">
		<div class="tmc-popup__buttons">
			<a href="#" class="tmc-popup__close-button"><?php esc_html_e( 'Cancel', 'thememove-core' ); ?></a>
			<button type="submit" class="tmc-popup__next-button"><?php esc_html_e( 'Continue', 'thememove-core' ); ?><i class="far fa-long-arrow-alt-right" /></button>
		</div>
	</div>
</form>
