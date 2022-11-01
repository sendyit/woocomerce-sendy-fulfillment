<?php
/**
 * Fired during plugin deactivation.
 */
class Sendy_Fulfillment_Deactivator {

	public static function deactivate() {

		//remove options

		delete_option('sendy_fulfillment_environment');
		delete_option('sendy_fulfillment_default_quantity_type');
		delete_option('sendy_fulfillment_default_quantity');
		delete_option('sendy_fulfillment_sync_products_on_add');
		delete_option('sendy_fulfillment_place_order_on_fulfillment');
		delete_option('sendy_fulfillment_include_tracking');
		delete_option('sendy_fulfillment_include_collect_amount');

		delete_option('sendy_fulfillment_api_key');

		delete_option('sendy_fulfillment_biz_name');
		delete_option('sendy_fulfillment_biz_email');
		delete_option('sendy_fulfillment_delivery_info');

		delete_option('sendy_fulfillment_api_username_live');
		delete_option('sendy_fulfillment_api_username_test');

		delete_option('sendy_fulfillment_api_key_live');
		delete_option('sendy_fulfillment_api_key_test');

		delete_option('sendy_fulfillment_sync_all_products');
		delete_option('sendy_fulfillment_channel_id');
		delete_option('sendy_fulfillment_api_username');


		delete_option('sendy_fulfillment_sales_channel_id_test');
		delete_option('sendy_fulfillment_sales_channel_id_live');

		delete_option('sendy_fulfillment_pickup_address_name_alt');
		delete_option('sendy_fulfillment_pickup_address_name');
		delete_option('sendy_fulfillment_pickup_address_lat');
		delete_option('sendy_fulfillment_pickup_address_long');
		





	}

}
