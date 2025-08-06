<?php

/**
 * Fired during plugin activation
 *
 * @link       https://products.wijnberg.dev
 * @since      1.0.0
 *
 * @package    Wdevs_Filter_Order_History
 * @subpackage Wdevs_Filter_Order_History/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Wdevs_Filter_Order_History
 * @subpackage Wdevs_Filter_Order_History/includes
 * @author     Wijnberg Developments <contact@wijnberg.dev>
 */
class Wdevs_Filter_Order_History_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			deactivate_plugins( plugin_basename( __FILE__ ) );

			wp_die( esc_html__( 'This plugin requires WooCommerce. Please install and activate WooCommerce before activating this plugin.', 'filter-order-history-for-woocommerce' ) );
		}
	}

}
