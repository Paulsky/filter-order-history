<?php

/**
 * The WooCommerce functionality of the plugin.
 *
 * @link       https://products.wijnberg.dev
 * @since      1.0.0
 *
 * @package    Wdevs_Filter_Order_History
 * @subpackage Wdevs_Filter_Order_History/includes
 */

/**
 * The WooCommerce functionality of the plugin.
 *
 * Defines the plugin name, version, and hooks for WooCommerce functionality.
 * This class is responsible for registering and rendering the WooCommerce settings.
 *
 * @package    Wdevs_Filter_Order_History
 * @subpackage Wdevs_Filter_Order_History/includes
 * @author     Wijnberg Developments <contact@wijnberg.dev>
 */
class Wdevs_Filter_Order_History_Woocommerce {

	/**
	 * Filter Order History helper trait for shared functionality.
	 */
	use Wdevs_Filter_Order_History_Helper_Trait;

	/**
	 * AJAX nonce action for column order updates.
	 *
	 * @since    1.0.0
	 * @var      string
	 */
	const AJAX_NONCE_ACTION = 'wdevs_foh_column_order_nonce';

	const AJAX_ACTION_UPDATE_ORDER = 'update_columns_order';


	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * The current settings section.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $current_section The current settings section.
	 */
	private $current_section;

	/**
	 * The filter manager instance.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      Wdevs_Filter_Order_History_Filter_Manager $filter_manager The filter manager instance.
	 */
	private $filter_manager;


	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		// Initialize filter manager
		$this->filter_manager = new Wdevs_Filter_Order_History_Filter_Manager();

		$this->current_section = isset( $_GET['section'] ) ? sanitize_text_field( $_GET['section'] ) : '';

		$page = isset( $_GET['page'] ) ? sanitize_text_field( $_GET['page'] ) : '';
		$tab  = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : '';

		if ( is_admin() && $page === 'wc-settings' && $tab === 'wdevs_foh') {
			$this->handle_sections();
		}
	}

	/**
	 * Declare WooCommerce compatibility
	 *
	 * @since 1.0.0
	 */
	public function declare_compatibility() {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', 'filter-order-history-for-woocommerce/wdevs-filter-order-history.php', true );
		}
	}

	/**
	 * Add settings tab to WooCommerce settings.
	 *
	 * @param array $settings_tabs Array of WooCommerce setting tabs.
	 *
	 * @return   array    $settings_tabs    Array of WooCommerce setting tabs.
	 * @since    1.0.0
	 */
	public function add_settings_tab( $settings_tabs ) {
		$settings_tabs['wdevs_foh'] = __( 'Filter Order History', 'filter-order-history-for-woocommerce' );

		return $settings_tabs;
	}

	/**
	 * Get WooCommerce settings for the Filter Order History tab.
	 *
	 * @return   array    $settings    Array of settings.
	 * @since    1.0.0
	 */
	public function get_settings() {
		$settings = array(
			array(
				'name' => __( 'Order filters settings', 'filter-order-history-for-woocommerce' ),
				'type' => 'title',
				'desc' => __( 'Select which columns customers can use to filter their orders.', 'filter-order-history-for-woocommerce' ),
				'id'   => 'wdevs_foh_section_title'
			),
			array(
				'name'     => __( 'Columns', 'filter-order-history-for-woocommerce' ),
				'type'     => 'multiselect',
				'desc'     => __( 'Select which order fields customers can use to filter their orders.', 'filter-order-history-for-woocommerce' ),
				'id'       => 'wdevs_foh_selected_fields',
				'options'  => $this->get_order_fields(),
				//'default'  => array(  ),
				'class'    => 'wc-enhanced-select',
				//	'css'      => 'min-width: 350px;',
				'desc_tip' => true,
			),
			array(
				'type' => 'sectionend',
				'id'   => 'wdevs_foh_section_end'
			)
		);

		return apply_filters( 'wdevs_foh_settings', $settings );
	}

	/**
	 * Output the WooCommerce settings tab content.
	 *
	 * @since    1.0.0
	 */
	public function settings_tab() {
		if ( $this->current_section === 'column_order' ) {
			$GLOBALS['hide_save_button'] = true;
			$all_columns = $this->selected_account_orders_columns();

			include plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/section-wdevs-filter-order-history-column-order.php';
		}else{
			woocommerce_admin_fields( $this->get_settings() );
		}
	}

	/**
	 * Update WooCommerce settings.
	 *
	 * @since    1.0.0
	 */
	public function update_settings() {
		woocommerce_update_options( $this->get_settings() );
	}

	/**
	 * Output footer info
	 *
	 * @since    1.0.0
	 */
	public function render_footer_info() {
		$text = sprintf(
		/* translators: %s: Link to author site. */
			__( ' OrderFinder - Filter Order History for WooCommerce is developed by %s. Your trusted WordPress & WooCommerce plugin partner from the Netherlands.', 'filter-order-history-for-woocommerce' ),
			'<a href="https://products.wijnberg.dev" target="_blank" rel="noopener">Wijnberg Developments</a>'
		);

		echo '<span style="padding: 0 30px; background: #f0f0f1; display: block;">' . wp_kses_post( $text ) . '</span>';
	}

	/**
	 * Handle sections for the settings tab.
	 *
	 * @since    1.0.0
	 */
	private function handle_sections() {
		add_action( 'woocommerce_sections_wdevs_foh', array( $this, 'output_sections' ) );

		if ( ! empty( $this->current_section ) ) {
			add_action( 'woocommerce_update_options_wdevs_foh_' . $this->current_section, array(
				$this,
				'update_settings'
			) );
		}else{
			add_action( 'woocommerce_update_options_wdevs_foh', array( $this, 'update_settings' ) );
		}
	}

	/**
	 * Output sections navigation.
	 *
	 * @since    1.0.0
	 */
	public function output_sections() {
		$sections = $this->get_sections();

		$documentationURL = 'https://products.wijnberg.dev/product/wordpress/plugins/filter-order-history/';

		echo '<ul class="subsubsub">';

		foreach ( $sections as $id => $label ) {
			$url       = admin_url( 'admin.php?page=wc-settings&tab=wdevs_foh&section=' . sanitize_title( $id ) );
			$class     = ( $this->current_section === $id ? 'current' : '' );
			$separator = '|';
			$text      = esc_html( $label );
			echo "<li><a href='$url' class='$class'>$text</a> $separator </li>";
		}

		?>

        <li>
            <a href="<?php echo esc_attr( $documentationURL ); ?>" target="_blank">
				<?php esc_html_e( 'Documentation', 'filter-order-history-for-woocommerce' ); ?>
                <svg style="width: 0.8rem; height: 0.8rem; stroke: currentColor; fill: none;"
                     xmlns="http://www.w3.org/2000/svg"
                     stroke-width="10" stroke-dashoffset="0"
                     stroke-dasharray="0" stroke-linecap="round"
                     stroke-linejoin="round" viewBox="0 0 100 100">
                    <polyline fill="none" points="40 20 20 20 20 90 80 90 80 60"/>
                    <polyline fill="none" points="60 10 90 10 90 40"/>
                    <line fill="none" x1="89" y1="11" x2="50" y2="50"/>
                </svg>
            </a>
        </li>

		<?php

		echo '</ul><br class="clear" />';
	}

	/**
	 * Get available sections for the WooCommerce settings tab.
	 *
	 * @return array Array of sections.
	 * @since    1.0.0
	 */
	private function get_sections() {
		return array(
			''             => __( 'Settings', 'filter-order-history-for-woocommerce' ),
			'column_order' => __( 'Columns ordering', 'filter-order-history-for-woocommerce' ),
		);
	}


	/**
	 * Add custom columns to account orders table.
	 *
	 * @param array $columns Existing columns.
	 *
	 * @return array Modified columns.
	 * @since 1.0.0
	 */
	public function add_account_orders_columns( $columns ) {
		if (! Wdevs_Filter_Order_History_Filter_Manager::is_enabled() ) {
            return $columns;
		}

		$selected_columns_data = $this->selected_account_orders_columns();
		$new_columns = array();

		foreach ( $selected_columns_data as $column_id => $column_data ) {
			$new_columns[ $column_id ] = $column_data['label'];
		}

		return $new_columns;
	}

	/**
	 * Render custom column content.
	 *
	 * @param WC_Order $order The order object.
	 * @param string $column_id The column ID.
	 *
	 * @since 1.0.0
	 */
	public function render_custom_column_content( $order, $column_id ) {
		$field_name = $this->column_id_to_field_name( $column_id );

		switch ( $field_name ) {
			case 'currency':
				echo esc_html( $order->get_currency() );
				break;

			case 'billing_first_name':
				echo esc_html( $order->get_billing_first_name() );
				break;

			case 'billing_last_name':
				echo esc_html( $order->get_billing_last_name() );
				break;

			case 'billing_company':
				echo esc_html( $order->get_billing_company() );
				break;

			case 'billing_address_1':
				echo esc_html( $order->get_billing_address_1() );
				break;

			case 'billing_address_2':
				echo esc_html( $order->get_billing_address_2() );
				break;

			case 'billing_city':
				echo esc_html( $order->get_billing_city() );
				break;

			case 'billing_state':
				echo esc_html( $order->get_billing_state() );
				break;

			case 'billing_postcode':
				echo esc_html( $order->get_billing_postcode() );
				break;

			case 'billing_country':
				echo esc_html( $order->get_billing_country() );
				break;

			case 'billing_email':
				echo esc_html( $order->get_billing_email() );
				break;

			case 'billing_phone':
				echo esc_html( $order->get_billing_phone() );
				break;

			case 'shipping_first_name':
				echo esc_html( $order->get_shipping_first_name() );
				break;

			case 'shipping_last_name':
				echo esc_html( $order->get_shipping_last_name() );
				break;

			case 'shipping_company':
				echo esc_html( $order->get_shipping_company() );
				break;

			case 'shipping_address_1':
				echo esc_html( $order->get_shipping_address_1() );
				break;

			case 'shipping_address_2':
				echo esc_html( $order->get_shipping_address_2() );
				break;

			case 'shipping_city':
				echo esc_html( $order->get_shipping_city() );
				break;

			case 'shipping_state':
				echo esc_html( $order->get_shipping_state() );
				break;

			case 'shipping_postcode':
				echo esc_html( $order->get_shipping_postcode() );
				break;

			case 'shipping_country':
				echo esc_html( $order->get_shipping_country() );
				break;

			case 'shipping_phone':
				echo esc_html( $order->get_shipping_phone() );
				break;

			case 'payment_method_title':
				echo esc_html( $order->get_payment_method_title() );
				break;

			default:
				echo '';
				break;
		}
	}

	/**
	 * Register column action hooks dynamically.
	 *
	 * @since 1.0.0
	 */
	public function register_column_hooks() {
		$selected_columns = $this->get_selected_columns();

		if ( empty( $selected_columns ) ) {
			return;
		}

		$existing_columns = $this->get_standard_woocommerce_columns();

		foreach ( $selected_columns as $key => $field ) {
			$column_id = $this->field_name_to_column_id( $field );

			if ( ! isset( $existing_columns[ $column_id ] ) ) {
				add_action( 'woocommerce_my_account_my_orders_column_' . $column_id, function ( $order ) use ( $column_id ) {
					if (! Wdevs_Filter_Order_History_Filter_Manager::is_enabled() ) {
						return;
					}

					$this->render_custom_column_content( $order, $column_id );
				}, 10, 1 );
			}
		}
	}

	/**
	 * Handle AJAX column order update.
	 *
	 * @since 1.0.0
	 */
	public function update_columns_order_action() {
		if ( ! isset( $_POST['column_order'] ) ) {
			wp_send_json_error( 'missing_fields' );
			wp_die();
		}

		if ( ! wp_verify_nonce( wp_unslash( $_POST['nonce'] ), self::AJAX_NONCE_ACTION ) ) {
			wp_send_json_error( 'bad_nonce' );
			wp_die();
		}

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_send_json_error( 'missing_capabilities' );
			wp_die();
		}

		$column_order_data = wp_unslash( $_POST['column_order'] );

		if ( ! is_array( $column_order_data ) ) {
			wp_send_json_error( 'invalid_data' );
			wp_die();
		}
        
		if ( count( $column_order_data ) > 50 ) { // Prevent too large arrays
			wp_send_json_error( 'too_many_items' );
			wp_die();
		}

		$new_order = array();

		foreach ( $column_order_data as $item ) {
			if ( ! is_array( $item ) || ! isset( $item['column_id'] ) || ! isset( $item['order'] ) ) {
				wp_send_json_error( 'invalid_item_structure' );
				wp_die();
			}

			$column_id = sanitize_text_field( $item['column_id'] );
			$order = absint( $item['order'] );

			if ( ! $this->is_valid_column_id( $column_id ) ) {
				wp_send_json_error( 'invalid_column_id' );
				wp_die();
			}

			$new_order[ $column_id ] = $order;
		}

		update_option( 'wdevs_foh_column_order', $new_order );
		wp_send_json_success( __( 'Column order updated.', 'filter-order-history-for-woocommerce' ) );
	}

	/**
	 * Validate column ID format.
	 *
	 * @param string $column_id Column ID to validate.
	 *
	 * @return bool True if valid, false otherwise.
	 * @since 1.0.0
	 */
	private function is_valid_column_id( $column_id ) {
		if ( empty( $column_id ) || ! is_string( $column_id ) ) {
			return false;
		}

		// Allow standard WooCommerce column IDs and our custom format
		// Pattern: order-{field-name} where field-name contains only letters, numbers, hyphens, underscores
		if ( preg_match( '/^order-[a-zA-Z0-9_-]+$/', $column_id ) ) {
			return true;
		}

		$allowed_standard = array( 'order-number', 'order-date', 'order-status', 'order-total', 'order-actions' );

		return in_array( $column_id, $allowed_standard, true );
	}

	/**
	 * Render order filters form before account orders table.
	 *
	 * @param bool $has_orders Whether the customer has orders.
	 *
	 * @since 1.0.0
	 */
	public function render_order_filters( $has_orders ) {
		if (! Wdevs_Filter_Order_History_Filter_Manager::is_enabled() ) {
			return;
		}

		return $this->filter_manager->render_order_filters( $has_orders );
	}

	/**
	 * Get filter fields configuration based on selected columns.
	 *
	 * @return array Array of filter field configurations.
	 * @since 1.0.0
	 */
	private function get_filter_fields_config() {
		$selected_fields = $this->get_selected_columns();
		$available_fields = $this->get_order_fields();
		$filter_fields = array();

		foreach ( $selected_fields as $field_name ) {
			if ( ! isset( $available_fields[ $field_name ] ) ) {
				continue;
			}

			$field_config = array(
				'label' => $available_fields[ $field_name ],
				'type'  => $this->get_field_filter_type( $field_name ),
			);

			if ( $field_config['type'] === 'select' ) {
				$field_config['options'] = $this->get_field_options( $field_name );
			}

			if ( $field_config['type'] === 'text' ) {
				$field_config['placeholder'] = sprintf(
				/* translators: %s: Field label */
					__( 'Search %s...', 'filter-order-history-for-woocommerce' ),
					$available_fields[ $field_name ]
				);
			}

			$filter_fields[ $field_name ] = $field_config;
		}

		return $filter_fields;
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
		$select_fields = array( 'status', 'payment_method_title', 'currency' );
		$date_fields = array( 'date_created' );

		if ( in_array( $field_name, $select_fields, true ) ) {
			return 'select';
		}

		if ( in_array( $field_name, $date_fields, true ) ) {
			return 'date_range';
		}

		return 'text';
	}

	/**
	 * Get options for select field filters.
	 *
	 * @param string $field_name Internal field name.
	 *
	 * @return array Array of options for select field.
	 * @since 1.0.0
	 */
	private function get_field_options( $field_name ) {
		switch ( $field_name ) {
			case 'status':
				return $this->get_customer_order_statuses();

			case 'payment_method_title':
				return $this->get_customer_payment_methods();

			case 'currency':
				return $this->get_customer_currencies();

			default:
				return array();
		}
	}

	/**
	 * Get unique order statuses from customer's orders.
	 *
	 * @return array Array of status options.
	 * @since 1.0.0
	 */
	private function get_customer_order_statuses() {
		$customer_id = get_current_user_id();
		if ( ! $customer_id ) {
			return array();
		}

		$orders = wc_get_orders( array(
			'customer' => $customer_id,
			'limit'    => -1,
			'return'   => 'ids',
		) );

		$statuses = array();
		foreach ( $orders as $order_id ) {
			$order = wc_get_order( $order_id );
			if ( $order ) {
				$status = $order->get_status();
				$statuses[ $status ] = wc_get_order_status_name( $status );
			}
		}

		return $statuses;
	}

	/**
	 * Get unique payment methods from customer's orders.
	 *
	 * @return array Array of payment method options.
	 * @since 1.0.0
	 */
	private function get_customer_payment_methods() {
		$customer_id = get_current_user_id();
		if ( ! $customer_id ) {
			return array();
		}

		$orders = wc_get_orders( array(
			'customer' => $customer_id,
			'limit'    => -1,
		) );

		$payment_methods = array();
		foreach ( $orders as $order ) {
			$method_title = $order->get_payment_method_title();
			if ( $method_title ) {
				$payment_methods[ $method_title ] = $method_title;
			}
		}

		return $payment_methods;
	}

	/**
	 * Get unique currencies from customer's orders.
	 *
	 * @return array Array of currency options.
	 * @since 1.0.0
	 */
	private function get_customer_currencies() {
		$customer_id = get_current_user_id();
		if ( ! $customer_id ) {
			return array();
		}

		$orders = wc_get_orders( array(
			'customer' => $customer_id,
			'limit'    => -1,
		) );

		$currencies = array();
		foreach ( $orders as $order ) {
			$currency = $order->get_currency();
			if ( $currency ) {
				$currencies[ $currency ] = $currency;
			}
		}

		return $currencies;
	}

	/**
	 * Get current filter values from URL parameters.
	 *
	 * @return array Array of current filter values.
	 * @since 1.0.0
	 */
	private function get_current_filters() {
		$filters = array();

		foreach ( $_GET as $key => $value ) {
			if ( strpos( $key, 'wdevs_filter_' ) === 0 ) {
				$filter_key = str_replace( 'wdevs_filter_', '', $key );
				$filters[ $filter_key ] = sanitize_text_field( $value );
			}
		}

		return $filters;
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
		if (! Wdevs_Filter_Order_History_Filter_Manager::is_enabled() ) {
			return $query_args;
		}

		return $this->filter_manager->filter_my_account_orders_query( $query_args );
	}

	/**
	 * Start hiding the default "no orders" message if filtering is active.
	 *
	 * @param bool $has_orders Whether the customer has orders.
	 *
	 * @since 1.0.0
	 */
	public function maybe_hide_no_orders_message_start( $has_orders ) {
		if (! Wdevs_Filter_Order_History_Filter_Manager::is_enabled() ) {
			return;
		}

		// Only hide the message if there are no orders but filtering is active
		if ( ! $has_orders && $this->filter_manager->is_filtering() ) {
			echo '<div class="wdevs-foh-hide-no-orders" style="display: none;">';
		}
	}

	/**
	 * End hiding the default "no orders" message if filtering is active.
	 *
	 * @param bool $has_orders Whether the customer has orders.
	 *
	 * @since 1.0.0
	 */
	public function maybe_hide_no_orders_message_end( $has_orders ) {
		if (! Wdevs_Filter_Order_History_Filter_Manager::is_enabled() ) {
			return;
		}

		// Only close the wrapper if there are no orders but filtering is active
		if ( ! $has_orders && $this->filter_manager->is_filtering() ) {
			echo '</div>';
			wc_print_notice( esc_html__( 'No results for the selected filters.', 'woocommerce' ), 'notice' ); // phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingHookComment
		}
	}

}