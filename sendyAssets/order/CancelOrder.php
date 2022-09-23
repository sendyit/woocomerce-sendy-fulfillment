<?

function sendyFulfillmentCancelOrder($default_data, $data, $url){

  $cancel_order_data = array(
    'api_username' => $default_data['apiusername'],
    'api_key' => $default_data['apiKey'],
    'channel_id' => $default_data['channel_id'],
    'fulfilment_request_id' => $data['order_id'],
    'cancellation_reason' => $data['cancellation_reason'],
  );
  
  $args = array(
      'body'        => $cancel_order_data,
      'timeout'     => '5000',
      'redirection' => '5',
      'httpversion' => '1.0',
      'blocking'    => true,
      'headers'     => array(),
      'cookies'     => array(),
  );
  $response = wp_remote_post( $url, $args );
  $resp_json = json_decode($response['body']);

  if ($resp_json->message == 'Fulfillment request cancelled successfully') {
      return $resp_json->data;
  } else {
      return ($resp_json->message);
  }
}
