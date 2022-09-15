
<?

function migrateAccount($default_data, $data, $url){



  $migrate_account_data = '{
  "api_username": "' . $default_data['apiusername'] . '",
  "api_key": "' . $default_data['apiKey'] . '",
  "country": "' . $data['country'] . '"
  }';

  $curl = curl_init($url);
  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_POST, true);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  $headers = array("Accept: application/json", "Content-Type: application/json",);
  curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
  $data = $migrate_account_data;
  curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
  //for debug only!
  curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
  $resp = curl_exec($curl);
  curl_close($curl);
  $resp_json = json_decode($resp);

  if ($resp_json->message == 'Business added to sales channel successfully') {

      return $resp_json->data;
  } else {
      return ($resp_json->message);
  }

}

function savePickUpAddress($default_data, $data, $url){


  $save_pickup_address_data = '{
  "api_username": "' . $default_data['apiusername'] . '",
  "api_key": "' . $default_data['apiKey'] . '",
  "business_default_address": ' . json_encode($data) . '
  }';

  $curl = curl_init($url);
  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_POST, true);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  $headers = array("Accept: application/json", "Content-Type: application/json",);
  curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
  $data = $save_pickup_address_data;
  curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
  //for debug only!
  curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
  $resp = curl_exec($curl);
  curl_close($curl);
  $resp_json = json_decode($resp);

  if ($resp_json->message == 'Business details saved successfully') {

      return $resp_json->data;
  } else {
      return ($resp_json->message);
  }

}
