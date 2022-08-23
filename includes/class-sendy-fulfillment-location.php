<?php
add_filter( 'woocommerce_billing_fields', 'billing_location' );

function billing_location( $fields ) {
    // echo("<script>console.log('PHP: " . $fields . "');</script>");x/
}