<?php

function sendyFulfillmentAddProduct($default_data, $data, $url, $product_details_url) {
    $quantity_data = sendy_fulfillment_clean_up_quantity($data['product_variant_quantity_type']);


    $add_channel_product_data = array(
      'api_username' => $default_data['apiusername'],
      'api_key' => $default_data['apiKey'],
      'channel_id' => $default_data['channel_id'],
      'product_name' => $data['product_name'],
      'product_description' => $data['product_description'],
      'product_variants' => array(
          array(
              'product_variant_description' => $data['product_variant_description'],
              'product_variant_currency' => $default_data['default_currency'],
              'product_variant_unit_price' => intval($data['product_variant_unit_price']),
              'product_variant_quantity' => floatval($data['product_variant_quantity']) *  $quantity_data['ratio'],
              'product_variant_quantity_type' => $quantity_data['unit'],
              'product_variant_image_link' => $data['product_variant_image_link'],
              'product_variant_expiry_date' => '',
          ),
      ),
    );

    $args = array(
      'body'        => $add_channel_product_data,
      'timeout'     => '5000',
      'redirection' => '5',
      'httpversion' => '1.0',
      'blocking'    => true,
      'headers'     => array(),
      'cookies'     => array(),
  );

  $response = wp_remote_post( $url, $args );
  $resp_json = json_decode($response['body']);

    if ($resp_json->message == 'Product added successfully on sales channel') {

      $response_data = $resp_json->data;
      $response_data_final['product_id'] = $response_data->product->product_id;
      $response_data_final['product_variant_id'] = $response_data->product->product_variants[0]->product_variant_id;

        return $response_data_final;
    } else {
        return ($resp_json->message);
    }
}

function sendy_fulfillment_clean_up_quantity($quantity){

if( $quantity == 'kg') { return array('unit'=>'KILOGRAM','ratio'=>1 ); }
else if( $quantity == 'g') { return array('unit'=>'GRAM','ratio'=>1 ); }
else if( $quantity == 'lbs') { return array('unit'=>'KILOGRAM','ratio'=>0.45 ); }
else if( $quantity == 'oz') { return array('unit'=>'KILOGRAM','ratio'=>0.03 ); }
else { return array('unit'=>'KILOGRAM','ratio'=>1 ); }


}
