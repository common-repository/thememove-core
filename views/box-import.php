<?php
/**
 * Import Demo Box
 *
 * @package ThemeMove_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

add_thickbox();
?>
<div class="tmc-box tmc-box--step-green tmc-box--import">
	<div class="tmc-box__header">
		<span class="tmc-box__number">2</span>
		<span><?php esc_html_e( 'Import Demo', 'thememove-core' ); ?></span>
	</div>
	<div class="tmc-box__body">

		<?php
		/**
		 * Hook: tmc_box_import_before_content
		 */
		do_action( 'tmc_box_import_before_content' );
		?>

		<p><?php esc_html_e( 'Our demo data import lets you have the whole data package in minutes, delivering all kinds of essential things quickly and simply. You may not have enough time for a coffee as the import is too fast!', 'thememove-core' ); ?></p>
		<i><?php esc_html_e( 'Notice: We recommend installing demo data on a clean WordPress website to prevent conflicts with your current content.', 'thememove-core' ); ?></i>
		<i>
		<?php
		echo sprintf(
			/* translators: %s: WordPress Reset plugin URL */
			wp_kses_post( __( '<br/>You can use the plugin <a href="%s" class="thickbox" title="Install WordPress Reset">WordPress Reset</a> to reset your website before importing if needed.', 'thememove-core' ) ),
			esc_url( admin_url( '/plugin-install.php?tab=plugin-information&plugin=wordpress-reset&TB_iframe=true&width=800&height=550' ) )
		);
		?>
		</i>

		<?php
		/**
		 * Hook: tmc_box_import_after_content
		 */
		do_action( 'tmc_box_import_after_content' );
		?>

	</div>
	<div class="tmc-box__footer">
		<a href="<?php echo esc_url( add_query_arg( 'page', 'thememove-core-import', admin_url( '/admin.php' ) ) ); ?>" class="button">
			<?php esc_html_e( 'Start Import Demo Data', 'thememove-core' ); ?>
			<i class="fas fa-download"></i>
		</a>
	</div>
</div>
