<?php
/**
 * Plugins Box
 *
 * @package ThemeMove_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

$tmc_plugins            = apply_filters( 'tmc_plugins', array() );
$installed_plugins      = class_exists( 'TGM_Plugin_Activation' ) ? TGM_Plugin_Activation::$instance->plugins : array();
$required_plugins_count = 0;
?>
<div class="tmc-box tmc-box--step-red tmc-box--plugins">
	<div class="tmc-box__header">
		<span class="tmc-box__number">1</span>
		<span><?php esc_html_e( 'Install Plugins', 'thememove-core' ); ?></span>
	</div>
	<div class="tmc-box__body">

		<?php
		/**
		 * Hook: tmc_box_plugins_before_content
		 */
		do_action( 'tmc_box_plugins_before_content' );
		?>

		<?php if ( ! empty( $tmc_plugins ) && class_exists( 'TGM_Plugin_Activation' ) ) : ?>
			<table class="wp-list-table widefat striped">
				<thead>
					<tr>
						<th class="tmc-plugin__name"><?php esc_html_e( 'Plugin', 'thememove-core' ); ?></th>
						<th class="tmc-plugin__version"><?php esc_html_e( 'Version', 'thememove-core' ); ?></th>
						<th class="tmc-plugin__type"><?php esc_html_e( 'Type', 'thememove-core' ); ?></th>
						<th class="tmc-plugin__action"><?php esc_html_e( 'Action', 'thememove-core' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $tmc_plugins as $plugin ) : ?>
						<?php
						$css_class = 'tmc-plugin';
						$icon      = '';

						if ( $plugin['required'] ) {
							$css_class .= ' tmc-plugin--required';
							if ( TGM_Plugin_Activation::$instance->is_plugin_active( $plugin['slug'] ) ) {
								$css_class .= ' tmc-plugin--activated';
								$icon       = '<i class="fal fa-check"></i>';
							} else {
								$css_class .= ' tmc-plugin--deactivated';
								$icon       = '<i class="fal fa-times"></i>';
								$required_plugins_count++;
							}
						} else {
							$css_class .= '';
						}

						$plugin_obj = $installed_plugins[ $plugin['slug'] ];
						?>
					<tr class="<?php echo esc_attr( $css_class ); ?>">
						<td class="tmc-plugin__name"><?php echo wp_kses_post( $icon ); ?><?php echo esc_html( $plugin['name'] ); ?></td>
						<td class="tmc-plugin__version"><?php echo isset( $plugin['version'] ) ? esc_html( $plugin['version'] ) : ''; ?></td>
						<td class="tmc-plugin__type"><?php echo $plugin['required'] ? esc_html__( 'Required', 'thememove-core' ) : esc_html__( 'Recommended', 'thememove-core' ); ?></td>
						<td class="tmc-plugin__action"><?php echo ThemeMove_Plugins::get_plugin_action( $plugin_obj ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php else : ?>
			<p><?php esc_html_e( 'This theme doesn\'t require any plugins.', 'thememove-core' ); ?></p>
		<?php endif; ?>

		<?php
		/**
		 * Hook: tmc_box_plugins_after_content
		 */
		do_action( 'tmc_box_plugins_after_content' );
		?>

	</div>
	<div class="tmc-box__footer">
		<?php if ( $required_plugins_count ) : ?>
			<span style="color: #dc433f">
			<?php
			/* translators: %s: Deactivated Plugin count */
			echo sprintf( wp_kses_post( __( 'Please install and activate all required plugins (%s)', 'thememove-core' ) ), esc_html( $required_plugins_count ) );
			?>
			</span>
		<?php else : ?>
			<span style="color: #6fbcae"><?php esc_html_e( 'All required plugins are activated. Now you can import the demo data.', 'thememove-core' ); ?></span>
		<?php endif; ?>
	</div>
</div>
