<?php
  $displayTracking = get_option('sendy_fulfillment_include_tracking','0');
  $createFulfillmentOrder = get_option('sendy_fulfillment_place_order_on_fulfillment','0');

  if($displayTracking == '1' && $createFulfillmentOrder == '1') {
    add_action('woocommerce_thankyou', 'add_tracking_data');
  }

  function add_tracking_data($order_id){
      echo '<h2>Track Order</h2>';

        require_once plugin_dir_path( dirname( __FILE__ ) ) . './sendyAssets/SendyFulfillment.php';

        $sendy_order_id = '';

        global $wpdb;
        global $woocommerce;

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
                $sendy_order_id = $row->meta_value;
            }
        }

        $data = array('order_id'=>$sendy_order_id);

        $FulfillmentProduct = new FulfillmentProduct();

        $response = $FulfillmentProduct->track_order($data);

        $orderStatus = $response['delivery_status'];

        $trackingLink = $response['delivery_tracking_link'];

        echo '
            <div class="tracking-block">
            <div class="tracking-block--inner">
                <h4 class="tracking-status">'.$orderStatus.'</h4>
                <a href="'.$trackingLink.'" target="_blank"><button class="track-order-button">Track Order</button></a>
            </div> </div><br></br>
        ';
  }
  function add_style()
    {
        wp_enqueue_style('styles', plugin_dir_url(__FILE__) . '../styles/index.css', false);
    }

    add_action('wp_enqueue_scripts', 'add_style');
  ?>
