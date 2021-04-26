<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    Ticket_Solution
 * @subpackage Ticket_Solution/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Ticket_Solution
 * @subpackage Ticket_Solution/includes
 * @author     makewebbetter <webmaster@makewebbetter.com>
 */
class Ticket_Solution {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Ticket_Solution_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $ts_onboard    To initializsed the object of class onboard.
	 */
	protected $ts_onboard;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area,
	 * the public-facing side of the site and common side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		if ( defined( 'TICKET_SOLUTION_VERSION' ) ) {

			$this->version = TICKET_SOLUTION_VERSION;
		} else {

			$this->version = '1.0.0';
		}

		$this->plugin_name = 'ticket-solution';

		$this->ticket_solution_dependencies();
		$this->ticket_solution_locale();
		if ( is_admin() ) {
			$this->ticket_solution_admin_hooks();
		} else {
			$this->ticket_solution_public_hooks();
		}
		$this->ticket_solution_common_hooks();

		$this->ticket_solution_api_hooks();


	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Ticket_Solution_Loader. Orchestrates the hooks of the plugin.
	 * - Ticket_Solution_i18n. Defines internationalization functionality.
	 * - Ticket_Solution_Admin. Defines all hooks for the admin area.
	 * - Ticket_Solution_Common. Defines all hooks for the common area.
	 * - Ticket_Solution_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function ticket_solution_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ticket-solution-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ticket-solution-i18n.php';

		if ( is_admin() ) {

			// The class responsible for defining all actions that occur in the admin area.
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-ticket-solution-admin.php';

			// The class responsible for on-boarding steps for plugin.
			if ( is_dir(  plugin_dir_path( dirname( __FILE__ ) ) . 'onboarding' ) && ! class_exists( 'Ticket_Solution_Onboarding_Steps' ) ) {
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ticket-solution-onboarding-steps.php';
			}

			if ( class_exists( 'Ticket_Solution_Onboarding_Steps' ) ) {
				$ts_onboard_steps = new Ticket_Solution_Onboarding_Steps();
			}
		} else {

			// The class responsible for defining all actions that occur in the public-facing side of the site.
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-ticket-solution-public.php';

		}

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'package/rest-api/class-ticket-solution-rest-api.php';

		/**
		 * This class responsible for defining common functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'common/class-ticket-solution-common.php';

		$this->loader = new Ticket_Solution_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Ticket_Solution_I18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function ticket_solution_locale() {

		$plugin_i18n = new Ticket_Solution_I18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function ticket_solution_admin_hooks() {

		$ts_plugin_admin = new Ticket_Solution_Admin( $this->ts_get_plugin_name(), $this->ts_get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $ts_plugin_admin, 'ts_admin_enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $ts_plugin_admin, 'ts_admin_enqueue_scripts' );

		// Add settings menu for Ticket solution.
		$this->loader->add_action( 'admin_menu', $ts_plugin_admin, 'ts_options_page' );
		$this->loader->add_action( 'admin_menu', $ts_plugin_admin, 'mwb_ts_remove_default_submenu', 50 );

		// All admin actions and filters after License Validation goes here.
		$this->loader->add_filter( 'mwb_add_plugins_menus_array', $ts_plugin_admin, 'ts_admin_submenu_page', 15 );
		$this->loader->add_filter( 'ts_template_settings_array', $ts_plugin_admin, 'ts_admin_template_settings_page', 10 );
		$this->loader->add_filter( 'ts_general_settings_array', $ts_plugin_admin, 'ts_admin_general_settings_page', 10 );

		// Saving tab settings.
		$this->loader->add_action( 'admin_init', $ts_plugin_admin, 'ts_admin_save_tab_settings' );
		// Register global settings menu for product sale ticket.
		$this->loader->add_action( 'admin_init', $ts_plugin_admin, 'mwb_register_product_sale_ticket', 99 );
		// Register global settings menu for api self.
		$this->loader->add_action( 'admin_menu', $ts_plugin_admin, 'mwb_sale_ticket_options_menu', 99 );
		// Setting tab in product panel.
		$this->loader->add_filter( 'woocommerce_product_data_tabs', $ts_plugin_admin, 'custom_product_settings_tabs' );
		// Content for custom tab created.
		$this->loader->add_action( 'woocommerce_product_data_panels', $ts_plugin_admin, 'custom_product_panels' );
		// Save content of setting tab.
		$this->loader->add_action( 'woocommerce_process_product_meta', $ts_plugin_admin, 'save_custom_field_data', 10, 1 );

	}

	/**
	 * Register all of the hooks related to the common functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function ticket_solution_common_hooks() {

		$ts_plugin_common = new Ticket_Solution_Common( $this->ts_get_plugin_name(), $this->ts_get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $ts_plugin_common, 'ts_common_enqueue_styles' );

		$this->loader->add_action( 'wp_enqueue_scripts', $ts_plugin_common, 'ts_common_enqueue_scripts' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function ticket_solution_public_hooks() {

		$ts_plugin_public = new Ticket_Solution_Public( $this->ts_get_plugin_name(), $this->ts_get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $ts_plugin_public, 'ts_public_enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $ts_plugin_public, 'ts_public_enqueue_scripts' );

		// Action for product quantity increased or decreased.
		$this->loader->add_action( 'woocommerce_reduce_order_stock', $ts_plugin_public, 'stock_updated', 99 );

	}

	/**
	 * Register all of the hooks related to the api functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function ticket_solution_api_hooks() {

		$ts_plugin_api = new Ticket_Solution_Rest_Api( $this->ts_get_plugin_name(), $this->ts_get_version() );

		$this->loader->add_action( 'rest_api_init', $ts_plugin_api, 'mwb_ts_add_endpoint' );

	}


	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function ts_run() {
		$this->loader->ts_run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function ts_get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Ticket_Solution_Loader    Orchestrates the hooks of the plugin.
	 */
	public function ts_get_loader() {
		return $this->loader;
	}


	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Ticket_Solution_Onboard    Orchestrates the hooks of the plugin.
	 */
	public function ts_get_onboard() {
		return $this->ts_onboard;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function ts_get_version() {
		return $this->version;
	}

	/**
	 * Predefined default mwb_ts_plug tabs.
	 *
	 * @return  Array       An key=>value pair of Ticket solution tabs.
	 */
	public function mwb_ts_plug_default_tabs() {

		$ts_default_tabs = array();

		$ts_default_tabs['ticket-solution-general'] = array(
			'title'       => esc_html__( 'General Setting', 'ticket-solution' ),
			'name'        => 'ticket-solution-general',
		);
		$ts_default_tabs                            = apply_filters( 'mwb_ts_plugin_standard_admin_settings_tabs', $ts_default_tabs );

		$ts_default_tabs['ticket-solution-system-status'] = array(
			'title'       => esc_html__( 'System Status', 'ticket-solution' ),
			'name'        => 'ticket-solution-system-status',
		);
		$ts_default_tabs['ticket-solution-template']      = array(
			'title'       => esc_html__( 'Templates', 'ticket-solution' ),
			'name'        => 'ticket-solution-template',
		);
		$ts_default_tabs['ticket-solution-overview']      = array(
			'title'       => esc_html__( 'Overview', 'ticket-solution' ),
			'name'        => 'ticket-solution-overview',
		);

		return $ts_default_tabs;
	}

	/**
	 * Locate and load appropriate tempate.
	 *
	 * @since   1.0.0
	 * @param string $path path file for inclusion.
	 * @param array  $params parameters to pass to the file for access.
	 */
	public function mwb_ts_plug_load_template( $path, $params = array() ) {

		$ts_file_path = TICKET_SOLUTION_DIR_PATH . $path;

		if ( file_exists( $ts_file_path ) ) {

			include $ts_file_path;
		} else {

			/* translators: %s: file path */
			$ts_notice = sprintf( esc_html__( 'Unable to locate file at location "%s". Some features may not work properly in this plugin. Please contact us!', 'ticket-solution' ), $ts_file_path );
			$this->mwb_ts_plug_admin_notice( $ts_notice, 'error' );
		}
	}

	/**
	 * Show admin notices.
	 *
	 * @param  string $ts_message    Message to display.
	 * @param  string $type       notice type, accepted values - error/update/update-nag.
	 * @since  1.0.0
	 */
	public static function mwb_ts_plug_admin_notice( $ts_message, $type = 'error' ) {

		$ts_classes = 'notice ';

		switch ( $type ) {

			case 'update':
				$ts_classes .= 'updated is-dismissible';
			break;

			case 'update-nag':
				$ts_classes .= 'update-nag is-dismissible';
			break;

			case 'success':
				$ts_classes .= 'notice-success is-dismissible';
			break;

			default:
				$ts_classes .= 'notice-error is-dismissible';
		}

		$ts_notice  = '<div class="' . esc_attr( $ts_classes ) . ' mwb-errorr-8">';
		$ts_notice .= '<p>' . esc_html( $ts_message ) . '</p>';
		$ts_notice .= '</div>';

		echo wp_kses_post( $ts_notice );
	}


	/**
	 * Show WordPress and server info.
	 *
	 * @return  Array $ts_system_data       returns array of all WordPress and server related information.
	 * @since  1.0.0
	 */
	public function mwb_ts_plug_system_status() {
		global $wpdb;
		$ts_system_status    = array();
		$ts_wordpress_status = array();
		$ts_system_data      = array();

		// Get the web server.
		$ts_system_status['web_server'] = isset( $_SERVER['SERVER_SOFTWARE'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) ) : '';

		// Get PHP version.
		$ts_system_status['php_version'] = function_exists( 'phpversion' ) ? phpversion() : __( 'N/A (phpversion function does not exist)', 'ticket-solution' );

		// Get the server's IP address.
		$ts_system_status['server_ip'] = isset( $_SERVER['SERVER_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_ADDR'] ) ) : '';

		// Get the server's port.
		$ts_system_status['server_port'] = isset( $_SERVER['SERVER_PORT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_PORT'] ) ) : '';

		// Get the uptime.
		$ts_system_status['uptime'] = function_exists( 'exec' ) ? @exec( 'uptime -p' ) : __( 'N/A (make sure exec function is enabled)', 'ticket-solution' );

		// Get the server path.
		$ts_system_status['server_path'] = defined( 'ABSPATH' ) ? ABSPATH : __( 'N/A (ABSPATH constant not defined)', 'ticket-solution' );

		// Get the OS.
		$ts_system_status['os'] = function_exists( 'php_uname' ) ? php_uname( 's' ) : __( 'N/A (php_uname function does not exist)', 'ticket-solution' );

		// Get WordPress version.
		$ts_wordpress_status['wp_version'] = function_exists( 'get_bloginfo' ) ? get_bloginfo( 'version' ) : __( 'N/A (get_bloginfo function does not exist)', 'ticket-solution' );

		// Get and count active WordPress plugins.
		$ts_wordpress_status['wp_active_plugins'] = function_exists( 'get_option' ) ? count( get_option( 'active_plugins' ) ) : __( 'N/A (get_option function does not exist)', 'ticket-solution' );

		// See if this site is multisite or not.
		$ts_wordpress_status['wp_multisite'] = function_exists( 'is_multisite' ) && is_multisite() ? __( 'Yes', 'ticket-solution' ) : __( 'No', 'ticket-solution' );

		// See if WP Debug is enabled.
		$ts_wordpress_status['wp_debug_enabled'] = defined( 'WP_DEBUG' ) ? __( 'Yes', 'ticket-solution' ) : __( 'No', 'ticket-solution' );

		// See if WP Cache is enabled.
		$ts_wordpress_status['wp_cache_enabled'] = defined( 'WP_CACHE' ) ? __( 'Yes', 'ticket-solution' ) : __( 'No', 'ticket-solution' );

		// Get the total number of WordPress users on the site.
		$ts_wordpress_status['wp_users'] = function_exists( 'count_users' ) ? count_users() : __( 'N/A (count_users function does not exist)', 'ticket-solution' );

		// Get the number of published WordPress posts.
		$ts_wordpress_status['wp_posts'] = wp_count_posts()->publish >= 1 ? wp_count_posts()->publish : __( '0', 'ticket-solution' );

		// Get PHP memory limit.
		$ts_system_status['php_memory_limit'] = function_exists( 'ini_get' ) ? (int) ini_get( 'memory_limit' ) : __( 'N/A (ini_get function does not exist)', 'ticket-solution' );

		// Get the PHP error log path.
		$ts_system_status['php_error_log_path'] = ! ini_get( 'error_log' ) ? __( 'N/A', 'ticket-solution' ) : ini_get( 'error_log' );

		// Get PHP max upload size.
		$ts_system_status['php_max_upload'] = function_exists( 'ini_get' ) ? (int) ini_get( 'upload_max_filesize' ) : __( 'N/A (ini_get function does not exist)', 'ticket-solution' );

		// Get PHP max post size.
		$ts_system_status['php_max_post'] = function_exists( 'ini_get' ) ? (int) ini_get( 'post_max_size' ) : __( 'N/A (ini_get function does not exist)', 'ticket-solution' );

		// Get the PHP architecture.
		if ( PHP_INT_SIZE == 4 ) {
			$ts_system_status['php_architecture'] = '32-bit';
		} elseif ( PHP_INT_SIZE == 8 ) {
			$ts_system_status['php_architecture'] = '64-bit';
		} else {
			$ts_system_status['php_architecture'] = 'N/A';
		}

		// Get server host name.
		$ts_system_status['server_hostname'] = function_exists( 'gethostname' ) ? gethostname() : __( 'N/A (gethostname function does not exist)', 'ticket-solution' );

		// Show the number of processes currently running on the server.
		$ts_system_status['processes'] = function_exists( 'exec' ) ? @exec( 'ps aux | wc -l' ) : __( 'N/A (make sure exec is enabled)', 'ticket-solution' );

		// Get the memory usage.
		$ts_system_status['memory_usage'] = function_exists( 'memory_get_peak_usage' ) ? round( memory_get_peak_usage( true ) / 1024 / 1024, 2 ) : 0;

		// Get CPU usage.
		// Check to see if system is Windows, if so then use an alternative since sys_getloadavg() won't work.
		if ( stristr( PHP_OS, 'win' ) ) {
			$ts_system_status['is_windows'] = true;
			$ts_system_status['windows_cpu_usage'] = function_exists( 'exec' ) ? @exec( 'wmic cpu get loadpercentage /all' ) : __( 'N/A (make sure exec is enabled)', 'ticket-solution' );
		}

		// Get the memory limit.
		$ts_system_status['memory_limit'] = function_exists( 'ini_get' ) ? (int) ini_get( 'memory_limit' ) : __( 'N/A (ini_get function does not exist)', 'ticket-solution' );

		// Get the PHP maximum execution time.
		$ts_system_status['php_max_execution_time'] = function_exists( 'ini_get' ) ? ini_get( 'max_execution_time' ) : __( 'N/A (ini_get function does not exist)', 'ticket-solution' );

		// Get outgoing IP address.
		$ts_system_status['outgoing_ip'] = function_exists( 'file_get_contents' ) ? file_get_contents( 'http://ipecho.net/plain' ) : __( 'N/A (file_get_contents function does not exist)', 'ticket-solution' );

		$ts_system_data['php'] = $ts_system_status;
		$ts_system_data['wp']  = $ts_wordpress_status;

		return $ts_system_data;
	}

	/**
	 * Generate html components.
	 *
	 * @param  string $ts_components    html to display.
	 * @since  1.0.0
	 */
	public function mwb_ts_plug_generate_html( $ts_components = array() ) {
		if ( is_array( $ts_components ) && ! empty( $ts_components ) ) {
			foreach ( $ts_components as $ts_component ) {
				if ( ! empty( $ts_component['type'] ) &&  ! empty( $ts_component['id'] ) ) {
					switch ( $ts_component['type'] ) {

						case 'hidden':
						case 'number':
						case 'email':
						case 'text':
							?>
						<div class="mwb-form-group mwb-ts-<?php echo esc_attr($ts_component['type']); ?>">
							<div class="mwb-form-group__label">
								<label for="<?php echo esc_attr( $ts_component['id'] ); ?>" class="mwb-form-label"><?php echo ( isset( $ts_component['title'] ) ? esc_html( $ts_component['title'] ) : '' ); ?></label>
							</div>
							<div class="mwb-form-group__control">
								<label class="mdc-text-field mdc-text-field--outlined">
									<span class="mdc-notched-outline">
										<span class="mdc-notched-outline__leading"></span>
										<span class="mdc-notched-outline__notch">
											<?php if ( 'number' != $ts_component['type'] ) { ?>
												<span class="mdc-floating-label" id="my-label-id" style=""><?php echo ( isset( $ts_component['placeholder'] ) ? esc_attr( $ts_component['placeholder'] ) : '' ); ?></span>
											<?php } ?>
										</span>
										<span class="mdc-notched-outline__trailing"></span>
									</span>
									<input
									class="mdc-text-field__input <?php echo ( isset( $ts_component['class'] ) ? esc_attr( $ts_component['class'] ) : '' ); ?>" 
									name="<?php echo ( isset( $ts_component['name'] ) ? esc_html( $ts_component['name'] ) : esc_html( $ts_component['id'] ) ); ?>"
									id="<?php echo esc_attr( $ts_component['id'] ); ?>"
									type="<?php echo esc_attr( $ts_component['type'] ); ?>"
									value="<?php echo ( isset( $ts_component['value'] ) ? esc_attr( $ts_component['value'] ) : '' ); ?>"
									placeholder="<?php echo ( isset( $ts_component['placeholder'] ) ? esc_attr( $ts_component['placeholder'] ) : '' ); ?>"
									>
								</label>
								<div class="mdc-text-field-helper-line">
									<div class="mdc-text-field-helper-text--persistent mwb-helper-text" id="" aria-hidden="true"><?php echo ( isset( $ts_component['description'] ) ? esc_attr( $ts_component['description'] ) : '' ); ?></div>
								</div>
							</div>
						</div>
							<?php
							break;

						case 'password':
							?>
						<div class="mwb-form-group">
							<div class="mwb-form-group__label">
								<label for="<?php echo esc_attr( $ts_component['id'] ); ?>" class="mwb-form-label"><?php echo ( isset( $ts_component['title'] ) ? esc_html( $ts_component['title'] ) : '' ); ?></label>
							</div>
							<div class="mwb-form-group__control">
								<label class="mdc-text-field mdc-text-field--outlined mdc-text-field--with-trailing-icon">
									<span class="mdc-notched-outline">
										<span class="mdc-notched-outline__leading"></span>
										<span class="mdc-notched-outline__notch">
										</span>
										<span class="mdc-notched-outline__trailing"></span>
									</span>
									<input 
									class="mdc-text-field__input <?php echo ( isset( $ts_component['class'] ) ? esc_attr( $ts_component['class'] ) : '' ); ?> mwb-form__password" 
									name="<?php echo ( isset( $ts_component['name'] ) ? esc_html( $ts_component['name'] ) : esc_html( $ts_component['id'] ) ); ?>"
									id="<?php echo esc_attr( $ts_component['id'] ); ?>"
									type="<?php echo esc_attr( $ts_component['type'] ); ?>"
									value="<?php echo ( isset( $ts_component['value'] ) ? esc_attr( $ts_component['value'] ) : '' ); ?>"
									placeholder="<?php echo ( isset( $ts_component['placeholder'] ) ? esc_attr( $ts_component['placeholder'] ) : '' ); ?>"
									>
									<i class="material-icons mdc-text-field__icon mdc-text-field__icon--trailing mwb-password-hidden" tabindex="0" role="button">visibility</i>
								</label>
								<div class="mdc-text-field-helper-line">
									<div class="mdc-text-field-helper-text--persistent mwb-helper-text" id="" aria-hidden="true"><?php echo ( isset( $ts_component['description'] ) ? esc_attr( $ts_component['description'] ) : '' ); ?></div>
								</div>
							</div>
						</div>
							<?php
							break;

						case 'textarea':
							?>
						<div class="mwb-form-group">
							<div class="mwb-form-group__label">
								<label class="mwb-form-label" for="<?php echo esc_attr( $ts_component['id'] ); ?>"><?php echo ( isset( $ts_component['title'] ) ? esc_html( $ts_component['title'] ) : '' ); ?></label>
							</div>
							<div class="mwb-form-group__control">
								<label class="mdc-text-field mdc-text-field--outlined mdc-text-field--textarea"  	for="text-field-hero-input">
									<span class="mdc-notched-outline">
										<span class="mdc-notched-outline__leading"></span>
										<span class="mdc-notched-outline__notch">
											<span class="mdc-floating-label"><?php echo ( isset( $ts_component['placeholder'] ) ? esc_attr( $ts_component['placeholder'] ) : '' ); ?></span>
										</span>
										<span class="mdc-notched-outline__trailing"></span>
									</span>
									<span class="mdc-text-field__resizer">
										<textarea class="mdc-text-field__input <?php echo ( isset( $ts_component['class'] ) ? esc_attr( $ts_component['class'] ) : '' ); ?>" rows="2" cols="25" aria-label="Label" name="<?php echo ( isset( $ts_component['name'] ) ? esc_html( $ts_component['name'] ) : esc_html( $ts_component['id'] ) ); ?>" id="<?php echo esc_attr( $ts_component['id'] ); ?>" placeholder="<?php echo ( isset( $ts_component['placeholder'] ) ? esc_attr( $ts_component['placeholder'] ) : '' ); ?>"><?php echo ( isset( $ts_component['value'] ) ? esc_textarea( $ts_component['value'] ) : '' ); ?></textarea>
									</span>
								</label>

							</div>
						</div>

							<?php
							break;

						case 'select':
						case 'multiselect':
							?>
						<div class="mwb-form-group">
							<div class="mwb-form-group__label">
								<label class="mwb-form-label" for="<?php echo esc_attr( $ts_component['id'] ); ?>"><?php echo ( isset( $ts_component['title'] ) ? esc_html( $ts_component['title'] ) : '' ); ?></label>
							</div>
							<div class="mwb-form-group__control">
								<div class="mwb-form-select">
									<select id="<?php echo esc_attr( $ts_component['id'] ); ?>" name="<?php echo ( isset( $ts_component['name'] ) ? esc_html( $ts_component['name'] ) : '' ); ?><?php echo ( 'multiselect' === $ts_component['type'] ) ? '[]' : ''; ?>" id="<?php echo esc_attr( $ts_component['id'] ); ?>" class="mdl-textfield__input <?php echo ( isset( $ts_component['class'] ) ? esc_attr( $ts_component['class'] ) : '' ); ?>" <?php echo 'multiselect' === $ts_component['type'] ? 'multiple="multiple"' : ''; ?> >
										<?php
										foreach ( $ts_component['options'] as $ts_key => $ts_val ) {
											?>
											<option value="<?php echo esc_attr( $ts_key ); ?>"
												<?php
												if ( is_array( $ts_component['value'] ) ) {
													selected( in_array( (string) $ts_key, $ts_component['value'], true ), true );
												} else {
													selected( $ts_component['value'], (string) $ts_key );
												}
												?>
												>
												<?php echo esc_html( $ts_val ); ?>
											</option>
											<?php
										}
										?>
									</select>
									<label class="mdl-textfield__label" for="octane"><?php echo esc_html( $ts_component['description'] ); ?><?php echo ( isset( $ts_component['description'] ) ? esc_attr( $ts_component['description'] ) : '' ); ?></label>
								</div>
							</div>
						</div>

							<?php
							break;

						case 'checkbox':
							?>
						<div class="mwb-form-group">
							<div class="mwb-form-group__label">
								<label for="<?php echo esc_attr( $ts_component['id'] ); ?>" class="mwb-form-label"><?php echo ( isset( $ts_component['title'] ) ? esc_html( $ts_component['title'] ) : '' ); // WPCS: XSS ok. ?></label>
							</div>
							<div class="mwb-form-group__control mwb-pl-4">
								<div class="mdc-form-field">
									<div class="mdc-checkbox">
										<input 
										name="<?php echo ( isset( $ts_component['name'] ) ? esc_html( $ts_component['name'] ) : esc_html( $ts_component['id'] ) ); ?>"
										id="<?php echo esc_attr( $ts_component['id'] ); ?>"
										type="checkbox"
										class="mdc-checkbox__native-control <?php echo ( isset( $ts_component['class'] ) ? esc_attr( $ts_component['class'] ) : '' ); ?>"
										value="<?php echo ( isset( $ts_component['value'] ) ? esc_attr( $ts_component['value'] ) : '' ); ?>"
										<?php checked( $ts_component['value'], '1' ); ?>
										/>
										<div class="mdc-checkbox__background">
											<svg class="mdc-checkbox__checkmark" viewBox="0 0 24 24">
												<path class="mdc-checkbox__checkmark-path" fill="none" d="M1.73,12.91 8.1,19.28 22.79,4.59"/>
											</svg>
											<div class="mdc-checkbox__mixedmark"></div>
										</div>
										<div class="mdc-checkbox__ripple"></div>
									</div>
									<label for="checkbox-1"><?php echo ( isset( $ts_component['description'] ) ? esc_attr( $ts_component['description'] ) : '' ); ?></label>
								</div>
							</div>
						</div>
							<?php
							break;

						case 'radio':
							?>
						<div class="mwb-form-group">
							<div class="mwb-form-group__label">
								<label for="<?php echo esc_attr( $ts_component['id'] ); ?>" class="mwb-form-label"><?php echo ( isset( $ts_component['title'] ) ? esc_html( $ts_component['title'] ) : '' ); // WPCS: XSS ok. ?></label>
							</div>
							<div class="mwb-form-group__control mwb-pl-4">
								<div class="mwb-flex-col">
									<?php
									foreach ( $ts_component['options'] as $ts_radio_key => $ts_radio_val ) {
										?>
										<div class="mdc-form-field">
											<div class="mdc-radio">
												<input
												name="<?php echo ( isset( $ts_component['name'] ) ? esc_html( $ts_component['name'] ) : esc_html( $ts_component['id'] ) ); ?>"
												value="<?php echo esc_attr( $ts_radio_key ); ?>"
												type="radio"
												class="mdc-radio__native-control <?php echo ( isset( $ts_component['class'] ) ? esc_attr( $ts_component['class'] ) : '' ); ?>"
												<?php checked( $ts_radio_key, $ts_component['value'] ); ?>
												>
												<div class="mdc-radio__background">
													<div class="mdc-radio__outer-circle"></div>
													<div class="mdc-radio__inner-circle"></div>
												</div>
												<div class="mdc-radio__ripple"></div>
											</div>
											<label for="radio-1"><?php echo esc_html( $ts_radio_val ); ?></label>
										</div>	
										<?php
									}
									?>
								</div>
							</div>
						</div>
							<?php
							break;

						case 'radio-switch':
							?>

						<div class="mwb-form-group">
							<div class="mwb-form-group__label">
								<label for="" class="mwb-form-label"><?php echo ( isset( $ts_component['title'] ) ? esc_html( $ts_component['title'] ) : '' ); ?></label>
							</div>
							<div class="mwb-form-group__control">
								<div>
									<div class="mdc-switch">
										<div class="mdc-switch__track"></div>
										<div class="mdc-switch__thumb-underlay">
											<div class="mdc-switch__thumb"></div>
											<input name="<?php echo ( isset( $ts_component['name'] ) ? esc_html( $ts_component['name'] ) : esc_html( $ts_component['id'] ) ); ?>" type="checkbox" id="<?php echo esc_html( $ts_component['id'] ); ?>" value="on" class="mdc-switch__native-control <?php echo ( isset( $ts_component['class'] ) ? esc_attr( $ts_component['class'] ) : '' ); ?>" role="switch" aria-checked="<?php if ( 'on' == $ts_component['value'] ) echo 'true'; else echo 'false'; ?>"
											<?php checked( $ts_component['value'], 'on' ); ?>
											>
										</div>
									</div>
								</div>
							</div>
						</div>
							<?php
							break;

						case 'button':
							?>
						<div class="mwb-form-group">
							<div class="mwb-form-group__label"></div>
							<div class="mwb-form-group__control">
								<button class="mdc-button mdc-button--raised" name= "<?php echo ( isset( $ts_component['name'] ) ? esc_html( $ts_component['name'] ) : esc_html( $ts_component['id'] ) ); ?>"
									id="<?php echo esc_attr( $ts_component['id'] ); ?>"> <span class="mdc-button__ripple"></span>
									<span class="mdc-button__label <?php echo ( isset( $ts_component['class'] ) ? esc_attr( $ts_component['class'] ) : '' ); ?>"><?php echo ( isset( $ts_component['button_text'] ) ? esc_html( $ts_component['button_text'] ) : '' ); ?></span>
								</button>
							</div>
						</div>

							<?php
							break;

						case 'multi':
							?>
							<div class="mwb-form-group mwb-isfw-<?php echo esc_attr( $ts_component['type'] ); ?>">
								<div class="mwb-form-group__label">
									<label for="<?php echo esc_attr( $ts_component['id'] ); ?>" class="mwb-form-label"><?php echo ( isset( $ts_component['title'] ) ? esc_html( $ts_component['title'] ) : '' ); ?></label>
									</div>
									<div class="mwb-form-group__control">
									<?php
									foreach ( $ts_component['value'] as $component ) {
										?>
											<label class="mdc-text-field mdc-text-field--outlined">
												<span class="mdc-notched-outline">
													<span class="mdc-notched-outline__leading"></span>
													<span class="mdc-notched-outline__notch">
														<?php if ( 'number' != $component['type'] ) { ?>
															<span class="mdc-floating-label" id="my-label-id" style=""><?php echo ( isset( $ts_component['placeholder'] ) ? esc_attr( $ts_component['placeholder'] ) : '' ); ?></span>
														<?php } ?>
													</span>
													<span class="mdc-notched-outline__trailing"></span>
												</span>
												<input 
												class="mdc-text-field__input <?php echo ( isset( $ts_component['class'] ) ? esc_attr( $ts_component['class'] ) : '' ); ?>" 
												name="<?php echo ( isset( $ts_component['name'] ) ? esc_html( $ts_component['name'] ) : esc_html( $ts_component['id'] ) ); ?>"
												id="<?php echo esc_attr( $component['id'] ); ?>"
												type="<?php echo esc_attr( $component['type'] ); ?>"
												value="<?php echo ( isset( $ts_component['value'] ) ? esc_attr( $ts_component['value'] ) : '' ); ?>"
												placeholder="<?php echo ( isset( $ts_component['placeholder'] ) ? esc_attr( $ts_component['placeholder'] ) : '' ); ?>"
												<?php echo esc_attr( ( 'number' === $component['type'] ) ? 'max=10 min=0' : '' ); ?>
												>
											</label>
								<?php } ?>
									<div class="mdc-text-field-helper-line">
										<div class="mdc-text-field-helper-text--persistent mwb-helper-text" id="" aria-hidden="true"><?php echo ( isset( $ts_component['description'] ) ? esc_attr( $ts_component['description'] ) : '' ); ?></div>
									</div>
								</div>
							</div>
								<?php
							break;
						case 'color':
						case 'date':
						case 'file':
							?>
							<div class="mwb-form-group mwb-isfw-<?php echo esc_attr( $ts_component['type'] ); ?>">
								<div class="mwb-form-group__label">
									<label for="<?php echo esc_attr( $ts_component['id'] ); ?>" class="mwb-form-label"><?php echo ( isset( $ts_component['title'] ) ? esc_html( $ts_component['title'] ) : '' ); ?></label>
								</div>
								<div class="mwb-form-group__control">
									<label class="mdc-text-field mdc-text-field--outlined">
										<input 
										class="<?php echo ( isset( $ts_component['class'] ) ? esc_attr( $ts_component['class'] ) : '' ); ?>" 
										name="<?php echo ( isset( $ts_component['name'] ) ? esc_html( $ts_component['name'] ) : esc_html( $ts_component['id'] ) ); ?>"
										id="<?php echo esc_attr( $ts_component['id'] ); ?>"
										type="<?php echo esc_attr( $ts_component['type'] ); ?>"
										value="<?php echo ( isset( $ts_component['value'] ) ? esc_attr( $ts_component['value'] ) : '' ); ?>"
										<?php echo esc_html( ( 'date' === $ts_component['type'] ) ? 'max='. date( 'Y-m-d', strtotime( date( "Y-m-d", mktime() ) . " + 365 day" ) ) .' ' . 'min=' . date( "Y-m-d" ) . '' : '' ); ?>
										>
									</label>
									<div class="mdc-text-field-helper-line">
										<div class="mdc-text-field-helper-text--persistent mwb-helper-text" id="" aria-hidden="true"><?php echo ( isset( $ts_component['description'] ) ? esc_attr( $ts_component['description'] ) : '' ); ?></div>
									</div>
								</div>
							</div>
							<?php
							break;

						case 'submit':
							?>
						<tr valign="top">
							<td scope="row">
								<input type="submit" class="button button-primary" 
								name="<?php echo ( isset( $ts_component['name'] ) ? esc_html( $ts_component['name'] ) : esc_html( $ts_component['id'] ) ); ?>"
								id="<?php echo esc_attr( $ts_component['id'] ); ?>"
								class="<?php echo ( isset( $ts_component['class'] ) ? esc_attr( $ts_component['class'] ) : '' ); ?>"
								value="<?php echo esc_attr( $ts_component['button_text'] ); ?>"
								/>
							</td>
						</tr>
							<?php
							break;

						default:
							break;
					}
				}
			}
		}
	}
}
