<?php



function sendyFulfillmentEditProduct($default_data, $data, $url) {

    $quantity_data = sendy_fulfillment_clean_up_quantity($data['product_variant_quantity_type']);

    $edit_channel_product_data = array(
        'api_username' => $default_data['apiusername'],
        'api_key' => $default_data['apiKey'],
        'channel_id' => $default_data['channel_id'],
        'product_name' => $data['product_name'],
        'product_id' => $data['product_id'],
        'product_description' => $data['product_description'],
        'product_variants' => array(
            array(
                'product_variant_id' => $data['product_variant_id'],
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
          'body'        => $edit_channel_product_data,
          'timeout'     => '5000',
          'redirection' => '5',
          'httpversion' => '1.0',
          'blocking'    => true,
          'headers'     => array(),
          'cookies'     => array(),
      );
      $response = wp_remote_post( $url, $args );
      $resp_json = json_decode($response['body']);

    if ($resp_json->message == 'Product edited successfully on sales channel') {
        return ($resp_json->data);
    } else {
        return ($resp_json->message);
    }
}
