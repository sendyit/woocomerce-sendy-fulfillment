# Sendy Fulfillment WooCommerce Plugin
Contributors: Evanson, Lewis , Dervine
Tags: WooCommerce, ECommerce, Consignment, Delivery, Sendy , Fulfillment
Requires at least: 5.8.3
Tested up to: 6.0.3
Stable tag: 2.0.1
Requires PHP: 7.3.29
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Plugin Url : https://github.com/sendyit/woocomerce-sendy-fulfillment

## Introduction 
***(on Wordpress Plugins Store & API documentation)***

This WooCommerce extension uses the Sendy Fulfilment API  to allow you to automate the processes of consigning and delivering with Sendy Fulfilment. 
## Features
- **Collect geolocation**: this extension adds a geolocation field to your checkout page that uses the google address autocomplete API to collect geolocation info from your customers.
- **Request deliveries**: when your buyer completes a checkout on your site, the extension will automatically create a delivery request on the Sendy fulfilment app with the buyer and the product information. You will also get an email alert confirming that the order has been created on the Sendy Fulfilment app.
- **Track deliveries**: this extension will create a track delivery button on your order confirmation page which will allow your buyers to open and track the order in realtime.
- **Manage inventory**: feature will allow you to sync your product inventory between your woocommerce store and the Sendy Fulfilment app. 

## Installation
### Minimum requirements
- Wordpress 6.0.0
- Woocommerce 6.8.0

### Other prerequisites:
- You need to have a Sendy Fulfilment account. If you don’t have one, you can sign-up [here](https://fulfillment.sendyit.com/auth/sign-up)
- A Sendy Fulfilment API key. 
- The API key is a unique identifier for your business, you can get one by sending an email to merchantapi@sendyit.com. Please store this securely for the live environment as it's exposure might allow someone else to place requests with your account.

Once you’ve installed the plugin, open the Sendy Fulfilment app to ensure the  products are synced and then consign your items to the Sendy fulfilment centres

## Automatic Installation (Work in progress, currently not available)
- Search for “Sendy Fulfilment WooCommerce” from your Wordpress dashboard  under Plugins > Add new
- After installation has finished, click the ‘activate plugin’ link.

## Manual Installation via the Wordpress interface
- Download the plugin zip file to your computer from here
- Go to the WordPress admin panel menu Plugins > Add New
- Choose upload
- Upload the plugin zip file, the plugin will now be installed
- After installation has finished, click the ‘activate plugin’ link
- Go to the Sendy Fulfilment tab on the left and set the API, inventory and order configurations using the instruction guides on each
- Use the FAQ to incase you have any issues but you can also get support via email at merchantapi@sendyit.com

## Development
### Changelog
**1.0.0 (2022-08-25)**
- Sync your products from your woocommerce store to the Sendy Fulfillment app, also adds ***sendy_product_id*** to your product metadata
- Create a Fulfilment request on checkout
- Add a delivery tracking button to the order confirmation page

**2.0.0 (2022-09-20)**
- Cancel deliveries on sendy
- Create a sales channel for businesses
- Create a Fulfilment request on a sales channel
- Create pick up request if stock is low
- Save pick up address
- Add business email for error and success notifications

**2.0.1 (2022-11-01)**
- Use woocommerce default currency