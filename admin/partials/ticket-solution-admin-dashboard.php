<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    Ticket_Solution
 * @subpackage Ticket_Solution/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {

	exit(); // Exit if accessed directly.
}

global $ts_mwb_ts_obj;
$ts_active_tab   = isset( $_GET['ts_tab'] ) ? sanitize_key( $_GET['ts_tab'] ) : 'ticket-solution-general';
$ts_default_tabs = $ts_mwb_ts_obj->mwb_ts_plug_default_tabs();
?>
<header>
	<div class="mwb-header-container mwb-bg-white mwb-r-8">
		<h1 class="mwb-header-title"><?php echo esc_attr( strtoupper( str_replace( '-', ' ', $ts_mwb_ts_obj->ts_get_plugin_name() ) ) ); ?></h1>
		<a href="https://docs.makewebbetter.com/" target="_blank" class="mwb-link"><?php esc_html_e( 'Documentation', 'ticket-solution' ); ?></a>
		<span>|</span>
		<a href="https://makewebbetter.com/contact-us/" target="_blank" class="mwb-link"><?php esc_html_e( 'Support', 'invoice-system-for-woocommerce' ); ?></a>
	</div>
</header>

<main class="mwb-main mwb-bg-white mwb-r-8">
	<nav class="mwb-navbar">
		<ul class="mwb-navbar__items">
			<?php
			if ( is_array( $ts_default_tabs ) && ! empty( $ts_default_tabs ) ) {

				foreach ( $ts_default_tabs as $ts_tab_key => $ts_default_tabs ) {

					$ts_tab_classes = 'mwb-link ';

					if ( ! empty( $ts_active_tab ) && $ts_active_tab === $ts_tab_key ) {
						$ts_tab_classes .= 'active';
					}
					?>
					<li>
						<a id="<?php echo esc_attr( $ts_tab_key ); ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=ticket_solution_menu' ) . '&ts_tab=' . esc_attr( $ts_tab_key ) ); ?>" class="<?php echo esc_attr( $ts_tab_classes ); ?>"><?php echo esc_html( $ts_default_tabs['title'] ); ?></a>
					</li>
					<?php
				}
			}
			?>
		</ul>
	</nav>

	<section class="mwb-section">
		<div>
			<?php 
				do_action( 'mwb_ts_before_general_settings_form' );
						// if submenu is directly clicked on woocommerce.
				if ( empty( $ts_active_tab ) ) {
					$ts_active_tab = 'mwb_ts_plug_general';
				}

						// look for the path based on the tab id in the admin templates.
				$ts_tab_content_path = 'admin/partials/' . $ts_active_tab . '.php';

				$ts_mwb_ts_obj->mwb_ts_plug_load_template( $ts_tab_content_path );

				do_action( 'mwb_ts_after_general_settings_form' ); 
			?>
		</div>
	</section>
