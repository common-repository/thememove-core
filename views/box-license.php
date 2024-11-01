<?php
/**
 * License Box
 *
 * @package ThemeMove_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

$license_key   = ThemeMove_License::get_license_key();
$license_info  = ThemeMove_License::get_license_info();
$renew_url     = ThemeMove_License::get_renew_url();
$box_css_class = 'tmc-box--green';
$box_title     = esc_html__( 'License Key', 'thememove-core' );
$td_css_class  = '';

if ( ! empty( $license_key ) && isset( $license_info['license'] ) && 'expired' === $license_info['license'] ) {
	$box_css_class = 'tmc-box--red';
	$box_title     = esc_html__( 'License Key - Expired', 'thememove-core' );
	$td_css_class  = 'error';
}
?>
<div class="tmc-box tmc-box--license <?php echo esc_attr( $box_css_class ); ?>">
	<div class="tmc-box__header"><?php echo esc_html( $box_title ); ?></div>
	<div class="tmc-box__body">

		<?php
		/**
		 * Hook: tmc_box_license_before_content
		 */
		do_action( 'tmc_box_license_before_content' );
		?>

		<?php if ( ! empty( $license_key ) && isset( $license_info['license'] ) && 'invalid' !== $license_info['license'] ) : ?>
			<p class="tmc-error-text">&nbsp;</p>
			<table class="wp-list-table widefat striped tmc-table">
				<tr>
					<td class="row-title"><?php esc_html_e( 'License Key:', 'thememove-core' ); ?></td>
					<td>
						<input type="password" class="tmc-table__license-key" value="<?php echo esc_attr( $license_key ); ?>" disabled />
						<a href="#" class="tmc-open-key"><i class="fal fa-lock-alt"></i></a>
					</td>
				</tr>
				<tr>
					<td class="row-title"><?php esc_html_e( 'Product:', 'thememove-core' ); ?></td>
					<td><strong><?php echo isset( $license_info['item_name'] ) ? esc_html( $license_info['item_name'] ) : ''; ?></strong></td>
				</tr>
				<tr>
					<td class="row-title <?php echo esc_attr( $td_css_class ); ?>"><?php esc_html_e( 'Expiration Date:', 'thememove-core' ); ?></td>
					<td class="<?php echo esc_attr( $td_css_class ); ?>">
					<?php if ( isset( $license_info['expires'] ) && 'lifetime' === $license_info['expires'] ) : ?>
						<?php esc_html_e( 'Lifetime', 'thememove-core' ); ?>
					<?php elseif ( ! empty( $license_info['expires'] ) ) : ?>
						<?php echo esc_html( date( 'M d, Y', strtotime( $license_info['expires'] ) ) ); ?>
					<?php endif; ?>
					</td>
				</tr>
				<tr>
					<td class="row-title"><?php esc_html_e( 'Activations:', 'thememove-core' ); ?></td>
					<td><?php echo isset( $license_info['site_count'], $license_info['license_limit'] ) ? esc_html( $license_info['site_count'] . ' / ' . ( 0 === $license_info['license_limit'] ? esc_html__( 'Unlimited', 'thememove-core' ) : $license_info['license_limit'] ) ) : ''; ?></td>
				</tr>
				<tr>
					<td class="row-title"><?php esc_html_e( 'Customer Name:', 'thememove-core' ); ?></td>
					<td><?php echo isset( $license_info['customer_name'] ) ? esc_html( $license_info['customer_name'] ) : ''; ?></td>
				</tr>
				<tr>
					<td class="row-title"><?php esc_html_e( 'Customer Email', 'thememove-core' ); ?></td>
					<td><?php echo isset( $license_info['customer_email'] ) ? esc_html( $license_info['customer_email'] ) : ''; ?></td>
				</tr>
				<tr>
					<td class="row-title"><?php esc_html_e( 'Actions:', 'thememove-core' ); ?></td>
					<td>
						<?php
						if ( isset( $license_info['payment_id'] ) ) :
							$manage_url = "{$theme_info['api_url']}/dashboard/purchases/?action=manage_licenses&payment_id={$license_info['payment_id']}";
							?>
							<a href="<?php echo esc_url( $manage_url ); ?>" target="_blank"><?php esc_html_e( 'Manage License on ThemeMove', 'thememove-core' ); ?></a>
						<?php endif; ?>|
						<?php if ( ! empty( $renew_url ) ) : ?>
						<a href="<?php echo esc_url( $renew_url ); ?>" target="_blank"><?php esc_html_e( 'Renew', 'thememove-core' ); ?></a> |
						<?php endif; ?>
						<a class="tmc-deactivate-link" href="#" data-license="<?php echo esc_attr( $license_key ); ?>" data-url="<?php echo esc_attr( site_url( '/' ) ); ?>" data-item-name="<?php echo esc_attr( TMC_THEME_NAME ); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'deactivate_license' ) ); ?>"><?php esc_html_e( 'Deactive', 'thememove-core' ); ?></a>
					</td>
				</tr>
			</table>
		<?php else : ?>
			<form action="" method="POST" class="tmc-license-key-form">
				<p class="tmc-error-text">
					<?php // translators: %s: Licenses page URL. ?>
					<?php echo sprintf( __( 'Wrong license key. Please find your license key in <a href="%s" target="_blank">Licenses page</a> and try again.', 'thememove-core' ), "{$theme_info['api_url']}/dashboard/licenses/" ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</p>
				<i class="fal fa-key-skeleton tmc-license-key-form__icon"></i>
				<input type="text" name="license" class="tmc-license-key-form__input" placeholder="<?php esc_html_e( 'Enter your license key', 'thememove-core' ); ?>" required />
				<input type="hidden" name="_wpnonce" value="<?php echo esc_attr( wp_create_nonce( 'activate_license' ) ); ?>">
				<button type="submit" class="button tmc-license-key-form__submit" >
					<?php esc_html_e( 'Submit', 'thememove-core' ); ?>
					<i class="fal fa-spinner-third tm-spin"></i>
				</button>
				<p class="license-key-description">
					<?php esc_html_e( 'Show us your license key to get the automatic update.', 'thememove-core' ); ?>
				</p>
			</form>
		<?php endif; ?>

		<?php
		/**
		 * Hook: tmc_box_license_after_content
		 */
		do_action( 'tmc_box_license_after_content' );
		?>

	</div>
</div>
