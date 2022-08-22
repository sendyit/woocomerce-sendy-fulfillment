<?php

//product_variant_quantity_type

echo 'testing adding products <br><br>';

$data = array(

    "product_id"=>"P-JAA-2920"
    );


echo 'posted data as an array <pre>'.json_encode($data,JSON_PRETTY_PRINT).'</pre> <br></br> Response ';
require_once '../SendyFulfillment.php';

$products = new FulfillmentProduct();

$response = $products->archive($data);

echo '<pre>'.json_encode($response).'</pre>';
