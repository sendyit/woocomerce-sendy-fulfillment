<?php

require_once plugin_dir_path( dirname( __FILE__ ) ) . './sendyAssets/SendyFulfillment.php';

// add_action( 'the_content', 'product_archive' );
 
// add_action( 'before_delete_post', 'wpse_110037_new_posts' );
add_action( 'save_post', 'process_action' );

function process_action($post_id) {
    global $wpdb;
    $results = $wpdb->get_results( "SELECT 
    products.ID as id,
    products.post_status,
    products.post_type
    FROM {$wpdb->posts} products 
    where products.ID = $post_id");
    foreach($results as $row){  
        if ($row->post_status == "trash") {
            product_archive($row->id);
        } else if ($row->post_status == "publish") {
            product_add($row->id);
        } else if ($row->post_status == "wc-pending" && $row->post_type == "shop_order") {
            order_sync($row->id);
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
    products.post_excerpt as product_variant_description
    FROM {$wpdb->posts} products 
    where products.post_type = 'product' and not products.post_title = 'AUTO-DRAFT' and not products.post_status = 'trash'");
    $response = [];
    $productsArray = [];
    foreach($results as $row){  
      $synced = $wpdb->get_results("SELECT info.meta_value, info.post_id from {$wpdb->postmeta} info where info.meta_key = 'sendy_product_id' and info.post_id = $row->id");
      $synced_variant = $wpdb->get_results("SELECT info.meta_value, info.post_id from {$wpdb->postmeta} info where info.meta_key = 'sendy_product_variant_id' and info.post_id = $row->id");
      $product_image = $wpdb->get_results("SELECT images.guid from {$wpdb->posts} images where images.post_parent = $row->id");
      if (count($product_image) > 0) {
        $row->product_variant_image_link = $product_image[0]->guid;
      } else {
        $row->product_variant_image_link = "null";
      }
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
            // add_post_meta( $row->id, "edit", $array, false );
            $product_id = $products->edit($array);
            array_push($response, $product_id);
          } else {
            $array = (array) $row;
            // add_post_meta( $row->id, "add", $array, false );
            $product_id = $products->add($array);
            if ($product_id['product_id'] != NULL) {
              add_post_meta( $row->id, "sendy_product_id", $product_id['product_id'], false );
              add_post_meta( $row->id, "sendy_product_variant_id", $product_id['product_variant_id'], false );
            } else {
              add_post_meta( $row->id, "failed_sync", $array, false );
            }
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
      $product_image = $wpdb->get_results("SELECT images.guid from {$wpdb->posts} images where images.post_parent = $post_id");
      if (count($product_image) > 0) {
        $row->product_variant_image_link = $product_image[0]->guid;
      } else {
        $row->product_variant_image_link = "null";
      }
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

function order_sync ($post_id) {
    $orders = new FulfillmentProduct();
    global $wpdb;
    global $woocommerce;
    $results = $wpdb->get_results( "SELECT ID
    FROM $wpdb->posts orders where orders.ID = $post_id ");
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
        $fName = get_post_meta($row->ID, '_billing_first_name', true);
        $lName = get_post_meta($row->ID, '_billing_last_name', true);
        $name = $fName . ' ' . $lName;
        $destination->name = $name;
        $destination->phone_number = get_post_meta($row->ID, '_billing_phone', true);
        $destination->secondary_phone_number = "";
        $destination->delivery_location = (object)[];
        $destination->delivery_location->description = "Marsabit plaza";
        $destination->delivery_location->longitude = 36.8880941;
        $destination->delivery_location->latitude = -1.3021192;
        $destination->house_location = "N/A";
        $notes = wc_get_order_notes(array(
          'order_id' => $row->ID,
        ));
        $all_notes = "";
        foreach($notes as $note){ 
          $all_notes = $all_notes . ". " . $note->content;
        }
        $destination->delivery_instructions = $all_notes;
        $payload->products = $products;
        $payload->destination = $destination;
        // add_post_meta( $post_id, "test_order", JSON_ENCODE($payload), false );
        $order_id = $orders->place_order($payload);
    }
}

