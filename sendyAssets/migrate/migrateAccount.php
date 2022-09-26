
<?

function sendyFulfillmentMigrateAccount($default_data, $data, $url){

  $migrate_account_data = array(
    'api_username' => $default_data['apiusername'],
    'api_key' => $default_data['apiKey'],
    'country' => $data['country'],
  );
  
  $args = array(
      'body'        => $migrate_account_data,
      'timeout'     => '5000',
      'redirection' => '5',
      'httpversion' => '1.0',
      'blocking'    => true,
      'headers'     => array(),
      'cookies'     => array(),
  );
  $response = wp_remote_post( $url, $args );
  $resp_json = json_decode($response['body']);

  if ($resp_json->message == 'Business added to sales channel successfully') {

      return $resp_json->data;
  } else {
      return ($resp_json->message);
  }

}

function sendyFulfillmentSavePickUpAddress($default_data, $data, $url){

  $save_pickup_address_data = array(
    'api_username' => $default_data['apiusername'],
    'api_key' => $default_data['apiKey'],
    'business_default_address' => $data,
  );
  
  $args = array(
      'body'        => $save_pickup_address_data,
      'timeout'     => '5000',
      'redirection' => '5',
      'httpversion' => '1.0',
      'blocking'    => true,
      'headers'     => array(),
      'cookies'     => array(),
  );
  $response = wp_remote_post( $url, $args );
  $resp_json = json_decode($response['body']);
  
  if ($resp_json->message == 'Business details saved successfully') {
      return $resp_json->data;
  } else {
      return $resp_json->message;
  }

}
