<?php

/**
 * Filter Order History Helper Trait
 *
 * Shared functionality for column and field management across admin and public areas.
 *
 * @link       https://products.wijnberg.dev
 * @since      1.0.0
 *
 * @package    Wdevs_Filter_Order_History
 * @subpackage Wdevs_Filter_Order_History/includes
 */

/**
 * Filter Order History Helper Trait
 *
 * Provides shared methods for managing WooCommerce order columns and fields.
 * Used by both admin (column ordering) and public (filtering) functionality.
 *
 * @package    Wdevs_Filter_Order_History
 * @subpackage Wdevs_Filter_Order_History/includes
 * @author     Wijnberg Developments <contact@wijnberg.dev>
 */
trait Wdevs_Filter_Order_History_Helper_Trait {

	/**
	 * Get available order fields for filtering.
	 *
	 * @return array Array of order fields with labels.
	 * @since 1.0.0
	 */
	public function get_order_fields() {
		$fields = array(
			'status'   => __( 'Order Status', 'filter-order-history-for-woocommerce' ),
			'number'   => __( 'Order Number', 'filter-order-history-for-woocommerce' ),

			// Additional order fields
			'currency' => __( 'Currency', 'filter-order-history-for-woocommerce' ),

			'date_created'         => __( 'Date created', 'filter-order-history-for-woocommerce' ),
			'date_modified'        => __( 'Date modified', 'filter-order-history-for-woocommerce' ),
			'date_completed'       => __( 'Date completed', 'filter-order-history-for-woocommerce' ),
			'date_paid'            => __( 'Date paid', 'filter-order-history-for-woocommerce' ),

			// Billing address fields
			'billing_first_name'   => __( 'Billing First Name', 'filter-order-history-for-woocommerce' ),
			'billing_last_name'    => __( 'Billing Last Name', 'filter-order-history-for-woocommerce' ),
			'billing_company'      => __( 'Billing Company', 'filter-order-history-for-woocommerce' ),
			'billing_address_1'    => __( 'Billing Address 1', 'filter-order-history-for-woocommerce' ),
			'billing_address_2'    => __( 'Billing Address 2', 'filter-order-history-for-woocommerce' ),
			'billing_city'         => __( 'Billing City', 'filter-order-history-for-woocommerce' ),
			'billing_state'        => __( 'Billing State', 'filter-order-history-for-woocommerce' ),
			'billing_postcode'     => __( 'Billing Postcode', 'filter-order-history-for-woocommerce' ),
			'billing_country'      => __( 'Billing Country', 'filter-order-history-for-woocommerce' ),
			'billing_email'        => __( 'Billing Email', 'filter-order-history-for-woocommerce' ),
			'billing_phone'        => __( 'Billing Phone', 'filter-order-history-for-woocommerce' ),

			// Shipping address fields
			'shipping_first_name'  => __( 'Shipping First Name', 'filter-order-history-for-woocommerce' ),
			'shipping_last_name'   => __( 'Shipping Last Name', 'filter-order-history-for-woocommerce' ),
			'shipping_company'     => __( 'Shipping Company', 'filter-order-history-for-woocommerce' ),
			'shipping_address_1'   => __( 'Shipping Address 1', 'filter-order-history-for-woocommerce' ),
			'shipping_address_2'   => __( 'Shipping Address 2', 'filter-order-history-for-woocommerce' ),
			'shipping_city'        => __( 'Shipping City', 'filter-order-history-for-woocommerce' ),
			'shipping_state'       => __( 'Shipping State', 'filter-order-history-for-woocommerce' ),
			'shipping_postcode'    => __( 'Shipping Postcode', 'filter-order-history-for-woocommerce' ),
			'shipping_country'     => __( 'Shipping Country', 'filter-order-history-for-woocommerce' ),
			'shipping_phone'       => __( 'Shipping Phone', 'filter-order-history-for-woocommerce' ),

			// Payment fields
			'payment_method_title' => __( 'Payment Method', 'filter-order-history-for-woocommerce' ),
		);

		return apply_filters( 'wdevs_foh_order_fields', $fields );
	}

	/**
	 * Get selected columns with ordering information.
	 *
	 * @return array Array of selected columns with order information.
	 * @since 1.0.0
	 */
	protected function get_selected_columns() {
		return get_option( 'wdevs_foh_selected_fields', array() );
	}

	/**
	 * Get standard WooCommerce "My Account > Orders" columns.
	 *
	 * @param array $excluded Array of column IDs to exclude.
	 *
	 * @return array Array of standard columns with labels.
	 * @since 1.0.0
	 */
	public function get_standard_woocommerce_columns( $excluded = array( 'order-actions' ) ) {
		$filter_removed = false;
		if ( has_filter( 'woocommerce_account_orders_columns', [ $this, 'add_account_orders_columns' ] ) ) {
			remove_filter( 'woocommerce_account_orders_columns', [ $this, 'add_account_orders_columns' ], 10 );
			$filter_removed = true;
			$excluded       = array();
		}

		$columns = wc_get_account_orders_columns();

		if ( $filter_removed ) {
			add_filter( 'woocommerce_account_orders_columns', [ $this, 'add_account_orders_columns' ], 10, 1 );
		}

		// Remove excluded columns
		if ( ! empty( $excluded ) ) {
			foreach ( $excluded as $column_id ) {
				unset( $columns[ $column_id ] );
			}
		}

		return apply_filters( 'wdevs_foh_standard_columns', $columns, $excluded );
	}

	/**
	 * Convert column ID to internal field name.
	 *
	 * @param string $column_id WooCommerce column ID.
	 *
	 * @return string Internal field name.
	 * @since 1.0.0
	 */
	protected function column_id_to_field_name( $column_id ) {
		$standard_mappings = array(
			'order-date'   => 'date_created',
//			'order-status' => 'status',
//			'order-number' => 'order_number',
//			'order-total'  => 'total'
		);

		if ( isset( $standard_mappings[ $column_id ] ) ) {
			return $standard_mappings[ $column_id ];
		}

		$field_name = str_replace( 'order-', '', $column_id );

		return str_replace( '-', '_', $field_name );
	}

	/**
	 * Convert internal field name to column ID.
	 *
	 * @param string $field_name Internal field name.
	 *
	 * @return string WooCommerce column ID.
	 * @since 1.0.0
	 */
	protected function field_name_to_column_id( $field_name ) {
		$reverse_mappings = array(
			'date_created' => 'order-date',
			'status'       => 'order-status',
			'total'        => 'order-total',
			'number'       => 'order-number',
		);

		if ( isset( $reverse_mappings[ $field_name ] ) ) {
			return $reverse_mappings[ $field_name ];
		}

		return 'order-' . str_replace( '_', '-', $field_name );
	}

	/**
	 * Map external column IDs to internal field names.
	 *
	 * @param array $columns Array with column IDs as keys.
	 *
	 * @return array Array with internal field names as keys.
	 * @since 1.0.0
	 */
	protected function map_columns_to_internal( $columns ) {
		$mapped = array();
		foreach ( $columns as $column_id => $label ) {
			$field_name            = $this->column_id_to_field_name( $column_id );
			$mapped[ $field_name ] = $label;
		}

		return $mapped;
	}

	/**
	 * Map internal field names to external column IDs.
	 *
	 * @param array $fields Array with field names as keys.
	 *
	 * @return array Array with column IDs as keys.
	 * @since 1.0.0
	 */
	protected function map_fields_to_external( $fields ) {
		$mapped = array();
		foreach ( $fields as $field_name => $label ) {
			$column_id            = $this->field_name_to_column_id( $field_name );
			$mapped[ $column_id ] = $label;
		}

		return $mapped;
	}

	/**
	 * Get selected account orders columns with proper ordering logic.
	 *
	 * @return array Array of selected columns with labels and order information.
	 * @since 1.0.0
	 */
	protected function selected_account_orders_columns() {
		$standard_columns = $this->get_standard_woocommerce_columns();
		$selected_fields  = $this->get_selected_columns(); // These are internal field names
		$column_order     = get_option( 'wdevs_foh_column_order', array() );
		$available_fields = $this->get_order_fields();
		$all_columns      = [];

		// Add standard columns
		foreach ( $standard_columns as $column_id => $label ) {
			$all_columns[ $column_id ] = [
				'label' => $label,
				'order' => PHP_INT_MAX
			];
		}

		// Convert selected fields (internal names) to external column IDs and add them
		if ( ! empty( $selected_fields ) ) {
			// Filter selected fields to only include available ones
			$filtered_fields = array_intersect_key( $available_fields, array_flip( $selected_fields ) );

			// Map internal field names to external column IDs
			$selected_columns = $this->map_fields_to_external( $filtered_fields );

			foreach ( $selected_columns as $column_id => $label ) {
				if ( ! isset( $all_columns[ $column_id ] ) ) {
					$all_columns[ $column_id ] = [
						'label' => $label,
						'order' => PHP_INT_MAX
					];
				}
			}
		}

		// Apply saved column order or assign default order
		if ( ! empty( $column_order ) ) {
			$explicitly_ordered = [];
			$unordered          = [];
			$max_order          = max( $column_order );

			// Separate columns that have explicit order from those that don't
			foreach ( $all_columns as $column_id => $column_data ) {
				if ( isset( $column_order[ $column_id ] ) ) {
					$explicitly_ordered[ $column_id ]          = $column_data;
					$explicitly_ordered[ $column_id ]['order'] = $column_order[ $column_id ];
				} else {
					$unordered[ $column_id ]          = $column_data;
					$unordered[ $column_id ]['order'] = ++ $max_order;
				}
			}

			$all_columns = array_merge( $explicitly_ordered, $unordered );
		} else {
			// No saved order, assign sequential order
			$order = 1;
			foreach ( $all_columns as $column_id => &$column_data ) {
				$column_data['order'] = $order ++;
			}
		}

		// Sort by order
		uasort( $all_columns, fn( $a, $b ) => $a['order'] <=> $b['order'] );

		// Always move order-actions to the end if it exists
		if ( isset( $all_columns['order-actions'] ) ) {
			$actions_column = $all_columns['order-actions'];
			unset( $all_columns['order-actions'] );
			$all_columns['order-actions'] = $actions_column;
		}

		return $all_columns;
	}
}