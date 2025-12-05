<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://products.wijnberg.dev
 * @since             1.0.0
 * @package           Wdevs_Filter_Order_History
 *
 * @wordpress-plugin
 * Plugin Name:       OrderFinder - Filter Order History for WooCommerce
 * Plugin URI:        https://products.wijnberg.dev
 * Description:       Let customers filter and search their order history with advanced options and customizable columns.
 * Version:           1.0.1
 * Author:            Wijnberg Developments
 * Author URI:        https://products.wijnberg.dev/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       filter-order-history-for-woocommerce
 * Domain Path:       /languages
 * Requires at least:    6.0
 * Tested up to:         6.9
 * Requires PHP:         7.4
 * WC requires at least: 7.0.0
 * WC tested up to:      10.3.6
 * Requires Plugins:     woocommerce
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WDEVS_FILTER_ORDER_HISTORY_VERSION', '1.0.1' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wdevs-filter-order-history-activator.php
 */
function wdevs_filter_order_history_activate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wdevs-filter-order-history-activator.php';
	Wdevs_Filter_Order_History_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wdevs-filter-order-history-deactivator.php
 */
function wdevs_filter_order_history_deactivate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wdevs-filter-order-history-deactivator.php';
	Wdevs_Filter_Order_History_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'wdevs_filter_order_history_activate' );
register_deactivation_hook( __FILE__, 'wdevs_filter_order_history_deactivate' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wdevs-filter-order-history.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wdevs_filter_order_history() {

	$plugin = new Wdevs_Filter_Order_History();
	$plugin->run();

}

run_wdevs_filter_order_history();
