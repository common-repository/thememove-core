<?php
/**
 * Export Box
 *
 * @package ThemeMove_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

$export_items = ThemeMove_Exporter::get_export_items();
?>
<div class="tmc-box tmc-box--gray tmc-box--export">
	<div class="tmc-box__body">

		<?php
		/**
		 * Action: tmc_box_export_before_content
		 */
		do_action( 'tmc_box_export_before_content' );
		?>

		<?php if ( ! empty( $export_items ) ) : ?>
			<?php foreach ( $export_items as $item ) : ?>
				<?php if ( isset( $item['name'], $item['action'], $item['icon'] ) ) : ?>
					<!-- Export <?php echo esc_html( $item['name'] ); ?>-->
					<div class="tmc-export-item tmc-export-item--<?php echo esc_attr( sanitize_title( $item['name'] ) ); ?>">
						<form action="<?php echo esc_url( admin_url( '/admin-post.php' ) ); ?>" method="POST" class="tmc-export-item__form">
								<?php if ( isset( $item['description'] ) ) : ?>
									<span class="tmc-export-item__help hint--right" aria-label="<?php echo esc_attr( $item['description'] ); ?>"><i class="fal fa-question-circle"></i></span>
								<?php endif; ?>

								<input type="hidden" name="_wpnonce" value="<?php echo esc_attr( wp_create_nonce( $item['action'] ) ); ?>">
								<input type="hidden" name="action" value="<?php echo esc_attr( $item['action'] ); ?>">
								<div class="tmc-export-item__icon<?php echo esc_attr( isset( $item['input_file_name'] ) && $item['input_file_name'] ? ' tmc-export-item__icon--has-file-name-input' : '' ); ?>">

								<?php if ( wp_http_validate_url( $item['icon'] ) ) : ?>
										<img src="<?php echo esc_attr( $item['icon'] ); ?>" alt="<?php echo esc_attr( $item['name'] ); ?>" />
									<?php else : ?>
										<i class="<?php echo esc_attr( $item['icon'] ); ?>"></i>
									<?php endif; ?>

									<?php if ( isset( $item['input_file_name'], $item['default_file_name'] ) && $item['input_file_name'] ) : ?>
										<input type="text"
											name="<?php echo esc_attr( sanitize_title( $item['name'] ) . '-file-name' ); ?>"
											id="<?php echo esc_attr( sanitize_title( $item['name'] ) . '-file-name' ); ?>"
											class="tmc-export-item__input"
											value="<?php echo esc_attr( $item['default_file_name'] ); ?>">
									<?php endif; ?>
								</div>
								<div class="tmc-export-item__footer">
									<p class="tmc-export-item__name"><?php echo esc_html( $item['name'] ); ?></p>
									<?php if ( isset( $item['export_page_url'] ) && ! empty( $item['export_page_url'] ) ) : ?>
										<a href="<?php echo esc_url( $item['export_page_url'] ); ?>" class="button tmc-export-item__button"><?php esc_html_e( 'Export', 'thememove-core' ); ?><i class="fad fa-arrow-circle-down"></i></a>
									<?php else : ?>
										<button type="submit" name="export" class="button tmc-export-item__button"><?php esc_html_e( 'Export', 'thememove-core' ); ?><i class="fad fa-arrow-circle-down"></i></button>
									<?php endif; ?>
								</div>
						</form>
					</div>
					<!-- /Export <?php echo esc_html( $item['name'] ); ?> -->
				<?php endif; ?>
			<?php endforeach; ?>
		<?php endif; ?>

		<?php
		/**
		 * Action: tmc_box_export_after_content
		 */
		do_action( 'tmc_box_export_after_content' );
		?>
	</div>
</div>
