<?php
/**
 * Import Demos Box
 *
 * @package ThemeMove_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

$demos       = ThemeMove_Importer::get_import_demos();
$demos_count = count( $demos );
?>
<div class="tmc-box tmc-box--green tmc-box--import-demos">
	<div class="tmc-box__header">
		<span class="tmc-box__icon"><i class="fad fa-download"></i></span>
		<span>
			<?php
			if ( ! empty( $demos ) && 1 < $demos_count ) {
				esc_html_e( 'Select a demo to import', 'thememove-core' );
			} elseif ( 1 === $demos_count ) {
				$demo     = reset( $demos );
				$name     = isset( $demo['name'] ) ? $demo['name'] : esc_html__( 'Import Demo', 'thememove-core' );
				$imported = get_option( TMC_THEME_SLUG . '_' . key( $demos ) . '_imported', false );

				if ( ! $imported ) :
					echo esc_html( $name );
				else :
					echo esc_html( $name );
					?>
					<small><?php esc_html_e( '(has been imported before)', 'thememove-core' ); ?></small>
					<?php
				endif;
			}
			?>
		</span>
		<?php if ( 1 === $demos_count ) : ?>
			<a href="#" class="button tmc-import-demo__button" data-demo-slug="<?php echo esc_attr( key( $demos ) ); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'fetch_demo_steps' ) ); ?>"><?php esc_html_e( 'Import Demo Data', 'thememove-core' ); ?></a>
		<?php endif; ?>
	</div>
	<div class="tmc-box__body<?php echo esc_attr( 1 < $demos_count ) ? ' tmc-box__body--flex' : ''; ?>">

		<?php
		/**
		 * Hook: tmc_box_import_demos_before_content
		 */
		do_action( 'tmc_box_import_demos_before_content' );
		?>

		<p class="tmc-error-text"></p>

		<?php if ( ! empty( $demos ) ) : ?>
			<?php foreach ( $demos as $demo_slug => $demo ) : ?>
				<?php $imported = get_option( TMC_THEME_SLUG . '_' . $demo_slug . '_imported', false ); ?>
				<?php if ( isset( $demo['name'], $demo['preview_image_url'] ) ) : ?>
					<?php
					$css_class = "tmc-import-demo tmc-import-demo--{$demo_slug}";

					if ( 1 < $demos_count ) {
						$css_class .= ' tmc-import-demo--half';
					}

					?>
				<!-- Import <?php echo esc_html( $demo['name'] ); ?> -->
				<div class="<?php echo esc_attr( $css_class ); ?>">
					<div class="tmc-import-demo__inner">
						<div class="tmc-import-demo__preview">
							<img src="<?php echo esc_attr( $demo['preview_image_url'] ); ?>" alt="<?php echo esc_attr( $demo['name'] ); ?>" />
						</div>

						<?php if ( 1 < $demos_count ) : ?>
							<div class="tmc-import-demo__footer">
								<p class="tmc-import-demo__name">
									<?php if ( ! $imported ) : ?>
										<span><?php echo esc_html( $demo['name'] ); ?></span>
									<?php else : ?>
										<span>
											<?php echo esc_html( $demo['name'] ); ?>
											<small><?php esc_html_e( '(has been imported before)', 'thememove-core' ); ?></small>
										</span>
									<?php endif; ?>
									<?php if ( isset( $demo['description'] ) ) : ?>
										<span class="tmc-import-demo__help hint--right" aria-label="<?php echo esc_attr( $demo['description'] ); ?>"><i class="fad fa-question-circle"></i></span>
									<?php endif; ?>
								</p>
								<a href="#" class="button tmc-import-demo__button" data-demo-slug="<?php echo esc_attr( $demo_slug ); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'fetch_demo_steps' ) ); ?>">
									<?php esc_html_e( 'Import', 'thememove-core' ); ?>
								</a>
							</div>
						<?php endif; ?>
					</div>
				</div>
				<!-- /Import <?php echo esc_html( $demo['name'] ); ?> -->
				<?php endif; ?>
			<?php endforeach; ?>
		<?php endif; ?>

		<?php
		/**
		 * Hook: tmc_box_import_demos_after_content
		 */
		do_action( 'tmc_box_import_demos_after_content' );
		?>

	</div>

	<div id="tmc-import-demo-popup" class="tmc-popup mfp-hide">
	</div>
</div>
