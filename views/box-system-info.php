<?php
/**
 * System Info Box
 *
 * @package ThemeMove_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

?>
<div class="tmc-box tmc-box--gray tmc-box--system-info">
	<div class="tmc-box__body">

		<?php
		/**
		 * Hook: tmc_box_system_info_before_content
		 */
		do_action( 'tmc_box_system_info_before_content' );
		?>

		<form action="<?php echo esc_url( admin_url( '/admin-post.php' ) ); ?>" method="POST">
			<input type="hidden" name="_wpnonce" value="<?php echo esc_attr( wp_create_nonce( 'download_sysinfo' ) ); ?>">
			<input type="hidden" name="action" value="download_sysinfo">
			<p>
				<textarea name="sysinfo" id="sysinfo" class="large-text" rows="20" readonly="readonly" onclick="this.focus(); this.select()"><?php echo esc_html( ThemeMove_System_Info::get_sysinfo() ); ?></textarea>
			</p>
			<input type="submit" class="button" value="Download System Info File" />
		</form>

		<?php
		/**
		 * Hook: tmc_box_system_info_after_content
		 */
		do_action( 'tmc_box_system_info_after_content' );
		?>

	</div>
</div>
