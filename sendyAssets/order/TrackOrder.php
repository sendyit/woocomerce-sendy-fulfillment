
<?

function TrackOrder($default_data, $data, $url,$tracking_url){



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
  //$resp = curl_exec($curl);

$resp = '{
    "message": "Tracking data retrieved successfully",
    "data": [
        {
            "event_code": "event.delivery.order.created",
            "event_date": 1659968974000,
            "event_notes": null,
            "meta_data": []
        },
        {
            "event_code": "event.delivery.order.canceled.by.seller",
            "event_date": 1659969262000,
            "event_notes": "Set the wrong quantities",
            "meta_data": []
        }
    ]
}';

  curl_close($curl);
  //echo $resp;
  $resp_json = json_decode($resp);

  if ($resp_json->message == 'Tracking data retrieved successfully') {

    $delivery_event =  $resp_json->data;


    if(count($delivery_event) == 0){ $delivery_status = 'Order Created';}
    else{

      //get the last item in array

      $latest_delivery_log = $delivery_event[count($delivery_event)-1];

      $latest_delivery_log_message = $latest_delivery_log->event_code;


      $delivery_status = $latest_delivery_log_message;

      //remove event.delivery remove . add spaces & capitalize

      $delivery_status =
      ucfirst(str_replace("."," ",str_replace("event.delivery.","",$latest_delivery_log_message)));

    }

    $tracking_respond = array(
      'delivery_status'=>$delivery_status,
      'delivery_tracking_link' => $tracking_url
    );

      return $tracking_respond;
  } else {
      return ($resp_json->message);
  }

}
