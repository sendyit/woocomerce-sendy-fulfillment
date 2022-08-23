
<?

function TrackOrder($default_data, $data, $url){



  $archive_product_data = '{
  "api_username": "' . $default_data['apiusername'] . '",
  "api_key": "' . $default_data['apiKey'] . '"
  }';

  //echo '<pre>'.$archive_product_data.'</pre>';
  //return 'done';

  $curl = curl_init($url);
  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_POST, true);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  $headers = array("Accept: application/json", "Content-Type: application/json",);
  curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
  $data = $archive_product_data;
  curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
  //for debug only!
  curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
  $resp = curl_exec($curl);
  curl_close($curl);
  echo $resp;
  $resp_json = json_decode($resp);

  if ($resp_json->message == 'Tracking data retrieved successfully') {

      return $resp_json->data;
  } else {
      return ($resp_json->message);
  }

}
