<?php

//product_variant_quantity_type

echo 'testing adding products <br><br>';

$data = array(

    "product_name"=>"product 1",
    "product_description"=>"product description",
    "product_variant_description"=>"product description",
    "product_variant_currency"=>"KES",
    "product_variant_unit_price"=>600,
    "product_variant_quantity"=>30,  //optional
    "product_variant_quantity_type"=>"KILOGRAMS",
    "product_variant_image_link"=>"https://sendy-partner-docs.s3-eu-west-1.amazonaws.com/fulfillment_products/B-000-1111_1658029476953.jpeg",
);


echo 'posted data as an array <pre>'.json_encode($data,JSON_PRETTY_PRINT).'</pre> <br></br> Response ';
require_once '../SendyFulfillment.php';

$products = new FulfillmentProduct();

$response = $products->add_edit($data);

echo '<pre>'.json_encode($response).'</pre>';
