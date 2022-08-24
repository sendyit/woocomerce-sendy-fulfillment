<?php
  add_action('woocommerce_thankyou', 'add_tracking_data');

  function add_tracking_data($order_id){
      echo("<script>console.log('PHP: " . $order_id . "');</script>");
      echo '<h2>Track Sendy Fulfillment Order</h2>';

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

        foreach($results as $row){  
            if ($row->meta_key == "sendy_order_id") {
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
            </div> <br></br>
        ';
  }
  function add_style()
    {
        wp_enqueue_style('styles', plugin_dir_url(__FILE__) . '../styles/index.css', false);
    }

    add_action('wp_enqueue_scripts', 'add_style');
  ?>
