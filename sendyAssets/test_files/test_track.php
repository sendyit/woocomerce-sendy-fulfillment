

<?php

//product_variant_quantity_type

echo 'testing tracking order <br><br>';



$data = array('order_id'=>'D-QSU-6745');


echo 'posted data as an array <pre>'.json_encode($data,JSON_PRETTY_PRINT).'</pre> <br></br> Response ';
//return 0;
require_once '../SendyFulfillment.php';

$products = new FulfillmentProduct();

$response = $products->track_order($data);

 print_r($response);
