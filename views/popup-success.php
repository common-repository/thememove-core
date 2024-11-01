<?php
/**
 * Success content for popup after importing
 *
 * @package ThemeMove_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

$regenerate_thumbnails = apply_filters( 'tmc_regenerate_thumbnails', false );
?>
<div class="animated fadeInRight" id="import-success">
	<h4 class="tmc-popup__title"><?php esc_html_e( 'All done!', 'thememove-core' ); ?></h4>
	<p class="tmc-popup__subtitle"><?php esc_html_e( 'Import is successful! Now customization is as easy as pie. Enjoy it!', 'thememove-core' ); ?></p>
	<?php if ( ! $regenerate_thumbnails ) : ?>
		<p>
			<?php
				echo sprintf(
					/* translators: %s: Regenerate Thumbnails plugin URL */
					wp_kses_post( __( 'You should use <a href="%s" class="thickbox" title="Install Regenerate Thumbnails">Regenerate Thumbnails</a> plugin to regenerate all thumbnail sizes to make sure that everything works fine.', 'thememove-core' ) ),
					esc_url( admin_url( '/plugin-install.php?tab=plugin-information&plugin=regenerate-thumbnails&TB_iframe=true&width=800&height=550' ) )
				);
			?>
		</p>
	<?php endif; ?>
	<div class="tmc-popup__footer">
		<div class="tmc-popup__buttons">
			<a href="#" class="tmc-popup__close-button"><?php esc_html_e( 'Close', 'thememove-core' ); ?></a>
			<a href="<?php echo esc_url( site_url( '/' ) ); ?>" target="_blank" class="tmc-popup__next-button"><?php esc_html_e( 'View your website', 'thememove-core' ); ?></a>
		</div>
	</div>
</div>
