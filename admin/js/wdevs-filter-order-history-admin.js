( function ( $ ) {
	'use strict';

	$( function () {
		if ( $( '#wdevs-foh-column-order-table' ).length ) {
			initColumnOrderSortable();
		}
	} );

	function initColumnOrderSortable() {
		$( '#wdevs-foh-column-order-table tbody' ).sortable( {
			items: 'tr',
			handle: '.wc-shipping-zone-sort',
			cursor: 'move',
			axis: 'y',
			scrollSensitivity: 40,
			helper: 'clone',
			opacity: 0.65,
			update: function ( event, ui ) {
				saveColumnOrder();
			},
		} );
	}

	function saveColumnOrder() {
		var $table = $( '#wdevs-foh-column-order-table' );
		var columnOrder = [];
		var orderIndex = 1;

		$table.find( 'tbody tr' ).each( function () {
			var columnId = $( this ).data( 'column-id' );
			if ( columnId ) {
				columnOrder.push( {
					column_id: columnId,
					order: orderIndex++,
				} );
			}
		} );

		// Block the table during AJAX request
		blockTable( $table );

		$.ajax( {
			url: wdevs_foh_admin.ajax_url,
			type: 'POST',
			data: {
				action: wdevs_foh_admin.update_order_action,
				nonce: wdevs_foh_admin.nonce,
				column_order: columnOrder,
			},
			success: function ( response ) {
				if ( response.success ) {
					// Optionally show success message
					console.log( 'Column order updated successfully' );
				} else {
					console.error(
						'Error updating column order:',
						response.data
					);
				}
			},
			error: function ( xhr, status, error ) {
				console.error( 'AJAX error:', error );
			},
			complete: function () {
				// Always unblock the table when request completes
				unblockTable( $table );
			},
		} );
	}

	function blockTable( $element ) {
		$element.block( {
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6,
			},
		} );
	}

	function unblockTable( $element ) {
		$element.unblock();
	}
} )( jQuery );
