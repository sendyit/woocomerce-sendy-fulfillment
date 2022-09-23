<?php

require_once plugin_dir_path( dirname( __FILE__ ) ) . './sendyAssets/SendyFulfillment.php';

 add_action( 'save_post', 'sendy_fulfillment_process_action' );

function sendy_fulfillment_process_action($post_id) {
    global $wpdb;
    $results = $wpdb->get_results(
      $wpdb->prepare(
        "SELECT 
        products.ID as id,
        products.post_status,
        products.post_type
        FROM {$wpdb->posts} products 
        where products.ID = %d",
        $post_id
      )
    );
    $env = get_option("sendy_fulfillment_environment");
    $edit_status = [];
    $order_placement_status = $wpdb->get_results(
      $wpdb->prepare(
        "SELECT settings.option_value from {$wpdb->options} settings where settings.option_name = 'sendy_fulfillment_place_order_on_fulfillment'"
      )
    );
    $product_placement_status = $wpdb->get_results(
      $wpdb->prepare(
      "SELECT settings.option_value from {$wpdb->options} settings where settings.option_name = 'sendy_fulfillment_sync_products_on_add'"
      )
    );
    if ($env == "Test") {
      $edit_status = $wpdb->get_results(
        $wpdb->prepare(
          "SELECT info.meta_value, info.post_id from {$wpdb->postmeta} info where info.meta_key = 'sendy_product_id_test' and info.post_id = %d",
          $post_id
        )
      );
    } else {
      $edit_status = $wpdb->get_results(
        $wpdb->prepare(
          "SELECT info.meta_value, info.post_id from {$wpdb->postmeta} info where info.meta_key = 'sendy_product_id' and info.post_id = %d",
          $post_id
        )
      );
    }
    foreach($results as $row){  
        if ($row->post_status == "trash") {
            sendy_fulfillment_product_archive($row->id);
        } else if ($row->post_status == "publish" && $product_placement_status[0]->option_value == "1") {
          if (count($edit_status) > 0) {
            sendy_fulfillment_product_edit($row->id);
          } else {
            sendy_fulfillment_product_add($row->id);
          }
        } else if ($row->post_status == "wc-processing" && $row->post_type == "shop_order" && $order_placement_status[0]->option_value == "1") {
            sendy_fulfillment_order_sync($row->id);
        }
    }
}

function sendy_fulfillment_product_count() {
    global $wpdb;
    $results = $wpdb->get_results(
      $wpdb->prepare(
        "SELECT 
        products.ID as id,
        products.post_name as product_name, 
        products.post_content as product_description, 
        products.post_excerpt as product_variant_description
        FROM {$wpdb->posts} products 
        where products.post_type = 'product' and not products.post_title = 'AUTO-DRAFT' and not products.post_status = 'trash'"
      )
   );
    return count($results);
}
function sendy_fulfillment_synced_product_count() {
  $env = get_option("sendy_fulfillment_environment");
  $meta_key = $env == "Test" ? 'sendy_product_id_test' : 'sendy_product_id';
  global $wpdb;
  $results = $wpdb->get_results(
    $wpdb->prepare(
      "SELECT 
      products.ID as id,
      productIds.post_id,
      products.post_name as product_name, 
      products.post_content as product_description, 
      products.post_excerpt as product_variant_description
      FROM {$wpdb->posts} products 
      INNER JOIN {$wpdb->postmeta} productIds ON products.ID=productIds.post_id
      where products.post_type = 'product' and not products.post_title = 'AUTO-DRAFT' and not products.post_status = 'trash' and productIds.meta_key = %s",
      $meta_key
    )
 );
  return count($results);
}

function sendy_fulfillment_product_sync ($type) {
    $env = get_option("sendy_fulfillment_environment");
    $products = new SendyFulfillmentProduct();
    global $wpdb;
    global $woocommerce;
    $results = $wpdb->get_results(
      $wpdb->prepare(
        "SELECT 
        products.ID as id,
        products.post_name as product_name, 
        products.post_content as product_description, 
        products.post_excerpt as product_variant_description
        FROM {$wpdb->posts} products 
        where products.post_type = 'product' and not products.post_title = 'AUTO-DRAFT' and not products.post_status = 'trash'"
      )
    );
    $response = [];
    $productsArray = [];
    $env = get_option("sendy_fulfillment_environment");
    foreach($results as $row){  
      $product = wc_get_product($row->id);
      $row->product_name = $product->get_name();
      $synced = [];
      $synced_variant = [];
      if ($env == "Test") {
        $synced = $wpdb->get_results(
          $wpdb->prepare(
            "SELECT info.meta_value, info.post_id from {$wpdb->postmeta} info where info.meta_key = 'sendy_product_id_test' and info.post_id = %d",
            $row->id
          )
        );
        $synced_variant = $wpdb->get_results(
          $wpdb->prepare(
            "SELECT info.meta_value, info.post_id from {$wpdb->postmeta} info where info.meta_key = 'sendy_product_variant_id_test' and info.post_id = %d",
            $row->id
          )
        );
      } else {
        $synced = $wpdb->get_results(
          $wpdb->prepare(
            "SELECT info.meta_value, info.post_id from {$wpdb->postmeta} info where info.meta_key = 'sendy_product_id' and info.post_id = %d",
            $row->id
          )
        );
        $synced_variant = $wpdb->get_results(
          $wpdb->prepare(
            "SELECT info.meta_value, info.post_id from {$wpdb->postmeta} info where info.meta_key = 'sendy_product_variant_id' and info.post_id = %d",
            $row->id
          )
        );
      }
      $image = get_the_post_thumbnail_url($row->id);
      if ($image ==  false) {
        $image = "null";
      }
      $row->product_variant_image_link = $image;
      $row->product_variant_currency = get_woocommerce_currency(); 
      $sale_price = $wpdb->get_results(
        $wpdb->prepare(
          "SELECT info.meta_value from {$wpdb->postmeta} info where info.meta_key = '_sale_price' and info.post_id = %d",
          $row->id
        )
      );
      $regular_price = $wpdb->get_results(
        $wpdb->prepare(
          "SELECT info.meta_value from {$wpdb->postmeta} info where info.meta_key = '_regular_price' and info.post_id = %d",
          $row->id
        )
      );
      if (count($sale_price) > 0) {
        $row->product_variant_unit_price = $sale_price[0]->meta_value;
      } else if (count($regular_price) > 0) {
        $row->product_variant_unit_price = $regular_price[0]->meta_value;
      }
      $row->product_variant_quantity_type = get_option('woocommerce_weight_unit');
      $weight = $wpdb->get_results(
        $wpdb->prepare(
          "SELECT info.meta_value from {$wpdb->postmeta} info where info.meta_key = '_weight' and info.post_id = %d",
          $row->id
        )
      );
      if (count($weight) > 0) {
        $row->product_variant_quantity = $weight[0]->meta_value;
      } else {
        $row->product_variant_quantity = get_option('sendy_fulfillment_default_quantity', 'null');
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
            if ($type ==  'all') {
              $product_id = $products->sendy_fulfillment_edit($array);
              array_push($response, $product_id);
            }
          } else {
            $array = (array) $row;
            $product_id = $products->sendy_fulfillment_add($array);
            if ($product_id['product_id'] != NULL) {
              add_post_meta( $row->id, $env == "Test" ? 'sendy_product_id_test' : 'sendy_product_id', $product_id['product_id'], false );
              add_post_meta( $row->id, $env == "Test" ? 'sendy_product_variant_id_test' : 'sendy_product_variant_id', $product_id['product_variant_id'], false );
            } else {
              add_post_meta( $row->id, $env == "Test" ? 'failed_sync_test' : 'failed_sync', $array, false );
            }
            array_push($response, $product_id);
          }
      }
    }
}

function sendy_fulfillment_product_add ($post_id) {
    $products = new SendyFulfillmentProduct();
    global $wpdb;
    global $woocommerce;
    $results = $wpdb->get_results(
      $wpdb->prepare(
        "SELECT 
        products.ID as id,
        products.post_name as product_name, 
        products.post_content as product_description, 
        products.post_excerpt as product_variant_description
        FROM {$wpdb->posts} products 
        where products.ID = %d",
        $post_id
      )
    );
    $response = [];
    $productsArray = [];
    foreach($results as $row){ 
      $product = wc_get_product($row->id);
      $row->product_name = $product->get_name(); 
      $image = get_the_post_thumbnail_url($row->id);
      if ($image ==  false) {
        $image = "null";
      }
      $row->product_variant_image_link = $image;
      $row->product_variant_currency = get_woocommerce_currency(); 
      $sale_price = $wpdb->get_results(
        $wpdb->prepare(
          "SELECT info.meta_value from {$wpdb->postmeta} info where info.meta_key = '_sale_price' and info.post_id = %d",
          $row->id
        )
      );
      $regular_price = $wpdb->get_results(
        $wpdb->prepare(
          "SELECT info.meta_value from {$wpdb->postmeta} info where info.meta_key = '_regular_price' and info.post_id = %d",
          $row->id
        )
      );
      if (count($sale_price) > 0) {
        $row->product_variant_unit_price = $sale_price[0]->meta_value;
      } else if (count($regular_price) > 0) {
        $row->product_variant_unit_price = $regular_price[0]->meta_value;
      }
      $row->product_variant_quantity_type = get_option('woocommerce_weight_unit');
      $weight = $wpdb->get_results(
        $wpdb->prepare(
          "SELECT info.meta_value from {$wpdb->postmeta} info where info.meta_key = '_weight' and info.post_id = %d",
          $row->id
        )
      );
      if (count($weight) > 0) {
        $row->product_variant_quantity = $weight[0]->meta_value;
      } else {
        $row->product_variant_quantity = get_option('sendy_fulfillment_default_quantity', 'null');
      }
      if ($row->product_description == "") {
        $row->product_description = "null";
      }
      if ($row->product_variant_description == "") {
        $row->product_variant_description = "null";
      }
      $env = get_option("sendy_fulfillment_environment");
      if ($row->product_variant_unit_price) {
            $array = (array) $row;
            $product_id = $products->sendy_fulfillment_add($array);
            add_post_meta( $row->id, $env == "Test" ? 'sendy_product_id_test' : 'sendy_product_id', $product_id['product_id'], false );
            add_post_meta( $row->id, $env == "Test" ? 'sendy_product_variant_id_test' : 'sendy_product_variant_id', $product_id['product_variant_id'], false );
            array_push($response, $product_id);
      }
    }
}

function sendy_fulfillment_product_edit($post_id) {
  $products = new SendyFulfillmentProduct();
  global $wpdb;
  global $woocommerce;
  $results = $wpdb->get_results(
    $wpdb->prepare(
      "SELECT 
      products.ID as id,
      products.post_name as product_name, 
      products.post_content as product_description, 
      products.post_excerpt as product_variant_description
      FROM {$wpdb->posts} products 
      where products.ID = %d",
      $post_id
    )
  );
  $response = [];
  $productsArray = [];
  foreach($results as $row){  
    $env = get_option("sendy_fulfillment_environment");
    $product = wc_get_product($row->id);
    $row->product_name = $product->get_name();
    $synced = [];
    $synced_variant = [];
    if ($env == "Test") {
      $synced = $wpdb->get_results(
        $wpdb->prepare(
          "SELECT info.meta_value, info.post_id from {$wpdb->postmeta} info where info.meta_key = 'sendy_product_id_test' and info.post_id = %d",
          $row->id
        )
      );
      $synced_variant = $wpdb->get_results(
        $wpdb->prepare(
          "SELECT info.meta_value, info.post_id from {$wpdb->postmeta} info where info.meta_key = 'sendy_product_variant_id_test' and info.post_id = %d",
          $row->id
        )
      );
    } else {
      $synced = $wpdb->get_results(
        $wpdb->prepare(
          "SELECT info.meta_value, info.post_id from {$wpdb->postmeta} info where info.meta_key = 'sendy_product_id' and info.post_id = %d",
          $row->id
        )
      );
      $synced_variant = $wpdb->get_results(
        $wpdb->prepare(
          "SELECT info.meta_value, info.post_id from {$wpdb->postmeta} info where info.meta_key = 'sendy_product_variant_id' and info.post_id = %d",
          $row->id
        )
      );
    }
    $image = get_the_post_thumbnail_url($row->id);
    if ($image ==  false) {
      $image = "null";
    }
    $row->product_variant_image_link = $image;
    $row->product_variant_currency = get_woocommerce_currency(); 
    $sale_price = $wpdb->get_results(
      $wpdb->prepare(
        "SELECT info.meta_value from {$wpdb->postmeta} info where info.meta_key = '_sale_price' and info.post_id = %d",
        $row->id
      )
    );
    $regular_price = $wpdb->get_results(
      $wpdb->prepare(
        "SELECT info.meta_value from {$wpdb->postmeta} info where info.meta_key = '_regular_price' and info.post_id = %d",
        $row->id
      )
    );
    if (count($sale_price) > 0) {
      $row->product_variant_unit_price = $sale_price[0]->meta_value;
    } else if (count($regular_price) > 0) {
      $row->product_variant_unit_price = $regular_price[0]->meta_value;
    }
    $row->product_variant_quantity_type = get_option('woocommerce_weight_unit');
    $weight = $wpdb->get_results(
      $wpdb->prepare(
        "SELECT info.meta_value from {$wpdb->postmeta} info where info.meta_key = '_weight' and info.post_id = %d",
        $row->id
      )
    );
    if (count($weight) > 0) {
      $row->product_variant_quantity = $weight[0]->meta_value;
    } else {
      $row->product_variant_quantity = get_option('sendy_fulfillment_default_quantity', 'null');
    }
    if ($row->product_description == "") {
      $row->product_description = "null";
    }
    if ($row->product_variant_description == "") {
      $row->product_variant_description = "null";
    }
    if ($row->product_variant_unit_price) {
        $row->product_id = $synced[count($synced) - 1]->meta_value;
        $row->product_variant_id = $synced_variant[count($synced_variant) - 1]->meta_value;
        $array = (array) $row;
        $product_id = $products->sendy_fulfillment_edit($array);
        array_push($response, $product_id);
    }
  }
}

function sendy_fulfillment_product_archive($post_id) {
    $products = new SendyFulfillmentProduct();
    global $wpdb;
    global $woocommerce;
    $results = $wpdb->get_results(
      $wpdb->prepare(
        "SELECT 
        products.ID as id
        FROM {$wpdb->posts} products 
        where products.ID = %d",
        $post_id
      )
    );
    $response = [];
    $productsArray = [];
    foreach($results as $row){  
      $env = get_option("sendy_fulfillment_environment");
      $synced = [];
      $synced_variant = [];
      if ($env == "Test") {
        $synced = $wpdb->get_results(
          $wpdb->prepare(
            "SELECT info.meta_value, info.post_id from {$wpdb->postmeta} info where info.meta_key = 'sendy_product_id_test' and info.post_id = %d",
            $row->id
          )
        );
        $synced_variant = $wpdb->get_results(
          $wpdb->prepare(
            "SELECT info.meta_value, info.post_id from {$wpdb->postmeta} info where info.meta_key = 'sendy_product_variant_id_test' and info.post_id = %d",
            $row->id
          )
        );
      } else {
        $synced = $wpdb->get_results(
          $wpdb->prepare(
            "SELECT info.meta_value, info.post_id from {$wpdb->postmeta} info where info.meta_key = 'sendy_product_id' and info.post_id = %d",
            $row->id
          )
        );
        $synced_variant = $wpdb->get_results(
          $wpdb->prepare(
            "SELECT info.meta_value, info.post_id from {$wpdb->postmeta} info where info.meta_key = 'sendy_product_variant_id' and info.post_id = %d",
            $row->id
          )
        );
      }
      $row->product_id = $synced[count($synced) - 1]->meta_value;
      $array = (array) $row;
      $product_id = $products->sendy_fulfillment_archive($array);
      array_push($response, $product_id);
    }
}

function sendy_fulfillment_order_sync ($post_id) {
    $orders = new SendyFulfillmentProduct();
    global $wpdb;
    global $woocommerce;
    $results = $wpdb->get_results(
      $wpdb->prepare(
        "SELECT ID
        FROM $wpdb->posts orders where orders.ID = %d ",
        $post_id
      )
    );
    $response = [];
    $products = [];
    $env = get_option("sendy_fulfillment_environment");
    $payload = (object)[];
    foreach($results as $row){  
        $env = get_option("sendy_fulfillment_environment");
        $order = wc_get_order($row->ID);
        foreach ($order->get_items() as $item_id => $item ) {
            $product_id = $item->get_product_id();
            $sendy_products = [];
            $sendy_product_variants = [];
            if ($env == "Test") {
              $sendy_products = $wpdb->get_results(
                $wpdb->prepare(
                  "SELECT info.meta_value, info.post_id from {$wpdb->postmeta} info where info.meta_key = 'sendy_product_id_test' and info.post_id = %d",
                  $product_id
                )
              );
              $sendy_product_variants = $wpdb->get_results(
                $wpdb->prepare(
                  "SELECT info.meta_value, info.post_id from {$wpdb->postmeta} info where info.meta_key = 'sendy_product_variant_id_test' and info.post_id = %d",
                  $product_id
                )
              );
            } else {
              $sendy_products = $wpdb->get_results(
                $wpdb->prepare(
                  "SELECT info.meta_value, info.post_id from {$wpdb->postmeta} info where info.meta_key = 'sendy_product_id' and info.post_id = %d",
                  $product_id
                )
              );
              $sendy_product_variants = $wpdb->get_results(
                $wpdb->prepare(
                  "SELECT info.meta_value, info.post_id from {$wpdb->postmeta} info where info.meta_key = 'sendy_product_variant_id' and info.post_id = %d",
                  $product_id
                )
              );
            }
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
        $biz_name = get_option('sendy_fulfillment_biz_name', '');
        $biz_email = get_option("sendy_fulfillment_biz_email", '');
        $business_label = '';
        if ($biz_name != '') {
          $business_label = ' (Wordpress order by ' . $biz_name . ')';
        }
        $fName = get_post_meta($row->ID, '_billing_first_name', true);
        $lName = get_post_meta($row->ID, '_billing_last_name', true);
        $name = $fName . ' ' . $lName . $business_label;
        $destination->name = $name;
        $destination->phone_number = get_post_meta($row->ID, '_billing_phone', true);
        $destination->delivery_location = (object)[];
        $destination->delivery_location->description = WC()->session->get('customerDeliveryLocationName');
        $destination->delivery_location->longitude = WC()->session->get('customerDeliveryLocationLat');
        $destination->delivery_location->latitude = WC()->session->get('customerDeliveryLocationLong');
        $house_loc = get_post_meta($row->ID, '_billing_address_1', true) . ", " . get_post_meta($row->ID, '_billing_address_2', true);
        $destination->house_location = $house_loc;
        $notes = wc_get_order_notes(array(
          'order_id' => $row->ID,
        ));
        $get_notes = $wpdb->get_results(
          $wpdb->prepare(
            "SELECT notes.post_excerpt from {$wpdb->posts} notes where notes.ID = %d",
            $post_id
          )
        );
        $all_notes = "";
        if (count($get_notes) > 0) {
          $all_notes = $get_notes[0]->post_excerpt;
        }
        $sendy_notes = $wpdb->get_results(
          $wpdb->prepare(
            "SELECT settings.option_value from {$wpdb->options} settings where settings.option_name = 'sendy_fulfillment_delivery_info'"
          )
        );
        if (count($sendy_notes) > 0) {
          $all_notes = $all_notes .". ". $sendy_notes[0]->option_value;
        }
        $sendy_payments_on_delivery = $wpdb->get_results(
          $wpdb->prepare(
            "SELECT settings.option_value from {$wpdb->options} settings where settings.option_name = 'sendy_fulfillment_include_collect_amount'"
          )
        );
        $payment_on_delivery_status = 0;
        if (count($sendy_payments_on_delivery) > 0) {
          $payment_on_delivery_status = $sendy_payments_on_delivery[0]->option_value;
        }
        $total_amount = get_post_meta($row->ID, '_order_total', true);
        if ($payment_on_delivery_status === "1") {
          $all_notes = $all_notes .". ". "Collect " . $order->get_currency() ." ". $total_amount . " on delivery";
        }
        if ($all_notes == "") {
          $all_notes = "No notes";
        }
        $destination->delivery_instructions = $all_notes;
        $payload->business_email = $biz_email;
        $payload->products = $products;
        $payload->destination = $destination;
        $order_id = $orders->sendy_fulfillment_place_order($payload);
        add_post_meta( $post_id, $env == "Test" ? 'sendy_order_id_test' : 'sendy_order_id', $order_id->fulfilment_requests[0]->fulfilment_request_id, false );
    }
}

