<?php

function sendyFulfillmentUnlinkProduct($default_data, $data, $url) {

    $unlink_channel_product_data = array(
        'api_username' => $default_data['apiusername'],
        'api_key' => $default_data['apiKey'],
        'channel_id' => $default_data['channel_id'],
        'product_id' => $data['product_id'],
      );
      
      $args = array(
          'body'        => $unlink_channel_product_data,
          'timeout'     => '5000',
          'redirection' => '5',
          'httpversion' => '1.0',
          'blocking'    => true,
          'headers'     => array(),
          'cookies'     => array(),
      );
      $response = wp_remote_post( $url, $args );
      $resp_json = json_decode($response['body']);

    if ($resp_json->message == 'Product unlinked from sales channel successfully') {

        return $resp_json->data;
    } else {
        return ($resp_json->message);
    }
}
