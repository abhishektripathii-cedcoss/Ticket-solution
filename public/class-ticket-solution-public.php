<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    Ticket_Solution
 * @subpackage Ticket_Solution/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 * namespace ticket_solution_public.
 *
 * @package    Ticket_Solution
 * @subpackage Ticket_Solution/public
 * @author     makewebbetter <webmaster@makewebbetter.com>
 */
class Ticket_Solution_Public {

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
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function ts_public_enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, TICKET_SOLUTION_DIR_URL . 'public/src/scss/ticket-solution-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function ts_public_enqueue_scripts() {

		wp_register_script( $this->plugin_name, TICKET_SOLUTION_DIR_URL . 'public/src/js/ticket-solution-public.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name, 'ts_public_param', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
		wp_enqueue_script( $this->plugin_name );

	}
	/**
	 * Callback function for stock price updation on stock reduced.
	 *
	 * @return void
	 */
	public function stock_updated() {

		$mwb_args = array(
			'post_type'      => 'product',
			'posts_per_page' => -1,
			
		);
	
		$mwb_loop          = new WP_Query( $mwb_args );
		$mwb_procdt_insert = array();
		global $product;
		while ( $mwb_loop->have_posts() ) : 
			$mwb_loop->the_post();

			$mwb_prod_id         = get_the_ID();
			$mwb_procdt_insert[] = $mwb_prod_id;
		endwhile;

		foreach ( $mwb_procdt_insert as $mwb_product_id ) {
			
			$mwb_product        = new WC_Product( $mwb_product_id );
			$mwb_stock_quantity = $mwb_product->get_stock_quantity();
			$mwb_stock_quantity = strval( $mwb_stock_quantity );


			$mwb_product_id_custom_panel_stock = get_post_meta( $mwb_product_id, 'custom_set_stock', true );
			$mwb_product_id_custom_panel_price = get_post_meta( $mwb_product_id, 'custom_set_price', true );

			$mwb_product_id_custom_panel_stock_2 = get_post_meta( $mwb_product_id, 'custom_set_stock_2', true );
			$mwb_product_id_custom_panel_price_2 = get_post_meta( $mwb_product_id, 'custom_set_price_2', true );
			if ( ! empty( $mwb_product_id_custom_panel_stock ) && ! empty( $mwb_product_id_custom_panel_price ) ) {
				if ( $mwb_stock_quantity === $mwb_product_id_custom_panel_stock ) {
					$mwb_new_price = $mwb_product->set_regular_price( $mwb_product_id_custom_panel_price );
					$mwb_product->save();
				}
			}
			if ( ! empty( $mwb_product_id_custom_panel_stock_2 ) && ! empty( $mwb_product_id_custom_panel_price_2 ) ) {
				if ( $mwb_stock_quantity === $mwb_product_id_custom_panel_stock_2 ) {
					$mwb_new_price_2 = $mwb_product->set_regular_price( $mwb_product_id_custom_panel_price_2 );
					$mwb_product->save();
				}
			}
		}

	}

}
