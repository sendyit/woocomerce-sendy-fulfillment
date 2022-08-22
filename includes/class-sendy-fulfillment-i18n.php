<?php
/**
 * Define the internationalization functionality
 */
class Sendy_Fulfillment_i18n {

	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'sendy-fulfillment',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
