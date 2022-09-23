<?php

function sendy_fulfillment_add_js_scripts()
    {
        wp_enqueue_script('ajax-script', plugin_dir_url(__FILE__) . '../scripts/sendy-fulfillment-locations.js', array('jquery'), '1.0', true);
        wp_localize_script('ajax-script', 'ajax_object', array('ajaxurl' => admin_url('admin-ajax.php')));

 $woocommerce_ship_to_destination = get_option('woocommerce_ship_to_destination');
 $current_user_id = get_current_user_id();

 if($woocommerce_ship_to_destination == 'billing' || $woocommerce_ship_to_destination == 'billing_only'){

   WC()->session->set( 'customerDeliveryLocationName' , get_user_meta($current_user_id,'billing_sendy_fulfillment_delivery_address')[0] );
   WC()->session->set( 'customerDeliveryLocationLat' , get_user_meta($current_user_id,'billing_sendy_fulfillment_delivery_address_lat')[0] );
   WC()->session->set( 'customerDeliveryLocationLong' , get_user_meta($current_user_id,'billing_sendy_fulfillment_delivery_address_long')[0] );

 } else if ($woocommerce_ship_to_destination == 'shipping'){

   WC()->session->set( 'customerDeliveryLocationName' , get_user_meta($current_user_id,'shipping_sendy_fulfillment_delivery_address')[0] );
   WC()->session->set( 'customerDeliveryLocationLat' , get_user_meta($current_user_id,'shipping_sendy_fulfillment_delivery_address_lat')[0] );
   WC()->session->set( 'customerDeliveryLocationLong' , get_user_meta($current_user_id,'shipping_sendy_fulfillment_delivery_address_long')[0] );

 }




    }
add_action('wp_enqueue_scripts', 'sendy_fulfillment_add_js_scripts');

add_filter( 'woocommerce_default_address_fields', 'sendy_fulfillment_add_delivery_address_field' );
function sendy_fulfillment_add_delivery_address_field( $fields ) {



	$fields[ 'sendy_fulfillment_delivery_address' ]   = array(
    'id'           => 'sendy_fulfillment_delivery_address',
		'label'        => 'Delivery Address',
		'required'     => true,
		'class'        => array( 'form-row-wide', 'my-custom-class' ),
		'priority'     => 20
	);

  $fields[ 'sendy_fulfillment_delivery_address_lat' ]   = array(
    'id'           => 'sendy_fulfillment_delivery_address_lat',
    'label'        => 'Delivery Address',
    'type'         => 'hidden',
    'required'     => true,
    'class'        => array( 'form-row-wide', 'my-custom-class' ),
    'priority'     => 20
  );

  $fields[ 'sendy_fulfillment_delivery_address_long' ]   = array(
    'id'           => 'sendy_fulfillment_delivery_address_long',
    'label'        => '',
    'type'         => 'hidden',
    'class'        => array( 'form-row-wide', 'my-custom-class' ),
    'priority'     => 20
  );

	return $fields;
}

function sendyFulfillmentSaveCustomerLocation() {
    if (isset($_POST['to_name'])) {
            $to_name = sanitize_text_field($_POST['to_name']);
            $to_lat =  sanitize_text_field($_POST['to_lat']);
            $to_long = sanitize_text_field($_POST['to_long']);

            //then update session
            WC()->session->set( 'customerDeliveryLocationName' , $to_name );
            WC()->session->set( 'customerDeliveryLocationLat' , $to_lat );
            WC()->session->set( 'customerDeliveryLocationLong' , $to_long );
        }
}

add_action('wp_ajax_nopriv_sendyFulfillmentSaveCustomerLocation', 'sendyFulfillmentSaveCustomerLocation');
add_action('wp_ajax_sendyFulfillmentSaveCustomerLocation', 'sendyFulfillmentSaveCustomerLocation');
