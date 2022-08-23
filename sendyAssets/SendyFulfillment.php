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
        
        //get these details
        $this->default_data['apiKey'] = 'uTvdcS6TGU3DyvpfK2pWNh53W9vMrE'; //get from db
        $this->default_data['apiusername'] = 'B-DDM-6999'; //get from db
        $this->default_data['default_quantity'] = 100; //get from db
        $this->default_data['default_quantity_type'] = 'Kilograms'; //get from db
        $this->default_data['default_currency'] = 'KES'; //get from db
        $this->default_data['environment'] = 'testing'; //get from db
        $this->default_data['default_means_of_payment'] = 'MPESA'; //currently defaulting to mpesa
        $this->default_data['live_api_link'] = 'https://fulfillment-api.sendyit.com/v1';
        $this->default_data['staging_api_link'] = 'https://fulfillment-api-test.sendyit.com/v1';
    }
    function get_link($append) {
        if ($this->default_data['environment'] == 'live') {
            return $this->default_data['live_api_link'] . '/' . $append;
        } else {
            return $this->default_data['staging_api_link'] . '/' . $append;
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
        $response = TrackOrder($this->default_data, $data, $url);
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
