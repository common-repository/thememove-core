<?php
/**
 * Important Notes Box
 *
 * @package ThemeMove_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

add_thickbox();
?>
<div class="tmc-box tmc-box--orange tmc-box--import-notes">
	<div class="tmc-box__header">
		<span class="tmc-box__icon"><i class="fad fa-comment-exclamation"></i></span>
		<span><?php esc_html_e( 'Important Notes', 'thememove-core' ); ?></span>
	</div>
	<div class="tmc-box__body">

		<?php
		/**
		 * Hook: tmc_box_import_notes_before_content
		 */
		do_action( 'tmc_box_import_notes_before_content' );
		?>

		<ol>
			<li>
			<?php
			echo sprintf(
				/* translators: %s: WordPress Reset plugin URL */
				wp_kses_post( __( 'No existing posts, pages, categories, images, widgets or any other data will be deleted or modifed, but we recommend installing demo data on a clean WordPress website to prevent conflicts with your current content.<br/>To reset your website before importing, use <a href="%s" class="thickbox" title="Install WordPress Reset">WordPress Reset</a> plugin.', 'thememove-core' ) ),
				esc_url( admin_url( '/plugin-install.php?tab=plugin-information&plugin=wordpress-reset&TB_iframe=true&width=800&height=550' ) )
			);
			?>
			</li>
			<li><?php echo wp_kses_post( __( '<strong>All required plugins</strong> should be installed.', 'thememove-core' ) ); ?></li>
			<li>
			<?php
			echo sprintf(
				/* translators: %s: Regenerate Thumbnails plugin URL */
				wp_kses_post( __( 'After importing demo data, you should use <a href="%s" class="thickbox" title="Install Regenerate Thumbnails">Regenerate Thumbnails</a> plugin.', 'thememove-core' ) ),
				esc_url( admin_url( '/plugin-install.php?tab=plugin-information&plugin=regenerate-thumbnails&TB_iframe=true&width=800&height=550' ) )
			);
			?>
			</li>
			<li>
				<?php echo wp_kses_post( __( 'Posts, pages, images, widgets, menus and more data will get imported.<br/>Please click on the "Import" button only once and wait until the process is completed, it may take a while.', 'thememove-core' ) ); ?>
			</li>
		</ol>

		<?php
		/**
		 * Hook: tmc_box_import_notes_after_content
		 */
		do_action( 'tmc_box_import_notes_after_content' );
		?>

	</div>
</div>
