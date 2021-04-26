<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    Ticket_Solution
 * @subpackage Ticket_Solution/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Ticket_Solution
 * @subpackage Ticket_Solution/admin
 * @author     makewebbetter <webmaster@makewebbetter.com>
 */
class Ticket_Solution_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 * @param    string $hook      The plugin page slug.
	 */
	public function ts_admin_enqueue_styles( $hook ) {
		$screen = get_current_screen();
		if ( isset( $screen->id ) && 'makewebbetter_page_ticket_solution_menu' == $screen->id ) {

			
			wp_enqueue_style( 'mwb-ts-meterial-css', TICKET_SOLUTION_DIR_URL . 'package/lib/material-design/material-components-web.min.css', array(), time(), 'all' );
			wp_enqueue_style( 'mwb-ts-meterial-css2', TICKET_SOLUTION_DIR_URL . 'package/lib/material-design/material-components-v5.0-web.min.css', array(), time(), 'all' );
			wp_enqueue_style( 'mwb-ts-meterial-lite', TICKET_SOLUTION_DIR_URL . 'package/lib/material-design/material-lite.min.css', array(), time(), 'all' );
			
			wp_enqueue_style( 'mwb-ts-meterial-icons-css', TICKET_SOLUTION_DIR_URL . 'package/lib/material-design/icon.css', array(), time(), 'all' );
			
			wp_enqueue_style( $this->plugin_name . '-admin-global', TICKET_SOLUTION_DIR_URL . 'admin/src/scss/ticket-solution-admin-global.css', array( 'mwb-ts-meterial-icons-css' ), time(), 'all' );
			
			wp_enqueue_style( $this->plugin_name, TICKET_SOLUTION_DIR_URL . 'admin/src/scss/ticket-solution-admin.scss', array(), $this->version, 'all' );
			wp_enqueue_style( 'mwb-admin-min-css', TICKET_SOLUTION_DIR_URL . 'admin/css/mwb-admin.min.css', array(), $this->version, 'all' );
		}
		wp_enqueue_style( 'mwb-ts-select2-css', TICKET_SOLUTION_DIR_URL . 'package/lib/select-2/ticket-solution-select2.css', array(), time(), 'all' );
		wp_enqueue_style( 'mwb-admin-custom-css', TICKET_SOLUTION_DIR_URL . 'admin/src/scss/ticket-solution-admin-custom.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 * @param    string $hook      The plugin page slug.
	 */
	public function ts_admin_enqueue_scripts( $hook ) {

		$screen = get_current_screen();
		if ( isset( $screen->id ) && 'makewebbetter_page_ticket_solution_menu' == $screen->id ) {

			wp_enqueue_script( 'mwb-ts-metarial-js', TICKET_SOLUTION_DIR_URL . 'package/lib/material-design/material-components-web.min.js', array(), time(), false );
			wp_enqueue_script( 'mwb-ts-metarial-js2', TICKET_SOLUTION_DIR_URL . 'package/lib/material-design/material-components-v5.0-web.min.js', array(), time(), false );
			wp_enqueue_script( 'mwb-ts-metarial-lite', TICKET_SOLUTION_DIR_URL . 'package/lib/material-design/material-lite.min.js', array(), time(), false );

		}
		wp_enqueue_script( 'mwb-ts-select2', TICKET_SOLUTION_DIR_URL . 'package/lib/select-2/ticket-solution-select2.js', array( 'jquery' ), time(), false );
		wp_register_script( $this->plugin_name . 'admin-js', TICKET_SOLUTION_DIR_URL . 'admin/src/js/ticket-solution-admin.js', array( 'jquery', 'mwb-ts-select2', 'mwb-ts-metarial-js', 'mwb-ts-metarial-js2', 'mwb-ts-metarial-lite' ), $this->version, false );

		wp_localize_script(
			$this->plugin_name . 'admin-js',
			'ts_admin_param',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'reloadurl' => admin_url( 'admin.php?page=ticket_solution_menu' ),
				'ts_gen_tab_enable' => get_option( 'ts_radio_switch_demo' ),
			)
		);

		wp_enqueue_script( $this->plugin_name . 'admin-js' );
		wp_enqueue_script( 'mwb-ticketsolution-admin-custom-js', TICKET_SOLUTION_DIR_URL . 'admin/src/js/ticket-solution-admin-custom.js', array( 'jquery', 'mwb-ts-select2' ), $this->version, true );
	}

	/**
	 * Adding settings menu for Ticket solution.
	 *
	 * @since    1.0.0
	 */
	public function ts_options_page() {
		global $submenu;
		if ( empty( $GLOBALS['admin_page_hooks']['mwb-plugins'] ) ) {
			add_menu_page( __( 'MakeWebBetter', 'ticket-solution' ), __( 'MakeWebBetter', 'ticket-solution' ), 'manage_options', 'mwb-plugins', array( $this, 'mwb_plugins_listing_page' ), TICKET_SOLUTION_DIR_URL . 'admin/src/images/MWB_Grey-01.svg', 15 );
			$ts_menus = apply_filters( 'mwb_add_plugins_menus_array', array() );
			if ( is_array( $ts_menus ) && ! empty( $ts_menus ) ) {
				foreach ( $ts_menus as $ts_key => $ts_value ) {
					add_submenu_page( 'mwb-plugins', $ts_value['name'], $ts_value['name'], 'manage_options', $ts_value['menu_link'], array( $ts_value['instance'], $ts_value['function'] ) );
				}
			}
		}
	}

	/**
	 * Removing default submenu of parent menu in backend dashboard
	 *
	 * @since   1.0.0
	 */
	public function mwb_ts_remove_default_submenu() {
		global $submenu;
		if ( is_array( $submenu ) && array_key_exists( 'mwb-plugins', $submenu ) ) {
			if ( isset( $submenu['mwb-plugins'][0] ) ) {
				unset( $submenu['mwb-plugins'][0] );
			}
		}
	}


	/**
	 * Ticket solution ts_admin_submenu_page.
	 *
	 * @since 1.0.0
	 * @param array $menus Marketplace menus.
	 */
	public function ts_admin_submenu_page( $menus = array() ) {
		$menus[] = array(
			'name'            => __( 'Ticket solution', 'ticket-solution' ),
			'slug'            => 'ticket_solution_menu',
			'menu_link'       => 'ticket_solution_menu',
			'instance'        => $this,
			'function'        => 'ts_options_menu_html',
		);
		return $menus;
	}


	/**
	 * Ticket solution mwb_plugins_listing_page.
	 *
	 * @since 1.0.0
	 */
	public function mwb_plugins_listing_page() {
		$active_marketplaces = apply_filters( 'mwb_add_plugins_menus_array', array() );
		if ( is_array( $active_marketplaces ) && ! empty( $active_marketplaces ) ) {
			require TICKET_SOLUTION_DIR_PATH . 'admin/partials/welcome.php';
		}
	}

	/**
	 * Ticket solution admin menu page.
	 *
	 * @since    1.0.0
	 */
	public function ts_options_menu_html() {

		include_once TICKET_SOLUTION_DIR_PATH . 'admin/partials/ticket-solution-admin-dashboard.php';
	}


	/**
	 * Ticket solution admin menu page.
	 *
	 * @since    1.0.0
	 * @param array $ts_settings_general Settings fields.
	 */
	public function ts_admin_general_settings_page( $ts_settings_general ) {

		$ts_settings_general = array(
			array(
				'title' => __( 'Enable plugin', 'ticket-solution' ),
				'type'  => 'radio-switch',
				'description'  => __( 'Enable plugin to start the functionality.', 'ticket-solution' ),
				'id'    => 'ts_radio_switch_demo',
				'value' => get_option( 'ts_radio_switch_demo' ),
				'class' => 'ts-radio-switch-class',
				'options' => array(
					'yes' => __( 'YES', 'ticket-solution' ),
					'no' => __( 'NO', 'ticket-solution' ),
				),
			),

			array(
				'type'  => 'button',
				'id'    => 'ts_button_demo',
				'button_text' => __( 'Button Demo', 'ticket-solution' ),
				'class' => 'ts-button-class',
			),
		);
		return $ts_settings_general;
	}

	/**
	 * Ticket solution admin menu page.
	 *
	 * @since    1.0.0
	 * @param array $ts_settings_template Settings fields.
	 */
	public function ts_admin_template_settings_page( $ts_settings_template ) {
		$ts_settings_template = array(
			array(
				'title' => __( 'Text Field Demo', 'ticket-solution' ),
				'type'  => 'text',
				'description'  => __( 'This is text field demo follow same structure for further use.', 'ticket-solution' ),
				'id'    => 'ts_text_demo',
				'value' => '',
				'class' => 'ts-text-class',
				'placeholder' => __( 'Text Demo', 'ticket-solution' ),
			),
			array(
				'title' => __( 'Number Field Demo', 'ticket-solution' ),
				'type'  => 'number',
				'description'  => __( 'This is number field demo follow same structure for further use.', 'ticket-solution' ),
				'id'    => 'ts_number_demo',
				'value' => '',
				'class' => 'ts-number-class',
				'placeholder' => '',
			),
			array(
				'title' => __( 'Password Field Demo', 'ticket-solution' ),
				'type'  => 'password',
				'description'  => __( 'This is password field demo follow same structure for further use.', 'ticket-solution' ),
				'id'    => 'ts_password_demo',
				'value' => '',
				'class' => 'ts-password-class',
				'placeholder' => '',
			),
			array(
				'title' => __( 'Textarea Field Demo', 'ticket-solution' ),
				'type'  => 'textarea',
				'description'  => __( 'This is textarea field demo follow same structure for further use.', 'ticket-solution' ),
				'id'    => 'ts_textarea_demo',
				'value' => '',
				'class' => 'ts-textarea-class',
				'rows' => '5',
				'cols' => '10',
				'placeholder' => __( 'Textarea Demo', 'ticket-solution' ),
			),
			array(
				'title' => __( 'Select Field Demo', 'ticket-solution' ),
				'type'  => 'select',
				'description'  => __( 'This is select field demo follow same structure for further use.', 'ticket-solution' ),
				'id'    => 'ts_select_demo',
				'value' => '',
				'class' => 'ts-select-class',
				'placeholder' => __( 'Select Demo', 'ticket-solution' ),
				'options' => array(
					'' => __( 'Select option', 'ticket-solution' ),
					'INR' => __( 'Rs.', 'ticket-solution' ),
					'USD' => __( '$', 'ticket-solution' ),
				),
			),
			array(
				'title' => __( 'Multiselect Field Demo', 'ticket-solution' ),
				'type'  => 'multiselect',
				'description'  => __( 'This is multiselect field demo follow same structure for further use.', 'ticket-solution' ),
				'id'    => 'ts_multiselect_demo',
				'value' => '',
				'class' => 'ts-multiselect-class mwb-defaut-multiselect',
				'placeholder' => '',
				'options' => array(
					'default' => __( 'Select currency code from options', 'ticket-solution' ),
					'INR' => __( 'Rs.', 'ticket-solution' ),
					'USD' => __( '$', 'ticket-solution' ),
				),
			),
			array(
				'title' => __( 'Checkbox Field Demo', 'ticket-solution' ),
				'type'  => 'checkbox',
				'description'  => __( 'This is checkbox field demo follow same structure for further use.', 'ticket-solution' ),
				'id'    => 'ts_checkbox_demo',
				'value' => '',
				'class' => 'ts-checkbox-class',
				'placeholder' => __( 'Checkbox Demo', 'ticket-solution' ),
			),

			array(
				'title' => __( 'Radio Field Demo', 'ticket-solution' ),
				'type'  => 'radio',
				'description'  => __( 'This is radio field demo follow same structure for further use.', 'ticket-solution' ),
				'id'    => 'ts_radio_demo',
				'value' => '',
				'class' => 'ts-radio-class',
				'placeholder' => __( 'Radio Demo', 'ticket-solution' ),
				'options' => array(
					'yes' => __( 'YES', 'ticket-solution' ),
					'no' => __( 'NO', 'ticket-solution' ),
				),
			),
			array(
				'title' => __( 'Enable', 'ticket-solution' ),
				'type'  => 'radio-switch',
				'description'  => __( 'This is switch field demo follow same structure for further use.', 'ticket-solution' ),
				'id'    => 'ts_radio_switch_demo',
				'value' => '',
				'class' => 'ts-radio-switch-class',
				'options' => array(
					'yes' => __( 'YES', 'ticket-solution' ),
					'no' => __( 'NO', 'ticket-solution' ),
				),
			),

			array(
				'type'  => 'button',
				'id'    => 'ts_button_demo',
				'button_text' => __( 'Button Demo', 'ticket-solution' ),
				'class' => 'ts-button-class',
			),
		);
		return $ts_settings_template;
	}

	/**
	* Ticket solution save tab settings.
	*
	* @since 1.0.0
	*/
	public function ts_admin_save_tab_settings() {
		global $ts_mwb_ts_obj;
		if ( isset( $_POST['ts_button_demo'] ) ) {
			$mwb_ts_gen_flag = false;
			$ts_genaral_settings = apply_filters( 'ts_general_settings_array', array() );
			$ts_button_index     = array_search( 'submit', array_column( $ts_genaral_settings, 'type' ) );
			if ( isset( $ts_button_index ) && ( null == $ts_button_index || '' == $ts_button_index ) ) {
				$ts_button_index = array_search( 'button', array_column( $ts_genaral_settings, 'type' ) );
			}
			if ( isset( $ts_button_index ) && '' !== $ts_button_index ) {
				unset( $ts_genaral_settings[$ts_button_index] );
				if ( is_array( $ts_genaral_settings ) && ! empty( $ts_genaral_settings ) ) {
					foreach ( $ts_genaral_settings as $ts_genaral_setting ) {
						if ( isset( $ts_genaral_setting['id'] ) && '' !== $ts_genaral_setting['id'] ) {
							if ( isset( $_POST[$ts_genaral_setting['id']] ) ) {
								update_option( $ts_genaral_setting['id'], $_POST[$ts_genaral_setting['id']] );
							} else {
								update_option( $ts_genaral_setting['id'], '' );
							}
						}else{
							$mwb_ts_gen_flag = true;
						}
					}
				}
				if ( $mwb_ts_gen_flag ) {
					$mwb_ts_error_text = esc_html__( 'Id of some field is missing', 'ticket-solution' );
					$ts_mwb_ts_obj->mwb_ts_plug_admin_notice( $mwb_ts_error_text, 'error' );
				}else{
					$mwb_ts_error_text = esc_html__( 'Settings saved !', 'ticket-solution' );
					$ts_mwb_ts_obj->mwb_ts_plug_admin_notice( $mwb_ts_error_text, 'success' );
				}
			}
		}
	}

	/**
	 * Register Global setting function
	 *
	 * @return void
	 */
	public function mwb_register_product_sale_ticket() {

		// Register a new setting for "sale_ticket" page.
		register_setting( 'sale_ticket', 'sale_ticket_options' );

		// Register a new section in the "sale_ticket" page.
		add_settings_section(
			'sale_ticket_section_developers',
			__( 'PRICE INCREASE ON PRODUCT DECREASE', 'ticket-solution' ),
			array( $this, 'sale_ticket_section_developers_callback' ),
			'sale_ticket'
		);
		// Register setting field for product selected to increase price
		add_settings_field(
			'selected_product_field',
			__( 'ALREADY SELECTED PRODUCT FOR PRICE INCREASE ON PRODUCT DECREASE', 'ticket-solution' ),
			array( $this, 'already_picked_product_callback' ),
			'sale_ticket',
			'sale_ticket_section_developers',
			array(
				'label_for'       => 'selected_product_field',
				'class'           => 'selected_product_field_row',
			)
		);
	}
	/**
	 * Developers section callback function.
	 *
	 * @param array $args  The settings array, defining title, id, callback.
	 */
	public function sale_ticket_section_developers_callback( $args ) {
		?>
		<p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'Product selected for price increase', 'ticket-solution' ); ?></p>
		<?php
	}
	/**
	 * Callback function for setting field.
	 *
	 * @param array $args comment.
	 * @return void
	 */
	public function already_picked_product_callback( $args ) {

		$mwb_args = array(
			'post_type'      => 'product',
			'posts_per_page' => -1,
			
		);
	
		$mwb_loop          = new WP_Query( $mwb_args );
		$mwb_procdt_insert = array();
		global $product;
		while ( $mwb_loop->have_posts() ) : 
			$mwb_loop->the_post();
			$mwb_prod_name                     = get_the_title();
			$mwb_prod_id                       = get_the_ID();
			$mwb_procdt_insert[ $mwb_prod_id ] = $mwb_prod_name;
		endwhile;

		foreach ( $mwb_procdt_insert as $mwb_product_id_key => $mwb_prod_name_value ) {
			$mwb_product_id_custom_panel_stock = get_post_meta( $mwb_product_id_key, 'custom_set_stock', true );
			$mwb_product_id_custom_panel_price = get_post_meta( $mwb_product_id_key, 'custom_set_price', true );
			
			if ( ! empty( $mwb_product_id_custom_panel_stock ) && ! empty( $mwb_product_id_custom_panel_price ) ) {
				$mwb_panel_data_has_id[ $mwb_product_id_key ] = $mwb_prod_name_value;
			}
		}
		?>

		<select id="<?php echo esc_attr( $args['label_for'] ); ?>"  class="my_custom_check_multiselect" multiple="multiple" name="sale_ticket_options[]" >
			<?php 
			if ( is_array( $mwb_panel_data_has_id ) && ! empty( $mwb_panel_data_has_id ) ) {

				foreach ( $mwb_panel_data_has_id as $mwb_panel_data_has_id_key => $mwb_panel_data_has_id_value ) {
					?>

					<option value="<?php echo esc_html( $mwb_panel_data_has_id_key ); ?>" <?php echo 'selected'; ?>>
					<?php 

						echo esc_html( $mwb_panel_data_has_id_value );
					?>
					</option>
					<?php
				}
			} else {
				?>
					<option value= '-1' <?php echo 'selected'; ?> ><?php esc_html_e( 'NONE', 'ticket-solution' ); ?></option>
					
					<?php
			} 
			?>
		</select>	
			<?php
	
	}
	/**
	 * Callback function for menu in sale_ticket global setting.
	 *
	 * @return void
	 */
	public function mwb_sale_ticket_options_menu() {
		add_menu_page(
			'MENU',
			'TICKET SOLUTION PLUGIN',
			'manage_options',
			'sale_ticket',
			array( $this, 'ticket_solution_page_html' )
		);
	}

	/**
	 * Callback function for saving global setting html.
	 *
	 * @return void
	 */
	public function ticket_solution_page_html() {
		// Check user capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {
				return;
		}
		// Check if the user have submitted the settings.
		if ( isset( $_GET['settings-updated'] ) ) {
			// Add settings saved message with the class of "updated".
			add_settings_error( 'sale_ticket_messages', 'sale_ticket_message', __( 'Settings Saved', 'ticket-solution' ), 'updated' );
		}
		// Show error/update messages.
		settings_errors( 'sale_ticket_messages' );
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<form action="options.php" method="post">
				<?php
				// output security fields for the registered setting "api".
				settings_fields( 'sale_ticket' );
				// (sections are registered for "api", each field is registered to a specific section)
				do_settings_sections( 'sale_ticket' );
				// output save settings button.Commented because not need to save this setting . for future refrences.
				//submit_button( 'Save Settings' );
				?>
			</form>
		</div>
		<?php

	}
	/**
	 * Callback function for custom tab in product panel.
	 *
	 * @param array $tabs comment.
	 * @return void
	 */
	public function custom_product_settings_tabs( $tabs ) {
	 
		$tabs['custom'] = array(
			'label'    => 'TICKET SOLUTION',
			'target'   => 'custom_product_data',
			'class'    => array( 'show_if_virtual', 'show_if_simple', 'show_if_variable', 'show_if_downloadable' ),
			'priority' => 21,
		);
		return $tabs; 
	}
	/**
	 * Callback function for adding custom tab in product edit page.
	 *
	 * @return void
	 */
	public function custom_product_panels() {
		$nonce = wp_create_nonce( 'nonce' );
 
		echo '<div id="custom_product_data" class="panel woocommerce_options_panel hidden">';
	 
		woocommerce_wp_text_input( array(
			'id'                => 'custom_set_stock',
			'value'             => get_post_meta( get_the_ID(), 'custom_set_stock', true ),
			'label'             => 'ENTER STOCK',
			'desc_tip'    		=> true,
			'description'       => 'Set number of stock at which price will increase'
		) );
	 
		woocommerce_wp_textarea_input( array(
			'id'          => 'custom_set_price',
			'value'       => get_post_meta( get_the_ID(), 'custom_set_price', true ),
			'label'       => 'ENTER PRICE',
			'description' => '<a href="#" class="sale_ticket_show">+</a>',
		) );
		// For nonce.
		woocommerce_wp_text_input( array(
			'type'        => 'hidden',
			'id'          => 'nonce',
			'value'       => $nonce,
		) );
		// Onclick button then it will appear
		woocommerce_wp_text_input( array(
			'id'                => 'custom_set_stock_2',
			'value'             => get_post_meta( get_the_ID(), 'custom_set_stock_2', true ),
			'label'             => 'ENTER STOCK',
			'desc_tip'    		=> true,
			'description'       => 'Set number of stock at which price will increase'
		) );

		woocommerce_wp_textarea_input( array(
			'id'          => 'custom_set_price_2',
			'value'       => get_post_meta( get_the_ID(), 'custom_set_price_2', true ),
			'label'       => 'ENTER PRICE',
			'description' => '<a href="#" class="sale_ticket_hide">Cancel</a>',
		) );
	 
		echo '</div>';
	 
	}
	/**
	 * Callback function for saving custom field.
	 *
	 * @param array $post_id comment.
	 * @return void
	 */
	public function save_custom_field_data( $post_id ) {
		if ( wp_verify_nonce(  $_POST[ 'nonce' ], 'nonce' ) ) {
			if ( isset( $_POST['custom_set_stock'] ) && isset( $_POST['custom_set_price'] ) ) {
				update_post_meta( $post_id, 'custom_set_stock', esc_attr( $_POST['custom_set_stock'] ) );
				update_post_meta( $post_id, 'custom_set_price', esc_attr( $_POST['custom_set_price'] ) );
			}
			if ( isset( $_POST['custom_set_stock'] ) && isset( $_POST['custom_set_price'] ) ) {
				update_post_meta( $post_id, 'custom_set_stock_2', esc_attr( $_POST['custom_set_stock_2'] ) );
				update_post_meta( $post_id, 'custom_set_price_2', esc_attr( $_POST['custom_set_price_2'] ) );
			}
		}
	}
}
