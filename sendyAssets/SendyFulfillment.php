<?php
Class SendyFulfillmentProduct {
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
        require_once ('order/CancelOrder.php');
        //migrate f(x) includes
        require_once ('migrate/migrateAccount.php');
        //for staging default apiusername to B-IGY-3791 (universal username on staging)
        $this->default_data['apiKey'] = $this->sendy_fulfillment_get_apikey();
        $this->default_data['apiusername'] = $this->sendy_fulfillment_get_apiusername();
        $this->default_data['channel_id'] = $this->sendy_fulfillment_get_saleschannelid();
        $this->default_data['default_quantity'] = get_option('sendy_fulfillment_default_quantity', 1);
        $this->default_data['default_quantity_type'] = get_option('sendy_fulfillment_default_quantity_type', 'KILOGRAMS');
        $this->default_data['default_currency'] = get_option('sendy_fulfillment_default_currency', 'KES'); //defaulting to KES
        $this->default_data['environment'] = get_option('sendy_fulfillment_environment');
        $this->default_data['default_means_of_payment'] = 'MPESA'; //currently defaulting to mpesa
        $this->default_data['live_api_link'] = 'https://fulfillment-api.sendyit.com/v2';
        $this->default_data['staging_api_link'] = 'https://fulfillment-api-test.sendyit.com/v2';
        $this->default_data['live_tracking_link'] = 'https://buyer.sendyit.com';
        $this->default_data['staging_tracking_link'] = 'https://buyer-test.sendyit.com';
        $this->default_data['migration'] = (get_option('sendy_fulfillment_environment') == 'Test' && get_option('sendy_fulfillment_sales_channel_id_test')) || (get_option('sendy_fulfillment_environment') == 'Live' && get_option('sendy_fulfillment_sales_channel_id_live'));
    }
    function sendy_fulfillment_get_apiusername() {
        if (get_option('sendy_fulfillment_environment') == 'Live') {
            return get_option('sendy_fulfillment_api_username_live');
        } else {
          return get_option('sendy_fulfillment_api_username_test');
        }
      }
  function sendy_fulfillment_get_apikey() {
      if (get_option('sendy_fulfillment_environment') == 'Live') {
          return get_option('sendy_fulfillment_api_key_live');
      } else {

          return get_option('sendy_fulfillment_api_key_test');
      }

    }
    function sendy_fulfillment_get_saleschannelid() {
      if (get_option('sendy_fulfillment_environment') == 'Live') {
          return get_option('sendy_fulfillment_sales_channel_id_live');
      } else {
          return get_option('sendy_fulfillment_sales_channel_id_test');
      }

    }
    function sendy_fulfillment_get_link($append) {
        if ($this->default_data['environment'] == 'Live') {
            return $this->default_data['live_api_link'] . '/' . $append;
        } else {
            return $this->default_data['staging_api_link'] . '/' . $append;
        }
    }
    function sendy_fulfillment_get_tracking_link($append) {
        if ($this->default_data['environment'] == 'Live') {
            return $this->default_data['live_tracking_link'] . '/' . $append;
        } else {
            return $this->default_data['staging_tracking_link'] . '/' . $append;
        }
    }
    function sendy_fulfillment_add_edit($data) {
        //check if adding or updating
        if ((isset($data['product_id'])) && (strlen($data['product_id']) > 2)) {
            //do an update
            return $this->sendy_fulfillment_edit($data);
        } else {
            // do an add
            return $this->sendy_fulfillment_add($data);
        }
    }
    public function sendy_fulfillment_add($data) {
        $url = $this->sendy_fulfillment_get_link('add-product');
        $product_details_url = $this->sendy_fulfillment_get_link('product-details');
        if ($this->default_data['migration']) {
            $response = sendyFulfillmentAddProduct($this->default_data, $data, $url, $product_details_url);
            return $response;
        }
    }
    public function sendy_fulfillment_edit($data) {
        $url = $this->sendy_fulfillment_get_link('edit-product');
        if ($this->default_data['migration']) {
            $response = sendyFulfillmentEditProduct($this->default_data, $data, $url);
            return $response;
        }
    }
    public function sendy_fulfillment_archive($data) {
        $url = $this->sendy_fulfillment_get_link('unlink-product');
        if ($this->default_data['migration']) {
            $response = sendyFulfillmentUnlinkProduct($this->default_data, $data, $url);
            return $response;
        }
    }
    function sendy_fulfillment_place_order($data) {
        $url = $this->sendy_fulfillment_get_link('create-fulfilment-request');
        if ($this->default_data['migration']) {
            $response = sendyFulfillmentPlaceOrder($this->default_data, $data, $url);
            return $response;
        }
    }
    function sendy_fulfillment_track_order($data) {
        $url = $this->sendy_fulfillment_get_link('track-order');
        if ($this->default_data['migration']) {
            $response = sendyFulfillmentTrackOrder($this->default_data, $data, $url);
            return $response;
        }
    }
    function sendy_fulfillment_cancel_order($data) {
        $url = $this->sendy_fulfillment_get_link('cancel-order');
        if ($this->default_data['migration']) {
            $response = sendyFulfillmentCancelOrder($this->default_data, $data, $url);
            return $response;
        }
    }
    function sendy_fulfillment_migrate_user_account($data) {
        $url = $this->sendy_fulfillment_get_link('saleschannel-migrate');
        $response = sendyFulfillmentMigrateAccount($this->default_data, $data, $url);
        return $response;
    }
    function sendy_fulfillment_save_pickup_address($data) {
        $url = $this->sendy_fulfillment_get_link('pickup-address');
        if ($this->default_data['migration']) {
            $response = sendyFulfillmentSavePickUpAddress($this->default_data, $data, $url);
            return $response;
        }
    }
    function sendy_fulfillment_test_settings($data) {
        $includedStuff = get_included_files();
        echo '<pre>';
        echo '</pre>';
        add_option('sendy_apiKey', '');
        echo 'testing settings';
    }
}
