<?php
add_action('woocommerce_thankyou', 'sendy_fulfillment_tracking');

function sendy_fulfillment_tracking( $order_id ) {
    $order = wc_get_order( $order_id );
    require_once plugin_dir_path( __FILE__ ) . '../sendyAssets/SendyFulfillment.php';

    $data = array('order_id'=>'D-QSU-6745');


    echo 'posted data as an array <pre>'.json_encode($data,JSON_PRETTY_PRINT).'</pre> <br></br> Response ';

    $FulfillmentProduct = new FulfillmentProduct();

    $response = $FulfillmentProduct->track_order($data);

    print_r($response);
	 
}