<?php

/**
 * Filter Order History Filter Manager Class
 *
 * Handles all frontend order filtering functionality.
 *
 * @link       https://products.wijnberg.dev
 * @since      1.0.0
 *
 * @package    Wdevs_Filter_Order_History
 * @subpackage Wdevs_Filter_Order_History/includes
 */

/**
 * Filter Order History Filter Manager Class
 *
 * Manages the frontend order filtering functionality including form rendering,
 * filter configuration, and query modification.
 *
 * @package    Wdevs_Filter_Order_History
 * @subpackage Wdevs_Filter_Order_History/includes
 * @author     Wijnberg Developments <contact@wijnberg.dev>
 */
class Wdevs_Filter_Order_History_Filter_Manager {

	/**
	 * Nonce action for filter form security.
	 */
	const FILTER_NONCE_ACTION = 'wdevs_foh_filter_action';

	/**
	 * Nonce field name for filter form.
	 */
	const FILTER_NONCE_NAME = 'wdevs_foh_filter_nonce';

	/**
	 * Filter Order History helper trait for shared functionality.
	 */
	use Wdevs_Filter_Order_History_Helper_Trait;

	/**
	 * Render order filters form before account orders table.
	 *
	 * @param bool $has_orders Whether the customer has orders.
	 *
	 * @since 1.0.0
	 */
	public function render_order_filters( $has_orders ) {
		// Show filters if customer has orders OR if filtering is currently active
		// This allows customers to modify/clear filters even when no results are found
		if ( ! $has_orders && ! $this->is_filtering() ) {
			return;
		}

		$selected_fields = $this->get_selected_columns();
		if ( empty( $selected_fields ) ) {
			return;
		}

		$filter_fields   = $this->get_filter_fields_config();
		$current_filters = $this->get_active_filter_values();
		$hidden_fields   = $this->get_hidden_fields();

		// Include the filter form template
		include plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/section-wdevs-filter-order-history-order-filters.php';
	}

	/**
	 * Get hidden form fields for preserving URL parameters.
	 *
	 * @return array Array of hidden field data with name and value keys.
	 * @since 1.0.0
	 */
	private function get_hidden_fields() {
		$hidden_fields = array();
		
		// Add nonce field
		$hidden_fields[] = array(
			'name'  => self::FILTER_NONCE_NAME,
			'value' => wp_create_nonce( self::FILTER_NONCE_ACTION )
		);

		// Preserve existing URL parameters (like pagination)
		foreach ( $_GET as $key => $value ) {
			// Skip our own filter parameters and nonce
			if ( strpos( $key, 'wdevs_foh_filter_' ) === 0 || 
				 $key === 'orders-search' || 
				 $key === self::FILTER_NONCE_NAME ) {
				continue;
			}

			$hidden_fields[] = array(
				'name'  => sanitize_key( $key ),
				'value' => sanitize_text_field( $value )
			);
		}

		return $hidden_fields;
	}

	/**
	 * Get filter fields configuration based on selected columns.
	 *
	 * @return array Array of filter field configurations.
	 * @since 1.0.0
	 */
	private function get_filter_fields_config() {
		$filter_fields = $this->get_filter_field_types();
		$this->populate_select_options( $filter_fields );

		return $filter_fields;
	}

	/**
	 * Get filter field types and base configuration.
	 *
	 * @return array Array of filter field configurations with types.
	 * @since 1.0.0
	 */
	private function get_filter_field_types() {
		$selected_fields  = $this->get_selected_columns();
		$available_fields = $this->get_order_fields();
		$filter_fields    = array();

		foreach ( $selected_fields as $field_name ) {
			if ( ! isset( $available_fields[ $field_name ] ) ) {
				continue;
			}

			$field_config = array(
				'label' => $available_fields[ $field_name ],
				'type'  => $this->get_field_filter_type( $field_name ),
			);

			// Initialize empty options array for select fields
			if ( $field_config['type'] === 'select' ) {
				$field_config['options'] = array();
			}

			$filter_fields[ $field_name ] = $field_config;
		}

		return $filter_fields;
	}

	/**
	 * Populate select options for select-type filter fields.
	 *
	 * @param array &$filter_fields Reference to filter fields array to populate.
	 * @since 1.0.0
	 */
	private function populate_select_options( &$filter_fields ) {
		// Get all select fields that need options
		$select_fields = array();
		foreach ( $filter_fields as $field_name => $field_config ) {
			if ( $field_config['type'] === 'select' ) {
				$select_fields[] = $field_name;
			}
		}

		if ( empty( $select_fields ) ) {
			return;
		}

		// Get customer orders to extract unique values
		$customer_id = get_current_user_id();
		$orders      = wc_get_orders( array(
			'customer' => $customer_id,
			'limit'    => -1,
			'return'   => 'objects',
		) );

		$unique_values = array_fill_keys( $select_fields, array() );

		// Extract unique values from orders
		foreach ( $orders as $order ) {
			foreach ( $select_fields as $field ) {
				$method = 'get_' . $field;
				if ( method_exists( $order, $method ) ) {
					$value = $order->$method();
				} else {
					$value = $order->get_meta( $field );
				}

				if ( ! empty( $value ) ) {
					$unique_values[ $field ][ $value ] = $value;
				}
			}
		}

		// Populate options in filter fields with translations
		foreach ( $select_fields as $field ) {
			if ( isset( $unique_values[ $field ] ) ) {
				$options = array();
				foreach ( $unique_values[ $field ] as $value ) {
					$options[ $value ] = $this->translate_option_value( $field, $value );
				}
				$filter_fields[ $field ]['options'] = $options;
			}
		}
	}

	/**
	 * Determine the appropriate filter type for a field.
	 *
	 * @param string $field_name Internal field name.
	 *
	 * @return string Filter type (select, text, date, date_range).
	 * @since 1.0.0
	 */
	private function get_field_filter_type( $field_name ) {
		$date_fields = $this->get_date_fields();

		if ( in_array( $field_name, $date_fields, true ) ) {
			return 'date_range';
		}

		return 'select';
	}

	/**
	 * Get active filter values from URL parameters.
	 *
	 * @return array Array of active filter values.
	 * @since 1.0.0
	 */
	private function get_active_filter_values() {
		$filters = array();

		foreach ( $_GET as $key => $value ) {
			if ( strpos( $key, 'wdevs_foh_filter_' ) === 0 ) {
				$filter_key = str_replace( 'wdevs_foh_filter_', '', $key );
				
				// Sanitize the value based on context
				if ( is_array( $value ) ) {
					$filters[ $filter_key ] = array_map( 'sanitize_text_field', $value );
				} else {
					$filters[ $filter_key ] = sanitize_text_field( $value );
				}
			}
		}

		return $filters;
	}

	/**
	 * Translate option value for display using WooCommerce functions when available.
	 *
	 * @param string $field_name The field name to determine translation method.
	 * @param mixed  $value      The value to translate.
	 * @return string Translated value or original value if no translation available.
	 * @since 1.0.0
	 */
	private function translate_option_value( $field_name, $value ) {
		// Handle different field types with appropriate WooCommerce translations
		switch ( $field_name ) {
			case 'status':
				// Use WooCommerce status name function
				if ( function_exists( 'wc_get_order_status_name' ) ) {
					$translated = wc_get_order_status_name( $value );
					return ! empty( $translated ) ? $translated : $value;
				}
				break;
				
			case 'currency':
				// Use WooCommerce currency functions for display
				if ( function_exists( 'get_woocommerce_currency_symbol' ) ) {
					$symbol = get_woocommerce_currency_symbol( $value );
					return $value . ' (' . $symbol . ')';
				}
				break;
				
			case 'payment_method':
				// Use WooCommerce payment gateway title
				if ( function_exists( 'WC' ) && WC()->payment_gateways() ) {
					$gateways = WC()->payment_gateways()->get_available_payment_gateways();
					if ( isset( $gateways[ $value ] ) ) {
						return $gateways[ $value ]->get_title();
					}
				}
				break;
		}

		// Fallback: try WordPress translation
		$translated = __( $value, 'filter-order-history-for-woocommerce' );
		
		// If translation is the same as original, return original (no translation found)
		return ( $translated !== $value ) ? $translated : $value;
	}

	/**
	 * Check if filtering is currently active.
	 *
	 * @return bool True if filtering is active, false otherwise.
	 * @since 1.0.0
	 */
	public function is_filtering() {
		// Check for filter nonce presence (indicates form was submitted)
		if ( ! empty( $_GET[ self::FILTER_NONCE_NAME ] ) ) {
			return true;
		}

		// Check for any filter parameters with non-empty values
		$filters = $this->get_active_filter_values();

		if ( empty( $filters ) ) {
			return false;
		}

		// Check if at least one filter has a meaningful value
		foreach ( $filters as $filter_value ) {
			if ( ! empty( $filter_value ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Filter customer orders query based on URL parameters.
	 *
	 * @param array $query_args WooCommerce orders query arguments.
	 *
	 * @return array Modified query arguments.
	 * @since 1.0.0
	 */
	public function filter_my_account_orders_query( $query_args ) {
		$filters = $this->get_active_filter_values();

		if ( empty( $filters ) ) {
			return $query_args;
		}

		// Require valid nonce when filters are present (security)
		if ( empty( $_GET[ self::FILTER_NONCE_NAME ] ) || 
			 ! wp_verify_nonce(
				sanitize_text_field( wp_unslash( $_GET[ self::FILTER_NONCE_NAME ] ) ),
				self::FILTER_NONCE_ACTION
			) ) {
			return $query_args; // No filtering without valid nonce
		}

		$date_fields = $this->get_date_fields();
		$date_ranges = [];

		// First pass: collect all date ranges
		foreach ( $date_fields as $date_field ) {
			$date_ranges[$date_field] = [
				'from' => $filters[$date_field . '_from'] ?? '',
				'to' => $filters[$date_field . '_to'] ?? ''
			];
		}

		// Second pass: handle other filters
		foreach ( $filters as $filter_key => $filter_value ) {
			if ( empty( $filter_value ) ) {
				continue;
			}

			// Skip date fields as we handle them separately
			$is_date_field = false;
			foreach ( $date_fields as $date_field ) {
				if ( strpos( $filter_key, $date_field ) === 0 ) {
					$is_date_field = true;
					break;
				}
			}
			if ( $is_date_field ) {
				continue;
			}

			// Handle other filters (HPOS compatible) with field validation
			$allowed_fields = $this->get_selected_columns();
			if ( in_array( $filter_key, $allowed_fields, true ) ) {
				$query_args[ $filter_key ] = $filter_value;
			}
		}

		// Third pass: apply date queries
		foreach ( $date_ranges as $date_field => $range ) {
			if ( ! empty( $range['from'] )) {
				if ( ! empty( $range['to'] ) ) {
					$query_args[$date_field] = $range['from'] . '...' . $range['to'];
				} else {
					$query_args[$date_field] = '>=' . $range['from'];
				}
			} elseif ( ! empty( $range['to'] ) ) {
				$query_args[$date_field] = '<=' . $range['to'];
			}
		}

		return $query_args;
	}

	protected function get_date_fields(): array {
		$date_fields = array( 'date_created', 'date_modified', 'date_completed', 'date_paid' );

		return $date_fields;
	}
}