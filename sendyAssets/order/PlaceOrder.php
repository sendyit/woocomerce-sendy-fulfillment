
<?

function PlaceOrder($default_data, $data, $url){



  $create_order_data = '{
  "api_username": "' . $default_data['apiusername'] . '",
  "api_key": "' . $default_data['apiKey'] . '",
  "channel_id": "' . $default_data['channel_id'] . '",
  "products": ' . json_encode($data->products) . ',
  "destination": ' . json_encode($data->destination) . '
  }';

  $curl = curl_init($url);
  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_POST, true);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  $headers = array("Accept: application/json", "Content-Type: application/json",);
  curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
  $data = $create_order_data;
  curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
  //for debug only!
  curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
  $resp = curl_exec($curl);
  curl_close($curl);
  //echo $resp;
  $resp_json = json_decode($resp);

  if ($resp_json->message == 'Order created successfully') {

      return $resp_json->data;
  } else {
      return ($resp_json->message);
  }

}
