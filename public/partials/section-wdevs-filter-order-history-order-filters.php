<?php

/**
 * Order filters form template
 *
 * This file is used to markup the order filtering form.
 *
 * @link       https://products.wijnberg.dev
 * @since      1.0.0
 *
 * @package    Wdevs_Filter_Order_History
 * @subpackage Wdevs_Filter_Order_History/public/partials
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( empty( $filter_fields ) || ! is_array( $filter_fields ) ) {
	return;
}

?>

<div class="wdevs-foh-order-filters">
    <form method="get" class="">
		<?php
		// Add hidden fields (nonce and preserved URL parameters)
		foreach ( $hidden_fields as $hidden_field ) {
			echo '<input type="hidden" name="' . esc_attr( $hidden_field['name'] ) . '" value="' . esc_attr( $hidden_field['value'] ) . '" />';
		}
		?>

        <div >
			<?php
			$counter = 0;
			foreach ($filter_fields as $field_key => $field_config) :
				$counter++;
				$row_class = ($counter % 2 === 1) ? 'form-row-first' : 'form-row-last';
				?>
                <div class="form-row <?php echo $row_class; ?>">
                    <label for="wdevs_foh_filter_<?php echo esc_attr($field_key); ?>">
						<?php echo esc_html($field_config['label']); ?>
                    </label>

					<?php if ($field_config['type'] === 'select') : ?>
                        <select name="wdevs_foh_filter_<?php echo esc_attr($field_key); ?>" id="wdevs_foh_filter_<?php echo esc_attr($field_key); ?>" class="select">
                            <option value=""><?php esc_html_e('All', 'filter-order-history-for-woocommerce'); ?></option>
							<?php foreach ($field_config['options'] as $option_value => $option_label) : ?>
                                <option value="<?php echo esc_attr($option_value); ?>" <?php selected($current_filters[$field_key] ?? '', $option_value); ?>>
									<?php echo esc_html($option_label); ?>
                                </option>
							<?php endforeach; ?>
                        </select>

					<?php elseif ($field_config['type'] === 'date_range') : ?>
                        <div class="form-row-first">
                            <input
                                    type="date"
                                    name="wdevs_foh_filter_<?php echo esc_attr($field_key); ?>_from"
                                    id="wdevs_foh_filter_<?php echo esc_attr($field_key); ?>_from"
                                    class="input-text"
                                    placeholder="<?php esc_attr_e('From', 'filter-order-history-for-woocommerce'); ?>"
                                    value="<?php echo esc_attr($current_filters[$field_key . '_from'] ?? ''); ?>"
                            />
                        </div>
                        <div class="form-row-last">
                            <input
                                    type="date"
                                    name="wdevs_foh_filter_<?php echo esc_attr($field_key); ?>_to"
                                    id="wdevs_foh_filter_<?php echo esc_attr($field_key); ?>_to"
                                    class="input-text"
                                    placeholder="<?php esc_attr_e('To', 'filter-order-history-for-woocommerce'); ?>"
                                    value="<?php echo esc_attr($current_filters[$field_key . '_to'] ?? ''); ?>"
                            />
                        </div>
					<?php endif; ?>
                </div>
			<?php endforeach; ?>
        </div>

        <div class="form-row form-row-wide">
            <button type="submit" class="button">
				<?php esc_html_e('Filter orders', 'filter-order-history-for-woocommerce'); ?>
            </button>

			<?php if (!empty(array_filter($current_filters))) : ?>
                <a href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>" class="button">
					<?php esc_html_e('Clear filters', 'filter-order-history-for-woocommerce'); ?>
                </a>
			<?php endif; ?>
        </div>
    </form>

	<?php if ( ! empty( array_filter( $current_filters ) ) ) : ?>
        <div class="wdevs-foh-active-filters">
            <div class="wdevs-foh-active-filters-title"><?php esc_html_e( 'Active filters:', 'filter-order-history-for-woocommerce' ); ?></div>
            <ul>
				<?php foreach ( array_filter( $current_filters ) as $filter_key => $filter_value ) : ?>
					<?php if ( isset( $filter_fields[ str_replace( array( '_from', '_to' ), '', $filter_key ) ] ) ) : ?>
                        <li>
                            <strong><?php echo esc_html( $filter_fields[ str_replace( array( '_from', '_to' ), '', $filter_key ) ]['label'] ); ?>:</strong>
							<?php echo esc_html( $filter_value ); ?>
                        </li>
					<?php endif; ?>
				<?php endforeach; ?>
            </ul>
        </div>
	<?php endif; ?>
</div>