=== OrderFinder - Filter Order History for WooCommerce ===
Contributors: wijnbergdevelopments
Tags: woocommerce, orders, filter, history, customer, account, search, columns
Requires at least: 6.0
Tested up to: 6.8
Stable tag: 1.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Let customers filter and search their order history with advanced options and customizable columns.

== Description ==

OrderFinder - Filter Order History for WooCommerce enhances your WooCommerce store by allowing customers to filter and search their order history using multiple criteria. This plugin adds customizable column displays and an intuitive filtering interface to the standard WooCommerce "My Account > Orders" page.

=== Key features ===

* Customizable column selection and ordering
* Advanced order filtering with multiple field support
* Date range filtering with intuitive interface
* Active filter display with clear removal options
* Drag-and-drop admin interface for column management

For more information about this plugin, please visit the [plugin page](https://products.wijnberg.dev/).

=== Requirements ===

* WooCommerce plugin installed and activated

=== Filterable order fields ===

The plugin supports filtering by different order fields:

* **Order information** such as status, order number, currency, and key dates (created, modified, completed, paid)
* **Billing information** such as customer name, company, contact details, and complete address information
* **Shipping information** such as recipient name, company, phone number, and delivery address
* **Payment information** including payment method details

=== Configuration ===

Configure the plugin settings below for proper functionality.

= Plugin settings =

Configure these plugin-specific settings:

1. **Column Selection**
   - Go to: *WooCommerce > Settings > Filter Order History*
   - Select which order fields customers can use to filter their orders
   - Choose from 25+ available order fields

2. **Column Ordering**
   - Go to: *WooCommerce > Settings > Filter Order History > Columns Ordering*
   - Drag and drop columns to reorder them as they will appear in the customer's order history table
   - Only selected columns from the main settings will be shown

=== Usage ===

After installation and configuration, customers will see enhanced functionality on the **My Account > Orders** page:

1. **Filtering Interface**: A filtering form appears above the orders table
2. **Dropdown Filters**: Easy selection for categorical fields like status and payment method
3. **Date Range Filters**: Date pickers for filtering by order dates
4. **Active Filters**: Clear display of applied filters with removal options
5. **Custom Columns**: Additional order information columns as configured by admin

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/wdevs-filter-order-history` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Use the WooCommerce > Settings > Filter Order History screen to configure the plugin.

== Frequently Asked Questions ==

= Will this work with my theme? =

Yes, the plugin uses WooCommerce's standard styling and hooks, making it compatible with most themes that properly support WooCommerce.

= Can I customize which fields customers can filter by? =

Absolutely! Go to WooCommerce > Settings > Filter Order History to select which order fields should be available for filtering.

= Can I change the order of columns in the orders table? =

Yes, use the drag-and-drop interface in WooCommerce > Settings > Filter Order History > Columns Ordering to reorder columns.

== Screenshots ==

1. Enhanced order history page with filtering interface and custom columns

== Changelog ==

= 1.0.0 =
* Initial release

== Additional Information ==

This plugin is fully open source. You can find the source code on [GitHub](https://github.com/Paulsky/filter-order-history)

For more WordPress and WooCommerce plugins, visit [Wijnberg Developments](https://products.wijnberg.dev/product-category/wordpress/plugins/).