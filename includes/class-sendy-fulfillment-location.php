<?php

function add_js_scripts()
    {
        wp_enqueue_script('ajax-script', plugin_dir_url(__FILE__) . '../scripts/sendy-fulfillment-locations.js', array('jquery'), '1.0', true);
        wp_localize_script('ajax-script', 'ajax_object', array('ajaxurl' => admin_url('admin-ajax.php')));
    }
add_action('wp_enqueue_scripts', 'add_js_scripts');

add_filter( 'woocommerce_default_address_fields', 'add__delivery_address_field' );
function add__delivery_address_field( $fields ) {
	
	$fields[ 'sendy_fulfillment_delivery_address' ]   = array(
        'id'           => 'sendy_fulfillment_delivery_address',
		'label'        => 'Sendy Fulfillment Delivery Address',
		'required'     => true,
		'class'        => array( 'form-row-wide', 'my-custom-class' ),
		'priority'     => 20,
		'placeholder'  => 'Enter a delivery address',
	);
	
	return $fields;
}

function saveCustomerLocation() {
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

add_action('wp_ajax_nopriv_saveCustomerLocation', 'saveCustomerLocation');
add_action('wp_ajax_saveCustomerLocation', 'saveCustomerLocation');