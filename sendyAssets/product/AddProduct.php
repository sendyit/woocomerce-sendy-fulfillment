<?php

//function to add products

function AddProduct($default_data, $data, $url, $product_details_url) {

    $add_product_data = '{
    "api_username": "' . $default_data['apiusername'] . '",
    "api_key": "' . $default_data['apiKey'] . '",
    "product_name": "' . $data['product_name'] . '",
    "product_description": "' . $data['product_description'] . '",
    "product_variants": [
    {
        "product_variant_description": "' . $data['product_variant_description'] . '",
        "product_variant_currency": "' . $default_data['default_currency'] . '",
        "product_variant_unit_price": ' . $data['product_variant_unit_price'] . ',
        "product_variant_quantity": 30,
        "product_variant_quantity_type": "KILOGRAM",
        "product_variant_image_link": "' . $data['product_variant_image_link'] . '",
        "product_variant_expiry_date": 123456789
    }
    ]
    }';

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $headers = array("Accept: application/json", "Content-Type: application/json",);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    $data = $add_product_data;
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    //for debug only!
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    $resp = curl_exec($curl);
    // curl_close($curl);
    $resp_json = json_decode($resp);

    if ($resp_json->message == 'Product added successfully') {

      $response_data = $resp_json->data;


 $product_variant_id = getVariantId($default_data, $response_data->productId, $product_details_url);

 $response_data_final['product_id'] = $response_data->productId;
 $response_data_final['product_variant_id'] = $product_variant_id;

        return $response_data_final;
    } else {
        return ($resp_json->message);
    }
}


function getVariantId($default_data, $product_id, $url){




  //echo $url;
  $add_product_data = '{
  "api_username": "' . $default_data['apiusername'] . '",
  "api_key": "' . $default_data['apiKey'] . '",
  "product_id": "' . $product_id . '"
  }';
  // echo '<pre>' . $add_product_data . '</pre>';
  // echo 'adding product';
  $curl = curl_init($url);
  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_POST, true);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  $headers = array("Accept: application/json", "Content-Type: application/json",);
  curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
  $data = $add_product_data;
  curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
  //for debug only!
  curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
  $resp = curl_exec($curl);
  // curl_close($curl);

  //echo '<br><br>response '.$resp;
  $resp_json = json_decode($resp);

  if($resp_json->message == 'Product details fetched successfully'){

    return $resp_json->data->product_variants[0]->product_variant_id;
  }else{

    return 'not found';
  }







}