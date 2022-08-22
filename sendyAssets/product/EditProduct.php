<?php

//function to edit products

/**{
  "api_username": "B-XGS-0000",
  "api_key": "eyJhbGciOiJIUzI1NiIsInR5cGUiOiJKV1QifQ.eyJwYXlsb2FkIjp7ImVycm9ycyI6e30sImRhdGEiOnsibG9naW5fc3VjY2VzcyI6dHJ1ZSwiYnVzaW5lc3MiOnsiYnVzaW5lc3NfZW1haWwiOiJsZXdpc0BzZW5keWl0LmNvbSIsImJ1c2luZXNzX2lkIjoiQi1YR1MtMTU0MiIsInVzZXJfaWQiOiJVLUxTSC01MDg2IiwiYnVzaW5lc3NfbmFtZSI6IlNrd29kaSBMdGQifX0sIm1lc3NhZ2UiOiJidXNpbmVzcy5zaWdudXAuc3VjY2VzcyJ9LCJzdGF0dXMiOnRydWV9",
  "product_name": "Pen",
  "product_id": "P-KXG-0000",
  "product_description": "Ball point pen",
  "product_variants": [
    {
      "product_variant_id": "PV-DUH-1182",
      "product_variant_description": "Fortified Cup",
      "product_variant_currency": "KES",
      "product_variant_unit_price": 400,
      "product_variant_quantity": 30,
      "product_variant_quantity_type": "KILOGRAM",
      "product_variant_image_link": "https://sendy-partner-docs.s3-eu-west-1.amazonaws.com/fulfillment_products/B-000-1111_1658029476953.jpeg",
      "product_variant_expiry_date": 123456789
    }
  ]
} */

function EditProduct($default_data, $data, $url) {
    //echo $url;
    $add_product_data = '{
    "api_username": "' . $default_data['apiusername'] . '",
    "api_key": "' . $default_data['apiKey'] . '",
    "product_name": "' . $data['product_name'] . '",
    "product_id": "' . $data['product_id'] . '",
    "product_description": "' . $data['product_description'] . '",
    "product_variants": [
    {
        "product_variant_id": "'.$data['product_variant_id'] . '",
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
    $resp_json = json_decode($resp);
    //echo $resp;
    //var_dump($resp_json);
    if ($resp_json->message == 'Product edited successfully') {
        return ($resp_json->data);
    } else {
        return ($resp_json->message);
    }
}
