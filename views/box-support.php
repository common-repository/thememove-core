<?php
/**
 * Support Box
 *
 * @package ThemeMove_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}
?>
<div class="tmc-box tmc-box--blue tmc-box--half tmc-box--support">
	<div class="tmc-box__header"><?php esc_html_e( 'Support', 'thememove-core' ); ?></div>
	<div class="tmc-box__body">

		<?php
		/**
		 * Hook: tmc_box_support_before_content
		 */
		do_action( 'tmc_box_support_before_content' );
		?>

		<table>
			<tr>
				<td><i class="fal fa-file-alt"></i></td>
				<td>
					<a href="<?php echo esc_url( $theme_info['docs_url'] ); ?>" target="_blank"><?php esc_html_e( 'Online Documentation', 'thememove-core' ); ?></a>
					<p><?php echo wp_kses_post( 'Detailed instruction to get<br/>the right way with our theme', 'thememove-core' ); ?></p>
				</td>
			</tr>
			<tr>
				<td><i class="fal fa-comment-alt-smile"></i></td>
				<td>
					<a href="<?php echo esc_url( $theme_info['faqs_url'] ); ?>" target="_blank"><?php esc_html_e( 'FAQs', 'thememove-core' ); ?></a>
					<p><?php esc_html_e( 'Check it before you ask for support.', 'thememove-core' ); ?></p>
				</td>
			</tr>
			<tr>
				<td><i class="fal fa-user-headset"></i></td>
				<td>
					<a href="<?php echo esc_url( $theme_info['support_url'] ); ?>" target="_blank"><?php esc_html_e( 'Human Support', 'thememove-core' ); ?></a>
					<p><?php esc_html_e( 'Our WordPress gurus\'d love to help you to shot issues one by one.', 'thememove-core' ); ?></p>
				</td>
			</tr>
		</table>
		<div>
			<a href="<?php echo esc_url( $theme_info['support_url'] ); ?>" target="_blank" class="button"><?php esc_html_e( 'Support Center', 'thememove-core' ); ?></a>
			<a href="<?php echo esc_url( $theme_info['faqs_url'] ); ?>" target="_blank" class="button"><?php esc_html_e( 'FAQs', 'thememove-core' ); ?></a>
		</div>

		<?php
		/**
		 * Hook: tmc_box_support_after_content
		 */
		do_action( 'tmc_box_support_after_content' );
		?>
		<div class="tmc-box__bg-image">&nbsp;</div>
	</div>

</div>
