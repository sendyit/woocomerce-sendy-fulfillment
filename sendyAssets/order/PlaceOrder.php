
<?

function sendyFulfillmentPlaceOrder($default_data, $data, $url){

  $create_order_data = array(
    'api_username' => $default_data['apiusername'],
    'api_key' => $default_data['apiKey'],
    'channel_id' => $default_data['channel_id'],
    'means_of_payment' => array(
      'means_of_payment_type' => $default_data['default_means_of_payment'],
      'means_of_payment_id' => ' ',
      'participant_type' => 'SELLER',
      'participant_id' => $default_data['apiusername'],
    ),
    'products' => $data->products,
    'destination' => $data->destination,
  );

  $args = array(
      'body'        => $create_order_data,
      'timeout'     => '5000',
      'redirection' => '5',
      'httpversion' => '1.0',
      'blocking'    => true,
      'headers'     => array(),
      'cookies'     => array(),
  );

  $response = wp_remote_post( $url, $args );
  $resp_json = json_decode($response['body']);

  if ($resp_json->message == 'Fulfilment request created successfully on sales channel') {
      return $resp_json->data;
  } else {
      return ($resp_json->message);
  }

}
