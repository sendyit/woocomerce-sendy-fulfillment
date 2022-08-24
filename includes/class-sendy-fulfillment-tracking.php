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
            <form action="'.$trackingLink.'" target="_blank">
            <div class="tracking-block--inner">
                <h4 class="tracking-status">'.$orderStatus.'</h4>
                <input class="track-order-button" type="submit" name="track-order" value="Track Order"></div>
            </form>
            </div> <br></br>
        ';
  }
  ?>
  <style>
    .tracking-block {
        color: #333;
        background-color: #f8f8f8;
        border: 1px solid transparent;
        -webkit-box-shadow: 0 1px 1px rgb(0 0 0 / 5%);
        box-shadow: 0 1px 1px rgb(0 0 0 / 5%);
        height: 13rem;
        padding: 2rem;
    }
    .track-order-button {
        background: #fff !important;
        border: 1px solid #ccc !important;
        padding: 0.375em 0.625em !important;
        color: #32373c !important;
        border-radius: 4px !important;
    }
    .tracking-status {
        color: #324ba8;
        font-weight: 400;
    }
    .tracking-block--inner {
        height:20rem;
    }
  </style>