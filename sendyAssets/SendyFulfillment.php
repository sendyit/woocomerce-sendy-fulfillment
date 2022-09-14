<?php
Class FulfillmentProduct {
    // Properties
    public $default_data;
    function __construct() {
        //product f(x) includes
        require_once ('product/AddProduct.php');
        require_once ('product/EditProduct.php');
        require_once ('product/UnlinkProduct.php');
        //order f(x) includes
        require_once ('order/PlaceOrder.php');
        require_once ('order/TrackOrder.php');
        //migrate f(x) includes
        require_once ('migrate/migrateAccount.php');
        //for staging default apiusername to B-IGY-3791 (universal username on staging)
        $this->default_data['apiKey'] = $this->get_apikey();
        $this->default_data['apiusername'] = $this->get_apiusername();
        $this->default_data['channel_id'] = $this->get_saleschannelid();
        $this->default_data['default_quantity'] = get_option('sendy_fulfillment_default_quantity', 1);
        $this->default_data['default_quantity_type'] = get_option('sendy_fulfillment_default_quantity_type', 'KILOGRAMS');
        $this->default_data['default_currency'] = get_option('sendy_fulfillment_default_currency', 'KES'); //defaulting to KES
        $this->default_data['environment'] = get_option('sendy_fulfillment_environment');
        $this->default_data['default_means_of_payment'] = 'MPESA'; //currently defaulting to mpesa
        $this->default_data['live_api_link'] = 'https://fulfillment-api.sendyit.com/v2';
        $this->default_data['staging_api_link'] = 'https://fulfillment-api-test.sendyit.com/v2';
        $this->default_data['live_tracking_link'] = 'https://buyer.sendyit.com';
        $this->default_data['staging_tracking_link'] = 'https://buyer-test.sendyit.com';
    }
    function get_apiusername() {
        if (get_option('sendy_fulfillment_environment') == 'Live') {
            return get_option('sendy_fulfillment_api_username_live');
        } else {
          return get_option('sendy_fulfillment_api_username_test');
        }
      }
  function get_apikey() {
      if (get_option('sendy_fulfillment_environment') == 'Live') {
          return get_option('sendy_fulfillment_api_key_live');
      } else {

          return get_option('sendy_fulfillment_api_key_test');
      }

    }
    function get_saleschannelid() {
      if (get_option('sendy_fulfillment_environment') == 'Live') {
          return get_option('sendy_fulfillment_sales_channel_id_live');
      } else {
          return get_option('sendy_fulfillment_sales_channel_id_test');
      }

    }
    function get_link($append) {
        if ($this->default_data['environment'] == 'Live') {
            return $this->default_data['live_api_link'] . '/' . $append;
        } else {
            return $this->default_data['staging_api_link'] . '/' . $append;
        }
    }
    function get_tracking_link($append) {
        if ($this->default_data['environment'] == 'Live') {
            return $this->default_data['live_tracking_link'] . '/' . $append;
        } else {
            return $this->default_data['staging_tracking_link'] . '/' . $append;
        }
    }
    function add_edit($data) {
        //check if adding or updating
        if ((isset($data['product_id'])) && (strlen($data['product_id']) > 2)) {
            //do an update
            return $this->edit($data);
        } else {
            // do an add
            return $this->add($data);
        }
    }
    public function add($data) {
        $url = $this->get_link('add-product');
        $product_details_url = $this->get_link('product-details');
        $response = Addproduct($this->default_data, $data, $url, $product_details_url);
        return $response;
    }
    public function edit($data) {
        $url = $this->get_link('edit-product');
        $response = EditProduct($this->default_data, $data, $url);
        return $response;
    }
    public function archive($data) {
        $url = $this->get_link('unlink-product');
        $response = UnlinkProduct($this->default_data, $data, $url);
        return $response;
    }
    function place_order($data) {
        $url = $this->get_link('create-fulfilment-request');
        $response = PlaceOrder($this->default_data, $data, $url);
        return $response;
    }
    function track_order($data) {
        $url = $this->get_link('track-order');
        $response = TrackOrder($this->default_data, $data, $url);
        return $response;
    }
    function migrate_account($data) {
        $url = $this->get_link('saleschannel-migrate');
        $response = migrateAccount($this->default_data, $data, $url);
        return $response;
    }
    function test_settings($data) {
        $includedStuff = get_included_files();
        echo '<pre>';
        echo '</pre>';
        add_option('sendy_apiKey', 'uTvdcS6TGU3DyvpfK2pWNh53W9vMrE');
        echo 'testing settings';
    }
}
