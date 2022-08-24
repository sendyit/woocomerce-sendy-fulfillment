<?php
/**
 * Fired during plugin activation.
 */
class Sendy_Fulfillment_Activator {

	public static function activate() {

		//set the default settings
		add_option('sendy_fulfillment_environment', 'Test');
		add_option('sendy_fulfillment_default_currency', 'KES');
		add_option('sendy_fulfillment_default_quantity_type', 'KILOGRAM');
		add_option('sendy_fulfillment_default_quantity', '1');
		add_option('sendy_fulfillment_sync_products_on_add', '1');
		add_option('sendy_fulfillment_place_order_on_fulfillment', '1');
		add_option('sendy_fulfillment_include_tracking', '1');
		add_option('sendy_fulfillment_include_collect_amount', '0');

	}

}
