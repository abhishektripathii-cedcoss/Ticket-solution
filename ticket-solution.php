<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://makewebbetter.com/
 * @since             1.0.0
 * @package           Ticket_Solution
 *
 * @wordpress-plugin
 * Plugin Name:       Ticket solution
 * Plugin URI:        https://makewebbetter.com/product/ticket-solution/
 * Description:       Your Basic Plugin
 * Version:           1.0.0
 * Author:            makewebbetter
 * Author URI:        https://makewebbetter.com/
 * Text Domain:       ticket-solution
 * Domain Path:       /languages
 *
 * Requires at least: 4.6
 * Tested up to:      4.9.5
 *
 * License:           GNU General Public License v3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}
// Action for checking woocommerce plugin is activated.

add_action( 'plugins_loaded', 'plugin_loaded_callback' );

/**
 * Callback function on plugins loaded hook
 *
 * @return void
 */
function plugin_loaded_callback() {
	check_plugin_woo_activation();
}

/**
 * Callback function for checking dependency on woocommerce plugin.
 */
function check_plugin_woo_activation() {
	if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) {
		add_action( 'admin_init', 'check_woocommerce_activation' );
		add_action( 'admin_notices', 'display_notices' );
	}
}

/**
 * Deactivating plugin function when woocommerce is not active.
 *
 * @return void
 */
function check_woocommerce_activation() {
	deactivate_plugins( plugin_basename( __FILE__ ) );
}

/**
 * Displaying notice function.
 *
 * @return void
 */
function display_notices() {
	if ( isset( $_GET['activate'] ) ) {
		unset( $_GET['activate'] );
	}
	?>
	<div class="error notice">
		<p>
			<strong><?php esc_html_e( 'Warning:', 'text-domain' ); ?></strong>
			<?php esc_html_e( 'Your Current Plugin', 'text-domain' ); ?> <strong><?php esc_html_e( 'TICKET SOLUTION', 'text-domain' ); ?> </strong>
			<?php esc_html_e( 'wont execute because the', 'text-domain' ); ?> <strong><?php esc_html_e( 'Woocommerce Plugin', 'text-domain' ); ?></strong> <?php esc_html_e( 'is not active.Please activate these ', 'text-domain' ); ?>
			<a href="plugins.php"><?php esc_html_e( 'plugin.', 'text-domain' ); ?></a>
		</p>
	</div>
	<?php
}

/**
 * Define plugin constants.
 *
 * @since             1.0.0
 */
function define_ticket_solution_constants() {

	ticket_solution_constants( 'TICKET_SOLUTION_VERSION', '1.0.0' );
	ticket_solution_constants( 'TICKET_SOLUTION_DIR_PATH', plugin_dir_path( __FILE__ ) );
	ticket_solution_constants( 'TICKET_SOLUTION_DIR_URL', plugin_dir_url( __FILE__ ) );
	ticket_solution_constants( 'TICKET_SOLUTION_SERVER_URL', 'https://makewebbetter.com' );
	ticket_solution_constants( 'TICKET_SOLUTION_ITEM_REFERENCE', 'Ticket solution' );
}


/**
 * Callable function for defining plugin constants.
 *
 * @param   String $key    Key for contant.
 * @param   String $value   value for contant.
 * @since             1.0.0
 */
function ticket_solution_constants( $key, $value ) {

	if ( ! defined( $key ) ) {

		define( $key, $value );
	}
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-ticket-solution-activator.php
 */
function activate_ticket_solution() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ticket-solution-activator.php';
	Ticket_Solution_Activator::ticket_solution_activate();
	$mwb_ts_active_plugin = get_option( 'mwb_all_plugins_active', false );
	if ( is_array( $mwb_ts_active_plugin ) && ! empty( $mwb_ts_active_plugin ) ) {
		$mwb_ts_active_plugin['ticket-solution'] = array(
			'plugin_name' => __( 'Ticket solution', 'ticket-solution' ),
			'active' => '1',
		);
	} else {
		$mwb_ts_active_plugin = array();
		$mwb_ts_active_plugin['ticket-solution'] = array(
			'plugin_name' => __( 'Ticket solution', 'ticket-solution' ),
			'active' => '1',
		);
	}
	update_option( 'mwb_all_plugins_active', $mwb_ts_active_plugin );
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-ticket-solution-deactivator.php
 */
function deactivate_ticket_solution() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ticket-solution-deactivator.php';
	Ticket_Solution_Deactivator::ticket_solution_deactivate();
	$mwb_ts_deactive_plugin = get_option( 'mwb_all_plugins_active', false );
	if ( is_array( $mwb_ts_deactive_plugin ) && ! empty( $mwb_ts_deactive_plugin ) ) {
		foreach ( $mwb_ts_deactive_plugin as $mwb_ts_deactive_key => $mwb_ts_deactive ) {
			if ( 'ticket-solution' === $mwb_ts_deactive_key ) {
				$mwb_ts_deactive_plugin[ $mwb_ts_deactive_key ]['active'] = '0';
			}
		}
	}
	update_option( 'mwb_all_plugins_active', $mwb_ts_deactive_plugin );
}

register_activation_hook( __FILE__, 'activate_ticket_solution' );
register_deactivation_hook( __FILE__, 'deactivate_ticket_solution' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-ticket-solution.php';


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_ticket_solution() {
	define_ticket_solution_constants();

	$ts_plugin_standard = new Ticket_Solution();
	$ts_plugin_standard->ts_run();
	$GLOBALS['ts_mwb_ts_obj'] = $ts_plugin_standard;

}
run_ticket_solution();


// Add settings link on plugin page.
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'ticket_solution_settings_link' );

/**
 * Settings link.
 *
 * @since    1.0.0
 * @param   Array $links    Settings link array.
 */
function ticket_solution_settings_link( $links ) {

	$my_link = array(
		'<a href="' . admin_url( 'admin.php?page=ticket_solution_menu' ) . '">' . __( 'Settings', 'ticket-solution' ) . '</a>',
	);
	return array_merge( $my_link, $links );
}

/**
 * Adding custom setting links at the plugin activation list.
 *
 * @param array  $links_array array containing the links to plugin.
 * @param string $plugin_file_name plugin file name.
 * @return array
*/
function ticket_solution_custom_settings_at_plugin_tab( $links_array, $plugin_file_name ) {
	if ( strpos( $plugin_file_name, basename( __FILE__ ) ) ) {
		$links_array[] = '<a href="#" target="_blank"><img src="' . esc_html( TICKET_SOLUTION_DIR_URL ) . 'admin/image/Demo.svg" class="mwb-info-img" alt="Demo image">'.__( 'Demo', 'ticket-solution' ).'</a>';
		$links_array[] = '<a href="#" target="_blank"><img src="' . esc_html( TICKET_SOLUTION_DIR_URL ) . 'admin/image/Documentation.svg" class="mwb-info-img" alt="documentation image">'.__( 'Documentation', 'ticket-solution' ).'</a>';
		$links_array[] = '<a href="#" target="_blank"><img src="' . esc_html( TICKET_SOLUTION_DIR_URL ) . 'admin/image/Support.svg" class="mwb-info-img" alt="support image">'.__( 'Support', 'ticket-solution' ).'</a>';
	}
	return $links_array;
}
add_filter( 'plugin_row_meta', 'ticket_solution_custom_settings_at_plugin_tab', 10, 2 );
