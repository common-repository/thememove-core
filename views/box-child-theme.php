<?php
/**
 * License Box
 *
 * @package ThemeMove_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

$theme = wp_get_theme();
?>
<div class="tmc-box tmc-box--gray tmc-box--child-theme">
	<div class="tmc-box__body">

		<?php
		/**
		 * Hook: tmc_box_child_theme_before_content
		 */
		do_action( 'tmc_box_child_theme_before_content' );
		?>

		<?php if ( $theme->parent() ) : ?>
			<p class="tmc-warning-text">
				<?php
				echo sprintf(
					/* translators: %1$s URL to 'Themes' page, %2$: Theme Name */
					__( 'This tool does not work with child themes. Please <a href="%1$s">activate %2$s</a> to generate a new child theme.', 'thememove-core' ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					esc_url(
						wp_nonce_url(
							add_query_arg(
								array(
									'action'     => 'activate',
									'stylesheet' => TMC_THEME_SLUG,
								),
								admin_url( '/themes.php' )
							),
							'switch-theme_' . TMC_THEME_SLUG
						)
					),
					esc_html( TMC_THEME_NAME )
				); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				?>
			</p>
		<?php else : ?>
			<h4 class="tmc-box__title"><?php esc_html_e( 'Create a child theme from the current theme.', 'thememove-core' ); ?></h4>
			<p class="tmc-box__description">
				<?php
					/* translators: %1$s Theme Name, %2$s: Theme Version */
					echo sprintf( esc_html__( 'Currrent Theme: %1$s - %2$s', 'thememove-core' ), esc_html( TMC_THEME_NAME ), esc_html( TMC_THEME_VERSION ) )
				?>
			</p>
			<p class="tmc-error-text">&nbsp;</p>
			<form action="" method="POST" class="tmc-child-theme-generator-form">
				<input type="hidden" name="_wpnonce" value="<?php echo esc_attr( wp_create_nonce( 'generate_child_theme' ) ); ?>">
				<table class="form-table">
					<tr>
						<th><label for="theme-name"><?php esc_html_e( 'Theme Name', 'thememove-core' ); ?></label></th>
						<td><input type="text" id="theme-name" name="theme-name" class="regular-text" value="<?php echo esc_attr( TMC_THEME_NAME ) . esc_html__( ' Child', 'thememove-core' ); ?>" /></td>
					</tr>
					<tr>
						<th><label for="theme-desc"><?php esc_html_e( 'Theme Description', 'thememove-core' ); ?></label></th>
						<td>
							<textarea id="theme-desc" name="theme-desc" class="regular-text"><?php /* translators: %s Theme name. */ echo sprintf( esc_html__( 'A child theme of %s', 'thememove-core' ), esc_html( TMC_THEME_NAME ) ); ?></textarea>
						</td>
					</tr>
					<tr>
						<th><label for="theme-uri"><?php esc_html_e( 'Child Theme URL', 'thememove-core' ); ?></label></th>
						<td><input type="text" id="theme-uri" name="theme-uri" class="regular-text" value="<?php echo esc_url( site_url( '/' ) ); ?>" /></td>
					</tr>
					<tr>
						<th><label for="author"><?php esc_html_e( 'Author', 'thememove-core' ); ?></label></th>
						<td><input type="text" id="author" name="author" class="regular-text" value="<?php echo esc_attr( 'ThemeMove', 'thememove-core' ); ?>" /></td>
					</tr>
					<tr>
						<th><label for="author-uri"><?php esc_html_e( 'Author URL', 'thememove-core' ); ?></label></th>
						<td><input type="text" id="author-uri" name="author-uri" class="regular-text" value="<?php echo esc_url( 'https://thememove.com' ); ?>" /></td>
					</tr>
					<tr>
						<th><label for="version"><?php esc_html_e( 'Version', 'thememove-core' ); ?></label></th>
						<td><input type="text" id="version" name="version" class="regular-text" value="1.0" /></td>
					</tr>
					<tr>
						<th>&nbsp;</th>
						<td>
							<input type="checkbox" id="copy-theme-mods" name="copy-theme-mods" value="yes" checked />
							<label for="copy-theme-mods"><?php esc_html_e( 'Copy Menus, Widgets and other Customizer Settings from the parent theme.', 'thememove-core' ); ?></label>
						</td>
					</tr>
					<tr>
						<th>&nbsp;</th>
						<td><input type="submit" name="Generate child theme" class="button" value="<?php esc_html_e( 'Generate', 'thememove-core' ); ?>" /></td>
					</tr>
				</table>
			</form>
		<?php endif; ?>

		<?php
		/**
		 * Hook: tmc_box_child_theme_after_content
		 */
		do_action( 'tmc_box_child_theme_after_content' );
		?>
	</div>
</div>
