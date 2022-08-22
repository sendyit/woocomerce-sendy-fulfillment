<?php
Class FulfillmentProduct {
    // Properties

    public $default_data;
    function __construct() {
        $this->default_data['apiKey'] = 'uTvdcS6TGU3DyvpfK2pWNh53W9vMrE';
        $this->default_data['apiusername'] = 'B-XGS-1542';
        $this->default_data['default_quantity'] = 100;
        $this->default_data['default_quantity_type'] = 'Kilograms';
        $this->default_data['default_currency'] = 'KES';
        $this->default_data['environment'] = 'testing';
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
    
        include 'product/AddProduct.php';
        $url = $this->get_link('add-product');
        $response = Addproduct($this->default_data, $data,$url); 
        return $response;
}
    public function edit($data) {
        
        include 'product/EditProduct.php';
        $url = $this->get_link('edit-product');
        $response = EditProduct($this->default_data, $data,$url); 
        return $response;
    }
    public function delete() {
        echo 'editing product';
    }
}
