<?php

require_once plugin_dir_path( dirname( __FILE__ ) ) . './sendyAssets/SendyFulfillment.php';

// add_action( 'the_content', 'product_archive' );
 
// add_action( 'before_delete_post', 'wpse_110037_new_posts' );
add_action( 'save_post', 'process_action' );

function process_action($post_id) {
    global $wpdb;
    $results = $wpdb->get_results( "SELECT 
    products.ID as id,
    products.post_status
    FROM {$wpdb->posts} products 
    where products.ID = $post_id");
    foreach($results as $row){  
        if ($row->post_status == "trash") {
            product_archive($row->id);
        } else if ($row->post_status == "publish") {
            product_add($row->id);
        }
    }
}

function product_sync () {
    $products = new FulfillmentProduct();
    global $wpdb;
    global $woocommerce;
    $results = $wpdb->get_results( "SELECT 
    products.ID as id,
    products.post_name as product_name, 
    products.post_content as product_description, 
    products.post_excerpt as product_variant_description, 
    images.guid as product_variant_image_link 
    FROM {$wpdb->posts} products 
    join {$wpdb->posts} images on images.post_parent = products.ID 
    where products.post_type = 'product' and not products.post_title = 'AUTO-DRAFT'");
    $response = [];
    $productsArray = [];
    foreach($results as $row){  
      $synced = $wpdb->get_results("SELECT info.meta_value, info.post_id from {$wpdb->postmeta} info where info.meta_key = 'sendy_product_id' and info.post_id = $row->id");
      $synced_variant = $wpdb->get_results("SELECT info.meta_value, info.post_id from {$wpdb->postmeta} info where info.meta_key = 'sendy_product_variant_id' and info.post_id = $row->id");
      $row->product_variant_currency = get_woocommerce_currency(); 
      $sale_price = $wpdb->get_results("SELECT info.meta_value from {$wpdb->postmeta} info where info.meta_key = '_sale_price' and info.post_id = $row->id");
      $regular_price = $wpdb->get_results("SELECT info.meta_value from {$wpdb->postmeta} info where info.meta_key = '_regular_price' and info.post_id = $row->id");
      if (count($sale_price) > 0) {
        $row->product_variant_unit_price = $sale_price[0]->meta_value;
      } else if (count($regular_price) > 0) {
        $row->product_variant_unit_price = $regular_price[0]->meta_value;
      }
      $row->product_variant_quantity_type = get_option('woocommerce_weight_unit');
      $weight = $wpdb->get_results("SELECT info.meta_value from {$wpdb->postmeta} info where info.meta_key = '_weight' and info.post_id = $row->id");
      if (count($weight) > 0) {
        $row->product_variant_quantity = $weight[0]->meta_value;
      } else {
        $row->product_variant_quantity = "null";
      }
      if ($row->product_description == "") {
        $row->product_description = "null";
      }
      if ($row->product_variant_description == "") {
        $row->product_variant_description = "null";
      }
      if ($row->product_variant_unit_price) {
        
        if (count($synced) > 0) {
            $row->product_id = $synced[count($synced) - 1]->meta_value;
            $row->product_variant_id = $synced_variant[count($synced_variant) - 1]->meta_value;
            $array = (array) $row;
            $product_id = $products->edit($array);
            array_push($response, $product_id);
          } else {
            $array = (array) $row;
            $product_id = $products->add($array);
            add_post_meta( $row->id, "sendy_product_id", $product_id['product_id'], false );
            add_post_meta( $row->id, "sendy_product_variant_id", $product_id['product_variant_id'], false );
            array_push($response, $product_id);
          }
      }
    }
}

if(isset($_POST['syncAllProducts']))
{
   if(product_sync()){
     echo "<script>alert('Product synced successfully')</script>";
    }
}

function product_add ($post_id) {
    $products = new FulfillmentProduct();
    global $wpdb;
    global $woocommerce;
    $results = $wpdb->get_results( "SELECT 
    products.ID as id,
    products.post_name as product_name, 
    products.post_content as product_description, 
    products.post_excerpt as product_variant_description
    FROM {$wpdb->posts} products 
    where products.ID = $post_id");
    $response = [];
    $productsArray = [];
    foreach($results as $row){  
      $product_image = $wpdb->get_results("SELECT images.guid from {$wpdb->posts} images where images.ID = $post_id");
      $row->product_variant_image_link = $product_image[0]->guid;
      $row->product_variant_currency = get_woocommerce_currency(); 
      $sale_price = $wpdb->get_results("SELECT info.meta_value from {$wpdb->postmeta} info where info.meta_key = '_sale_price' and info.post_id = $row->id");
      $regular_price = $wpdb->get_results("SELECT info.meta_value from {$wpdb->postmeta} info where info.meta_key = '_regular_price' and info.post_id = $row->id");
      if (count($sale_price) > 0) {
        $row->product_variant_unit_price = $sale_price[0]->meta_value;
      } else if (count($regular_price) > 0) {
        $row->product_variant_unit_price = $regular_price[0]->meta_value;
      }
      $row->product_variant_quantity_type = get_option('woocommerce_weight_unit');
      $weight = $wpdb->get_results("SELECT info.meta_value from {$wpdb->postmeta} info where info.meta_key = '_weight' and info.post_id = $row->id");
      if (count($weight) > 0) {
        $row->product_variant_quantity = $weight[0]->meta_value;
      } else {
        $row->product_variant_quantity = "null";
      }
      if ($row->product_description == "") {
        $row->product_description = "null";
      }
      if ($row->product_variant_description == "") {
        $row->product_variant_description = "null";
      }
      if ($row->product_variant_unit_price) {
            $array = (array) $row;
            $product_id = $products->add($array);
            add_post_meta( $row->id, "sendy_product_id", $product_id['product_id'], false );
            add_post_meta( $row->id, "sendy_product_variant_id", $product_id['product_variant_id'], false );
            array_push($response, $product_id);
      }
    }
}

function product_archive($post_id) {
    $products = new FulfillmentProduct();
    global $wpdb;
    global $woocommerce;
    $results = $wpdb->get_results( "SELECT 
    products.ID as id
    FROM {$wpdb->posts} products 
    where products.ID = $post_id");
    $response = [];
    $productsArray = [];
    foreach($results as $row){  
      $synced = $wpdb->get_results("SELECT info.meta_value, info.post_id from {$wpdb->postmeta} info where info.meta_key = 'sendy_product_id' and info.post_id = $row->id");
      $synced_variant = $wpdb->get_results("SELECT info.meta_value, info.post_id from {$wpdb->postmeta} info where info.meta_key = 'sendy_product_variant_id' and info.post_id = $row->id");
        $row->product_id = $synced[count($synced) - 1]->meta_value;
        $array = (array) $row;
        $product_id = $products->archive($array);
        add_post_meta( $post_id, "test_archive", $product_id, false );
        array_push($response, $product_id);
    }
}

function order_sync ( $content ) {
    $orders = new FulfillmentProduct();
    global $wpdb;
    global $woocommerce;
    $results = $wpdb->get_results( "SELECT ID
    FROM $wpdb->posts orders where orders.post_type = 'shop_order' ");
    $response = [];
    $products = [];
    $payload = (object)[];
    foreach($results as $row){  
        $order = wc_get_order($row->ID);
        foreach ($order->get_items() as $item_id => $item ) {
            $product_id = $item->get_product_id();
            $sendy_products = $wpdb->get_results("SELECT info.meta_value, info.post_id from {$wpdb->postmeta} info where info.meta_key = 'sendy_product_id' and info.post_id = $product_id");
            $sendy_product_variants = $wpdb->get_results("SELECT info.meta_value, info.post_id from {$wpdb->postmeta} info where info.meta_key = 'sendy_product_variant_id' and info.post_id = $product_id");
            $sendy_product_id = $sendy_products[count($sendy_products) - 1]->meta_value;
            $sendy_product_variant_id = $sendy_product_variants[count($sendy_product_variants) - 1]->meta_value;
            $product = (object)[];
            $product->product_id = $sendy_product_id;
            $product->product_variant_id = $sendy_product_variant_id;
            $product->quantity = $item->get_quantity();
            $product->currency = $order->get_currency();
            $product->unit_price = $item->get_product()->get_price();
            array_push($products, $product);
        }
        $destination = (object)[];
        $destination->name = "Lewis";
        $destination->phone_number = "+254795510441";
        $destination->secondary_phone_number = "";
        $destination->delivery_location = (object)[];
        $destination->delivery_location->description = "Marsabit plaza";
        $destination->delivery_location->longitude = 36.8880941;
        $destination->delivery_location->latitude = -1.3021192;
        $destination->house_location = "Sendy office";
        $destination->delivery_instructions = "";
        $payload->products = $products;
        $payload->destination = $destination;
        $order_id = $orders->place_order($payload);
    }
    return $content .= "<h6>Add products</h6>" ."\r\n". "<p>" .json_encode($order_id). "</p>". "<p><input type='text' placeholder='Product name' /></p>" ."\r\n". "<br><p><input type='text' placeholder='Product quantity' /></p>";
}

