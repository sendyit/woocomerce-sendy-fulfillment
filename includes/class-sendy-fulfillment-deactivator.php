<?php
/**
 * Fired during plugin deactivation.
 */
class Sendy_Fulfillment_Deactivator {

	public static function deactivate() {

		//remove options

		delete_option('sendy_fulfillment_environment');
		delete_option('sendy_fulfillment_default_currency');
		delete_option('sendy_fulfillment_default_quantity_type');
		delete_option('sendy_fulfillment_default_quantity');
		delete_option('sendy_fulfillment_sync_products_on_add');
		delete_option('sendy_fulfillment_place_order_on_fulfillment');
		delete_option('sendy_fulfillment_include_tracking');
		delete_option('sendy_fulfillment_include_collect_amount');

		delete_option('sendy_fulfillment_api_key');
		delete_option('sendy_fulfillment_api_username');
		delete_option('sendy_fulfillment_biz_name');
		delete_option('sendy_fulfillment_delivery_info');

	}

}
