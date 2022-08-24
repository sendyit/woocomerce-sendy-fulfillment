<?php
  add_action('woocommerce_thankyou', 'add_tracking_data');

  function add_tracking_data(){
      echo '<h2>Track Sendy Fulfillment Order</h2>';

        require_once plugin_dir_path( dirname( __FILE__ ) ) . './sendyAssets/SendyFulfillment.php';

        $order_id = WC()->session->get( 'sendy_fulfillment_order_id');

        $data = array('order_id'=>$order_id);

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
