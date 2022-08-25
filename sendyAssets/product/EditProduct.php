<?php



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
        "product_variant_quantity": ' . $data['product_variant_quantity'] . ',
        "product_variant_quantity_type": "'.clean_up_quantity($data['product_variant_quantity_type']).'",
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

function clean_up_quantity($quantity){

if( $quantity == 'KILOGRAM') { return array('unit'=>'KILOGRAM','ratio'=>1 ); }
else if( $quantity == 'GRAM') { return array('unit'=>'GRAM','ratio'=>1 ); }
else if( $quantity == 'POUND') { return array('unit'=>'KILOGRAM','ratio'=>1 ); }
else if( $quantity == 'OUNCE') { return array('unit'=>'KILOGRAM','ratio'=>1 ); }
else { return array('unit'=>'KILOGRAM','ratio'=>1 ); }


}
