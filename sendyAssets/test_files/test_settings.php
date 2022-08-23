

<?php

//product_variant_quantity_type

echo 'testing settings<br><br>';



$data = array('setting1'=>'value1');


echo 'posted data as an array <pre>'.json_encode($data,JSON_PRETTY_PRINT).'</pre> <br></br> Response ';
//return 0;
require_once '../SendyFulfillment.php';

$products = new FulfillmentProduct();

$response = $products->test_settings($data);

echo '<pre>'.json_encode($response).'</pre>';
