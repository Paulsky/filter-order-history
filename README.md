# OrderFinder - Filter Order History for WooCommerce

Enhances the WooCommerce order history page by adding advanced filtering capabilities and customizable column displays. This enables customers to easily search and filter their order history using multiple criteria while giving store owners full control over which information is displayed. Works particularly well with plugins like [WooCommerce Address Book](https://wordpress.org/plugins/woo-address-book/).

For more WordPress plugins, check out our products at [Wijnberg Developments](https://products.wijnberg.dev).

## Built with

- [WooCommerce](https://github.com/woocommerce/woocommerce)
- [WordPress](https://github.com/WordPress/WordPress)

## Requirements

- WooCommerce plugin installed and activated
- WordPress 6.0 or higher  
- PHP 7.4 or higher
- WooCommerce 7.0.0 or higher

## Key Features

### For Store Administrators
- **Column Management**: Select which order fields to display as columns from 25+ available options
- **Drag-and-Drop Ordering**: Intuitive interface to reorder columns in the orders table
- **Flexible Configuration**: Choose exactly what information customers can filter by
- **HPOS Compatibility**: Full support for WooCommerce High-Performance Order Storage

### For Customers  
- **Advanced Filtering**: Filter orders by status, dates, billing/shipping information, and payment method
- **Date Range Selection**: Filter by creation, modification, completion, and payment dates
- **Active Filter Display**: Clear indication of applied filters with easy removal
- **Intuitive Interface**: Dropdown selects and date pickers for user-friendly filtering

## Available Order Fields

The plugin provides access to comprehensive order information:

- **Order information** such as status, order number, currency, and key dates (created, modified, completed, paid)
- **Billing information** such as customer name, company, contact details, and complete address information
- **Shipping information** such as recipient name, company, phone number, and delivery address
- **Payment information** including payment method details

## Installation

To install the plugin, follow these steps:

1. Download the `.zip` file from the [releases page](https://github.com/wijnberg-developments/orderfinder-filter-order-history/releases).
2. In your WordPress admin dashboard, go to `Plugins` > `Add New`.
3. Click `Upload Plugin` at the top of the page.
4. Click `Choose File`, select the `.zip` file you downloaded, then click `Install Now`.
5. After installation, click `Activate Plugin`.

The plugin is now ready for use.

## Getting started

These instructions will guide you through the installation and basic setup of the OrderFinder plugin.

### Configuration

Once activated, you can customize the plugin through the admin interface:

#### Column Selection
1. Go to `WooCommerce` > `Settings` > `Filter Order History`
2. Select which order fields customers can use to filter their orders
3. Choose from 25+ available order fields including billing, shipping, and order information
4. Save your changes

#### Column Ordering  
1. Navigate to `WooCommerce` > `Settings` > `Filter Order History` > `Columns Ordering`
2. Use the drag-and-drop interface to reorder table columns as they will appear in the customer's order history
3. Only selected columns from the main settings will be displayed
4. Changes take effect immediately on the frontend

### Usage

After configuration, navigate to the My Account > Orders page on your website. You will see:

- **Filter Form**: Appears above the orders table with selected filter options
- **Dropdown Filters**: For categorical fields like order status and payment method
- **Date Range Filters**: Date pickers for filtering by various order dates  
- **Active Filters**: Display of currently applied filters with clear removal options
- **Custom Columns**: Additional order information columns based on your configuration

## Language support

Currently supported languages:
- English

If you would like to add support for a new language or improve existing translations, please let us know by opening an issue or contacting us through our website.

## Contributing

Your contributions are welcome! If you'd like to contribute to the project, feel free to fork the repository, make your changes, and submit a pull request.

## Development and deployment

To prepare your development work for submission, ensure you have `npm` installed and run `npm run build`. This command compiles the assets and prepares the plugin for deployment.

### Steps:

1. Ensure `npm` is installed.
2. Navigate to the project root.
3. Run `npm run build`.

The compiled files are now ready for use. Please ensure your changes adhere to the project's coding standards.

## Security

If you discover any security related issues, please email us instead of using the issue tracker.

## License

This plugin is licensed under the GNU General Public License v2 or later.

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License, version 2, as published by the Free Software Foundation.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.