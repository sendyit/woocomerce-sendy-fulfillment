<?php
require_once plugin_dir_path( dirname( __FILE__ ) ) . './sendyAssets/SendyFulfillment.php';

add_action( 'woocommerce_order_status_cancelled', 'cancel_fulfillment_request', 21, 1 );

function cancel_fulfillment_request( $order_id ){
    
    global $wpdb;

    $fulfillment_request_id = '';
    
    $results = $wpdb->get_results( "SELECT
            orders.meta_id as id,
            orders.meta_key,
            orders.meta_value
            FROM {$wpdb->postmeta} orders
            where orders.post_id = $order_id");
    $env = get_option("sendy_fulfillment_environment");
    $order_id = "";
    if ($env == "Test") {
        $order_id = "sendy_order_id_test";
    } else {
        $order_id = "sendy_order_id";
    }
    foreach($results as $row){
        if ($row->meta_key == $order_id) {
            $fulfillment_request_id = $row->meta_value;
        }
    }
    $FulfillmentProduct = new FulfillmentProduct();

    $data = array(
        "order_id"=>$fulfillment_request_id,
        "cancellation_reason"=>"Cancelled on wordpress"
    );
    $response = $FulfillmentProduct->cancel_order($data);

    echo '<pre>'.json_encode($response).'</pre>';
}
