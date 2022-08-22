<?php
 
/*
 
Plugin Name: Sendy Fulfillment Plugin
 
Plugin URI: https://gitlab.com/sendy/sendy-fulfillement-woocomerce-plugin
 
Description: Plugin to allow automation of consignment and delivery with Sendy Fulfillment.
 
Version: 1.0.0
 
Author: Merchant Engineers
 
License: GPLv2 or later
 
Text Domain: sendy-fulfillment
 
*/

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

define('SENDY_FULFILLMENT_WOOCOMMERCE_VERSION', '1.0.0');

function activate_sendy_fulfillment()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-sendy-fulfillment-activator.php';
    Sendy_Fuilfilment_Activator::activate();
}

function deactivate_sendy_fulfillment()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-sendy-fulfillment-deactivator.php';
    Sendy_Fulfillment_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_sendy_fulfillment');
register_deactivation_hook(__FILE__, 'deactivate_sendy_fulfillment');