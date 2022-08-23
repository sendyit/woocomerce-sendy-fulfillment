<?php
Class FulfillmentProduct {
    // Properties
    public $default_data;
    function __construct() {

        require_once('../../../../../wp-config.php');

        //product f(x) includes
        require_once('product/AddProduct.php');
        require_once('product/EditProduct.php');
        require_once('product/ArchiveProduct.php');

        //order f(x) includes
        require_once( 'order/PlaceOrder.php');
        require_once( 'order/TrackOrder.php');

        //get these details for tracking B-MKB-9125  (B-DDM-6999)
        $this->default_data['apiKey'] = get_option('sendy_fulfillment_api_key'); //get from db
        $this->default_data['apiusername'] = get_option('sendy_fulfillment_api_username'); //get from db
        $this->default_data['default_quantity'] = get_option('sendy_fulfillment_default_quantity',1); //get from db
        $this->default_data['default_quantity_type'] = get_option('sendy_fulfillment_default_quantity_type','KILOGRAMS'); //get from db
        $this->default_data['default_currency'] = get_option('sendy_fulfillment_default_currency'); //get from db
        $this->default_data['environment'] = get_option('sendy_fulfillment_environment'); //get from db  Test
        $this->default_data['default_means_of_payment'] = 'MPESA'; //currently defaulting to mpesa
        $this->default_data['live_api_link'] = 'https://fulfillment-api.sendyit.com/v1';
        $this->default_data['staging_api_link'] = 'https://fulfillment-api-test.sendyit.com/v1';

        $this->default_data['live_tracking_link'] = 'https://buyer.sendyit.com';
        $this->default_data['staging_tracking_link'] = 'https://buyer-test.sendyit.com';
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
        $url = $this->get_link('product-status');
        $response = ArchiveProduct($this->default_data, $data, $url);
        return $response;
    }
    function place_order($data) {
        $url = $this->get_link('orders');
        $response = PlaceOrder($this->default_data, $data, $url);
        return $response;
    }
    function track_order($data) {
        $url = $this->get_link('orders/tracking/'.$data['order_id']);
        $tracking_url = $this->get_tracking_link($data['order_id']);
        $response = TrackOrder($this->default_data, $data, $url,$tracking_url);
        return $response;
    }
    function test_settings($data){

      $includedStuff = get_included_files();
      echo '<pre>';
 //print_r($includedStuff);
 echo '</pre>';

      add_option('sendy_apiKey', 'uTvdcS6TGU3DyvpfK2pWNh53W9vMrE');

      echo 'testing settings';


    }
}
