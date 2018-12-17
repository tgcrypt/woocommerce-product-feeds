<?php

class WoocommerceGpfMulticurrency {

	public function run() {
		// Bail if no currency forced.
		if ( empty( $_GET['currency'] ) ) {
			return;
		}
		add_filter( 'woocommerce_gpf_feed_item', array( $this, 'add_currency_arg_to_product_permalinks' ), 10, 2 );
	}

	/**
	 * @param $feed_item
	 * @param $wc_product
	 *
	 * @return mixed
	 */
	public function add_currency_arg_to_product_permalinks( $feed_item, $wc_product ) {
		$feed_item->purchase_link = add_query_arg(
			array(
				'currency' => $_GET['currency'],
			),
			$feed_item->purchase_link
		);

		return $feed_item;
	}
}
