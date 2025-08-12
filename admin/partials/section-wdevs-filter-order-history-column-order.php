<?php

/**
 * Provide a admin area view for the column order settings
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://products.wijnberg.dev
 * @since      1.0.0
 *
 * @package    Wdevs_Filter_Order_History
 * @subpackage Wdevs_Filter_Order_History/admin/partials
 */

// Prevent direct access
if ( ! defined( 'WPINC' ) ) {
	exit;
}

?>

<h2><?php esc_html_e( 'Columns ordering', 'filter-order-history-for-woocommerce' ); ?></h2>
<p class="wc-shipping-zone-heading-help-text"><?php esc_html_e( 'Drag and drop to reorder columns. Only selected columns from the main settings will be shown in the customer order table.', 'filter-order-history-for-woocommerce' ); ?></p>

<table class="wc-shipping-zones widefat" cellspacing="0" id="wdevs-foh-column-order-table">
	<thead>
		<tr>
            <th class="wc-shipping-zone-sort"><?php echo wc_help_tip( esc_html__( 'Drag and drop to re-order your custom zones. This is the order in which they will be matched against the customer address.', 'filter-order-history-for-woocommerce' ) ); ?></th>
			<th class="wc-shipping-zone-name"><?php esc_html_e( 'Column name', 'filter-order-history-for-woocommerce' ); ?></th>
		</tr>
	</thead>
	<tbody class="wc-shipping-zone-rows">
		<?php foreach ( $all_columns as $column_id => $column_data ) : ?>
			<tr data-column-id="<?php echo esc_attr( $column_id ); ?>">
                <td width="1%" class="wc-shipping-zone-sort" style="width: 1%;"></td>
				<td class="wc-shipping-zone-name">
					<?php echo esc_html( $column_data['label'] ); ?>
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>