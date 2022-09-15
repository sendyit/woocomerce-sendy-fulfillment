
<?

function TrackOrder($default_data, $data, $url){



  $track_order_data = '{
  "api_username": "' . $default_data['apiusername'] . '",
  "api_key": "' . $default_data['apiKey'] . '",
  "channel_id": "' . $default_data['channel_id'] . '",
  "fulfilment_request_id": "' . $data['order_id'] . '"
  }';

  $curl = curl_init($url);
  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_POST, true);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  $headers = array("Accept: application/json", "Content-Type: application/json",);
  curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
  $data = $track_order_data;
  curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
  //for debug only!
  curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
  $resp = curl_exec($curl);



  curl_close($curl);
  $resp_json = json_decode($resp);
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
