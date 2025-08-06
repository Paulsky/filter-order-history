<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://products.wijnberg.dev
 * @since      1.0.0
 *
 * @package    Wdevs_Filter_Order_History
 * @subpackage Wdevs_Filter_Order_History/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wdevs_Filter_Order_History
 * @subpackage Wdevs_Filter_Order_History/admin
 * @author     Wijnberg Developments <contact@wijnberg.dev>
 */
class Wdevs_Filter_Order_History_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		$page = isset( $_GET['page'] ) ? sanitize_text_field( $_GET['page'] ) : '';
		$tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : '';

		if ( $page === 'wc-settings' && $tab === 'wdevs_foh' ) {
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wdevs-filter-order-history-admin.js', array('jquery', 'jquery-ui-sortable', 'jquery-blockui'), $this->version, false );
			
			wp_localize_script( $this->plugin_name, 'wdevs_foh_admin', array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce' => wp_create_nonce( Wdevs_Filter_Order_History_Woocommerce::AJAX_NONCE_ACTION ),
				'update_order_action' => Wdevs_Filter_Order_History_Woocommerce::AJAX_ACTION_UPDATE_ORDER
			) );
		}

	}

	/**
	 * @since 1.0.0
	 */
	public function add_action_links( $actions ) {
		$links = array(
			'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=wdevs_foh' ) . '">' . __( 'Settings' ) . '</a>', //Yes, just use WordPress text domain
		);

		$actions = array_merge( $actions, $links );

		return $actions;
	}

}
