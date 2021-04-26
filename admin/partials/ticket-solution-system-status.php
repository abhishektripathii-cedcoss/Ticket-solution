<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the html for system status.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    Ticket_Solution
 * @subpackage Ticket_Solution/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// Template for showing information about system status.
global $ts_mwb_ts_obj;
$ts_default_status = $ts_mwb_ts_obj->mwb_ts_plug_system_status();
$ts_wordpress_details = is_array( $ts_default_status['wp'] ) && ! empty( $ts_default_status['wp'] ) ? $ts_default_status['wp'] : array();
$ts_php_details = is_array( $ts_default_status['php'] ) && ! empty( $ts_default_status['php'] ) ? $ts_default_status['php'] : array();
?>
<div class="mwb-ts-table-wrap">
	<div class="mwb-col-wrap">
		<div id="mwb-ts-table-inner-container" class="table-responsive mdc-data-table">
			<div class="mdc-data-table__table-container">
				<table class="mwb-ts-table mdc-data-table__table mwb-table" id="mwb-ts-wp">
					<thead>
						<tr>
							<th class="mdc-data-table__header-cell"><?php esc_html_e( 'WP Variables', 'ticket-solution' ); ?></th>
							<th class="mdc-data-table__header-cell"><?php esc_html_e( 'WP Values', 'ticket-solution' ); ?></th>
						</tr>
					</thead>
					<tbody class="mdc-data-table__content">
						<?php if ( is_array( $ts_wordpress_details ) && ! empty( $ts_wordpress_details ) ) { ?>
							<?php foreach ( $ts_wordpress_details as $wp_key => $wp_value ) { ?>
								<?php if ( isset( $wp_key ) && 'wp_users' != $wp_key ) { ?>
									<tr class="mdc-data-table__row">
										<td class="mdc-data-table__cell"><?php echo esc_html( $wp_key ); ?></td>
										<td class="mdc-data-table__cell"><?php echo esc_html( $wp_value ); ?></td>
									</tr>
								<?php } ?>
							<?php } ?>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="mwb-col-wrap">
		<div id="mwb-ts-table-inner-container" class="table-responsive mdc-data-table">
			<div class="mdc-data-table__table-container">
				<table class="mwb-ts-table mdc-data-table__table mwb-table" id="mwb-ts-sys">
					<thead>
						<tr>
							<th class="mdc-data-table__header-cell"><?php esc_html_e( 'Sysytem Variables', 'ticket-solution' ); ?></th>
							<th class="mdc-data-table__header-cell"><?php esc_html_e( 'System Values', 'ticket-solution' ); ?></th>
						</tr>
					</thead>
					<tbody class="mdc-data-table__content">
						<?php if ( is_array( $ts_php_details ) && ! empty( $ts_php_details ) ) { ?>
							<?php foreach ( $ts_php_details as $php_key => $php_value ) { ?>
								<tr class="mdc-data-table__row">
									<td class="mdc-data-table__cell"><?php echo esc_html( $php_key ); ?></td>
									<td class="mdc-data-table__cell"><?php echo esc_html( $php_value ); ?></td>
								</tr>
							<?php } ?>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
