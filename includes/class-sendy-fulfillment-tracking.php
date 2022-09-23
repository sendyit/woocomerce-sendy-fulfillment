<?php

  add_action('woocommerce_thankyou', 'get_sendy_fulfillment_order');

  function get_sendy_fulfillment_order($order_id) {
      $sendy_order_id = '';

        global $wpdb;

        $results = $wpdb->get_results(
          $wpdb->prepare(
            "SELECT
                orders.meta_id as id,
                orders.meta_key,
                orders.meta_value
                FROM {$wpdb->postmeta} orders
                where orders.post_id = %d",
                $order_id
            )
          );
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
        if ($sendy_order_id) {
            $displayTracking = get_option('sendy_fulfillment_include_tracking','0');
            $createFulfillmentOrder = get_option('sendy_fulfillment_place_order_on_fulfillment','0');
            if($displayTracking == '1' && $createFulfillmentOrder == '1') {
            sendy_fulfillment_add_tracking_data($sendy_order_id);
        }
    }
  }

  function sendy_fulfillment_add_tracking_data($sendy_order_id){
      echo '<h2>Track Order</h2>';

        require_once plugin_dir_path( dirname( __FILE__ ) ) . './sendyAssets/SendyFulfillment.php';

        $data = array('order_id'=>$sendy_order_id);

        $SendyFulfillmentProduct = new SendyFulfillmentProduct();

        $response = $SendyFulfillmentProduct->sendy_fulfillment_track_order($data);
        
        $orderStatus = "REQUEST PROCESSING";
        if (is_array($response)) {
          $orderStatus = $response['order_status'];
        }

        echo '
            <div class="tracking-block">
            <div class="tracking-block--inner">
                <h4 class="tracking-status">'.$orderStatus.'</h4>
            </div> </div><br></br>
        ';
  }
  function sendy_fulfillment_add_style()
    {
        wp_enqueue_style('styles', plugin_dir_url(__FILE__) . '../styles/index.css', false);
        $style = "";
        ?><script>
            setTimeout(() => {
                document.getElementById('sendy_fulfillment_delivery_address_long_field').style.display = 'none';
                document.getElementById('sendy_fulfillment_delivery_address_lat_field').style.display = 'none';
            }, 300);
        </script><?php
    }

    add_action('wp_enqueue_scripts', 'sendy_fulfillment_add_style');
  ?>
