
<?

function sendyFulfillmentTrackOrder($default_data, $data, $url){

  $track_order_data = array(
    'api_username' => $default_data['apiusername'],
    'api_key' => $default_data['apiKey'],
    'channel_id' => $default_data['channel_id'],
    'fulfilment_request_id' => $data['order_id'],
  );
  
  $args = array(
      'body'        => $track_order_data,
      'timeout'     => '5000',
      'redirection' => '5',
      'httpversion' => '1.0',
      'blocking'    => true,
      'headers'     => array(),
      'cookies'     => array(),
  );
  $response = wp_remote_post( $url, $args );
  $resp_json = json_decode($response['body']);

  if ($resp_json->message == 'Tracking data retrieved successfully') {

    $request_event =  $resp_json->data->status;

    //remove _ add spaces & capitalize -> "REQUEST_IN_PICKUP"

    $request_status = ucfirst(strtolower((str_replace("_"," ",$request_event))));

    $tracking_respond = array(
      'order_status'=>$request_status
    );

      return $tracking_respond;
  } else {
      return ($resp_json->message);
  }

}
