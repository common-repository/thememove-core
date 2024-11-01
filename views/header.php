<?php
/**
 * ThemeMove Core Header
 *
 * @package ThemeMove_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

$theme_info = ThemeMove_Core::get_theme_info();

if ( ThemeMove_Core::is_theme_support() ) : ?>
<div class="tmc-header">

	<?php
	/**
	 * Action: tmc_before_header
	 */
	do_action( 'tmc_before_header' );
	?>

	<div class="tmc-header__thumbnail">
		<img src="<?php echo esc_url( $theme_info['thumbnail'] ); ?>" alt="<?php echo esc_attr( TMC_THEME_NAME ); ?>">
	</div>
	<div class="tmc-header__text">
		<?php // translators: %1$s: Theme Name, %2$s: Theme Version. ?>
		<h1 class="tmc-header__title"><?php echo sprintf( __( 'Welcome to %1$s <span>%2$s</span>', 'thememove-core' ), esc_html( TMC_THEME_NAME ), esc_html( TMC_THEME_VERSION ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h1>
		<p class="tmc-header__description">
			<?php echo esc_html( $theme_info['desc'] ); ?>
			<br/><a target="_blank" href="<?php echo esc_url( $theme_info['support_url'] ); ?>">Need support?</a> |
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=thememove-core-update' ) ); ?>">Check Update</a>
		</p>
	</div>

	<?php
	/**
	 * Action: tmc_after_header
	 */
	do_action( 'tmc_after_header' );
	?>

</div>
<?php else : ?>
<div class="tmc-body">
	<div class="tmc-box tmc-box--error">
		<div class="tmc-box__header">
			<span class="tmc-box__icon"><i class="fal fa-exclamation-circle"></i></span>
			<span class="tmc-box__title"><?php esc_html_e( 'Ooops!', 'thememove-core' ); ?></span>
		</div>
		<div class="tmc-box__body">
			<?php esc_html_e( 'Seems the current theme not compatible with ThemeMove Core.', 'thememove-core' ); ?>
		</div>
	</div>
</div>
	<?php
	exit;
endif;
