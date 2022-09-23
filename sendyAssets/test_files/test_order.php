

<?php

//product_variant_quantity_type

echo 'testing creating order <br><br>';


$json_string ='{
  "products": [
    {
      "product_id": "P-WGR-9886",
      "product_variant_id": "PV-ZRL-6038",
      "quantity": "1",
      "currency": "KES",
      "unit_price": 50
    }
  ],
  "destination": {
    "name": "Customer name",
    "phone_number": "+254 795 000000",
    "secondary_phone_number": "",
    "delivery_location": {
      "description": "Sendy office, Marsabit plaza",
      "longitude": 36.837456,
      "latitude": -1.3021192
    },
    "house_location": "house_location",
    "delivery_instructions": "delivery_instructions"
  }
}';

$data = json_decode($json_string);


echo 'posted data as an array <pre>'.json_encode($data,JSON_PRETTY_PRINT).'</pre> <br></br> Response ';
//return 0;
require_once '../SendyFulfillment.php';

$products = new SendyFulfillmentProduct();

$response = $products->sendy_fulfillment_place_order($data);

echo '<pre>'.json_encode($response).'</pre>';
