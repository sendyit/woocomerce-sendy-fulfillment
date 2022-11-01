<?php

/*

Plugin Name: Sendy Fulfillment

Plugin URI: https://gitlab.com/sendy/sendy-fulfillement-woocomerce-plugin

Description: Plugin to allow automation of consignment and delivery with Sendy Fulfillment.

Version: 2.0.1

Author: Sendy Engineering

License: GPLv2 or later

Text Domain: sendy-fulfillment

*/

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

define('SENDY_FULFILLMENT_WOOCOMMERCE_VERSION', '2.0.1');

function activate_sendy_fulfillment()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-sendy-fulfillment-activator.php';
    Sendy_Fulfillment_Activator::activate();
}

function deactivate_sendy_fulfillment()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-sendy-fulfillment-deactivator.php';
    Sendy_Fulfillment_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_sendy_fulfillment');
register_deactivation_hook(__FILE__, 'deactivate_sendy_fulfillment');

require plugin_dir_path( __FILE__ ) . 'includes/class-sendy-fulfillment.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-sendy-fulfillment-settings.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-sendy-fulfillment-inventory.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-sendy-fulfillment-location.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-sendy-fulfillment-tracking.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-sendy-fulfillment-orders.php';


/**
 * This function goes last
 */
function run_sendy_fulfillment()
{

    $plugin = new Sendy_Fulfillment();
    $plugin->run();

}

run_sendy_fulfillment();
