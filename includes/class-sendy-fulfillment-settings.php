<?php
require_once plugin_dir_path( dirname( __FILE__ ) ) . './sendyAssets/SendyFulfillment.php';

// create custom plugin settings menu
add_action('admin_menu', 'sendy_fulfillment_create_menu');

function sendy_fulfillment_create_menu()
{
    //create fulfillment menu and submenu
    add_menu_page('Sendy Fulfillment Settings', 'Sendy Fulfillment', 'edit_themes', 'sendy-fulfillment', 'sendy_fulfillment_settings_page', 'dashicons-menu', 'administrator');
    //add_submenu_page('sendy-fulfillment', 'Installation Guide', 'Installation Guide', 'edit_themes', 'sendy-fulfillment-installation-guide', 'sendy_fulfillment_submenu_settings_page');

    //call register settings function
    add_action('admin_init', 'register_sendy_fulfillment_settings');
}

function register_sendy_fulfillment_settings()
{
    //register API settings
    //register_setting('plugin-api-settings', 'sendy_fulfillment_api_key');
    register_setting('plugin-api-settings', 'sendy_fulfillment_api_username_live', function ( $value ) { return sendy_fulfillment_input_validation( $value, 'sendy_fulfillment_api_username_live', 'Sendy Fulfillment API Username' );});
    register_setting('plugin-api-settings', 'sendy_fulfillment_environment');
    register_setting('plugin-api-settings', 'sendy_fulfillment_biz_name', function ( $value ) { return sendy_fulfillment_input_validation( $value, 'sendy_fulfillment_biz_name', 'Sendy Fulfillment Business Name' );});
    register_setting('plugin-api-settings', 'sendy_fulfillment_biz_email', function ( $value ) { return sendy_fulfillment_input_validation( $value, 'sendy_fulfillment_biz_email', 'Sendy Fulfillment Business Email' );});


    //register inventory settings
    register_setting('inventory-settings', 'sendy_fulfillment_sync_products_on_add');

    register_setting('inventory-settings', 'sendy_fulfillment_default_currency');
    register_setting('inventory-settings', 'sendy_fulfillment_default_quantity_type');
    register_setting('inventory-settings', 'sendy_fulfillment_default_quantity', function ( $value ) { return sendy_fulfillment_input_validation( $value, 'sendy_fulfillment_default_quantity', 'Sendy Fulfillment Default Quantity' );});

    //register order settings
    register_setting('order-settings', 'sendy_fulfillment_place_order_on_fulfillment');
    register_setting('order-settings', 'sendy_fulfillment_delivery_info');
    register_setting('order-settings', 'sendy_fulfillment_include_tracking');
    register_setting('order-settings', 'sendy_fulfillment_include_collect_amount');
    register_setting('order-settings', 'sendy_fulfillment_pickup_address_name');
    register_setting('order-settings', 'sendy_fulfillment_pickup_address_lat');
    register_setting('order-settings', 'sendy_fulfillment_pickup_address_long');
}

function sendy_fulfillment_input_validation( $value, $field, $name ) {
    if ( empty( $value ) ) {
        $value = get_option( $field ); // ignore the user's changes and use the old database value
        add_settings_error( 'plugin-api-settings', "invalid_$field", "$name is required." );
    } else if($field === 'sendy_fulfillment_biz_email') {
        if(is_email($value)) {
            $value = $value; 
        } else {
            $value = get_option( 'sendy_fulfillment_biz_email' ); 
            add_settings_error( 'plugin-api-settings', "invalid_$field", "$name is invalid." );
        }
    } else {
        if(preg_match( '/^[a-zA-Z0-9- ]+$/', $value )) {
            $value = $value;
        } else {
            $value = get_option( $field );
            add_settings_error( 'plugin-api-settings', "invalid_$field", "$name is not acceptable." );
        }
    }

    return $value;
}

function sendy_fulfillment_submenu_settings_page()
{}

function sendy_fulfillment_add_pickup_scripts() {
    wp_enqueue_script('ajax-script', plugin_dir_url(__FILE__) . '../scripts/sendy-fulfillment-pickup-locations.js', array('jquery'), '1.0', true);
}

function sendyFulfillmentSavePickUpLocation() {
    if (get_option('sendy_fulfillment_pickup_address_name') <> get_option('sendy_fulfillment_pickup_address_name_alt') && get_option('sendy_fulfillment_environment') == 'Live') {
        $payload = array(
            'description' => get_option('sendy_fulfillment_pickup_address_name'),
            'longitude' => get_option('sendy_fulfillment_pickup_address_lat'),
            'latitude' => get_option('sendy_fulfillment_pickup_address_long'),
        );
        $array = (array) $payload;
        $migrate = new SendyFulfillmentProduct();
        $business_details = $migrate->sendy_fulfillment_save_pickup_address($array);
        if ($business_details->business) {
            delete_option('sendy_fulfillment_pickup_address_name_alt');
            add_option('sendy_fulfillment_pickup_address_name_alt', $business_details->business->business_default_address->description);
        }
    }
}

function sendy_fulfillment_migrate_account() {
    //migrate account to sales channel

    global $woocommerce;
    $WC_Countries = new WC_Countries();
    $payload = array(
        'country' => strtoupper(WC()->countries->countries[$WC_Countries->get_base_country()]),
    );
    $array = (array) $payload;
    $migrate = new SendyFulfillmentProduct();
    if (get_option('sendy_fulfillment_environment') == 'Test') {
        if (!get_option('sendy_fulfillment_sales_channel_id_test')) {
            $channel_id = $migrate->sendy_fulfillment_migrate_user_account($array);
            delete_option('sendy_fulfillment_sales_channel_id_test');
            add_option('sendy_fulfillment_sales_channel_id_test', $channel_id->channel_id);
        }
    } else {
        if (!get_option('sendy_fulfillment_sales_channel_id_live') || get_option('sendy_fulfillment_api_username_live') <> get_option('sendy_fulfillment_api_username')) {
            $channel_id = $migrate->sendy_fulfillment_migrate_user_account($array);
            delete_option('sendy_fulfillment_sales_channel_id_live');
            delete_option('sendy_fulfillment_api_username');
            add_option('sendy_fulfillment_sales_channel_id_live', $channel_id->channel_id);
            add_option('sendy_fulfillment_api_username', get_option('sendy_fulfillment_api_username_live'));
        }
    }
}

function sendy_fulfillment_settings_page()
{
?>
<!--- css block maybe help me move to a file :) -->

<style>

.sendy-custom-input{ width:330px;
  color: #333;
  background-color: #fff;
  border: 1px solid transparent;
  border-radius: 4px;
  -webkit-box-shadow: 0 1px 1px rgb(0 0 0 / 5%);
  box-shadow: 0 1px 1px rgb(0 0 0 / 5%);
  border-color: #ddd !important;}

.wrap h1{ padding: 40px 0px 0px 20px; font-weight: normal;}

.wrap form{ padding: 0px 20px 10px 20px;}

.sendy-top-message{

  padding: 10px !important;
  margin: 20px 20px 0px 20px !important;

  margin-bottom: 20px !important;


}

.lower-info-section{ padding: 20px;}

.description-reason{ display: inline; margin-left: 30px; color:grey; font-style:italic;}



</style>


<div class="wrap">



<div> <?php settings_errors(); ?></div>

    <?php
    if (isset($_GET))
    {
        $active_tab = sanitize_key($_GET['tab']);
    }
    if( strlen($active_tab) < 2 ) { $active_tab = 'general';}
?>
        <h2 class="nav-tab-wrapper">
            <a href="?page=<?php echo esc_html($_GET['page']); ?>&tab=general" class="nav-tab <?php echo $active_tab == 'general' ? 'nav-tab-active' : ''; ?>"> API Settings</a>
            <a href="?page=<?php echo esc_html($_GET['page']); ?>&tab=inventory" class="nav-tab <?php echo $active_tab == 'inventory' ? 'nav-tab-active' : ''; ?>">Inventory Settings</a>
            <a href="?page=<?php echo esc_html($_GET['page']); ?>&tab=orders" class="nav-tab <?php echo $active_tab == 'orders' ? 'nav-tab-active' : ''; ?>">Orders Settings</a>
            <a href="?page=<?php echo esc_html($_GET['page']); ?>&tab=Faqs" class="nav-tab <?php echo $active_tab == 'Faqs' ? 'nav-tab-active' : ''; ?>">Faqs</a>
        </h2>
        <?php if ($active_tab == 'general')
    { ?>
            <h1>API Settings</h1>







            <div class="notice inline sendy-top-message notice-info">
	<p>
    These configurations enable us to uniquely identify and authenticate the requests made from your woocommerce store. To get the API key and API username  for your business, send an email request to merchantapi@sendyit.com
</p><p>
You can test the plugin without an API-key but don’t forget to configure your API key and switch to production once testing is done.
</p><p>
You can use any business name but we advise you to use the same name as on the Sendy Fulfilment app to avoid confusion.
	</p>


</div>



        <form method="post" action="options.php">
            <?php settings_fields('plugin-api-settings'); ?>
            <?php
                do_settings_sections('plugin-api-settings');
                if (get_option('sendy_fulfillment_api_username_live')) {
                    sendy_fulfillment_migrate_account();
                }
            ?>
            <table class="form-table">
              <tr valign="top">
              <td style="width:100px;" scope="row">Business Name</td>
              <td><input class="sendy-custom-input" type="text" id="sendy_fulfillment_biz_name" name="sendy_fulfillment_biz_name" value="<?php echo esc_attr(get_option('sendy_fulfillment_biz_name')); ?>" /></td>
              </tr>
                <tr valign="top">
                    <td style="width:100px;" scope="row">Business Email</td>
                    <td><input class="sendy-custom-input" type="text" id="sendy_fulfillment_biz_email" name="sendy_fulfillment_biz_email" value="<?php echo esc_attr(get_option('sendy_fulfillment_biz_email')); ?>" /></td>
                </tr>

                <tr valign="top">
                <td scope="row">API Username</td>
                <td><input class="sendy-custom-input" type="text" id="sendy_fulfillment_api_username_live" name="sendy_fulfillment_api_username_live" value="<?php echo esc_attr(get_option('sendy_fulfillment_api_username_live')); ?>" /></td>
                </tr>

                <tr valign="top">
                <td scope="row">Environment</td>
                <td>
                    <?php
                        $options = get_option( 'sendy_fulfillment_environment' );
                    ?>
                    <select class="sendy-custom-input" name='sendy_fulfillment_environment' <?php echo $_SERVER['HTTP_HOST'] == 'https://demo.sendyit.com/' ? 'disabled' : '';?>>
                        <option value='Test' <?php selected( $options, 'Test' ); ?>>Test</option>
                        <option value='Live' <?php selected( $options, 'Live' ); ?>>Live</option>
                    </select>
                </td>
              </tr>
            </table>

            <?php submit_button(); ?>

        </form>

        <hr>
        <p class="lower-info-section"> Create an account at
          <a type="_blank" href="https://fulfillment.sendyit.com/auth/sign-up">sendy fulfillment</a> and
          send us an email on <a href="mailto:merchantapi@sendyit.com">merchantapi@sendyit.com </a>
          to receive an api key and username
         </p>
            <?php
    }
    elseif ($active_tab == 'inventory')
    { ?>
                <h1>Inventory Settings</h1>
                <div class="notice inline sendy-top-message notice-info"> <p>

During delivery and pickup, Sendy Fulfilment needs to know the product weight or capacity for delivery consolidation purposes. We also need to know the price of each product for insurance purposes.
                 </p></div>


<form method="post" action="options.php">
    <?php settings_fields('inventory-settings'); ?>
    <?php do_settings_sections('inventory-settings'); ?>
    <table class="form-table">
        <tr valign="top">
        <td style="width:200px;" scope="row">Sync Products On Change</td>
        <td><?php
        $options = get_option( 'sendy_fulfillment_sync_products_on_add' );?>
        <input type="checkbox" id="sendy_fulfillment_sync_products_on_add" name="sendy_fulfillment_sync_products_on_add" value="1" <?php echo esc_html(checked( 1, $options, false ))?>/>
        <div class="description-reason"> When this is checked your inventory will synch automatically when you update
        your products </div>
        </td>
        </tr>

        <tr valign="top">
        <td scope="row">Default Currency</td>
        <td><input disabled type="text" class="sendy-custom-input" id="sendy_fulfillment_default_currency" name="sendy_fulfillment_default_currency" value="<?php echo esc_attr(get_option('woocommerce_currency')); ?>" /></td>
        </tr>

        <tr valign="top">
        <td scope="row">Default Quantity Type</td>
        <td>
                    <?php
                        $options = get_option( 'sendy_fulfillment_default_quantity_type' );
                    ?>
                    <select name='sendy_fulfillment_default_quantity_type' class="sendy-custom-input">
                        <option value='KILOGRAM' <?php selected( $options, 'KILOGRAM' ); ?>>KILOGRAM</option>
                        <option value='GRAM' <?php selected( $options, 'GRAM' ); ?>>GRAM</option>
                        <option value='LITRE' <?php selected( $options, 'LITRE' ); ?>>LITRE</option>
                        <option value='MILLILITRE' <?php selected( $options, 'MILLILITRE' ); ?>>MILLILITRE</option>
                    </select>
                </td>
        </tr>

        <tr valign="top">
        <td scope="row">Default Quantity</td>
        <td><input type="text" class="sendy-custom-input" id="sendy_fulfillment_default_quantity" name="sendy_fulfillment_default_quantity" value="<?php echo esc_attr(get_option('sendy_fulfillment_default_quantity')); ?>" /></td>
        </tr>
    </table>

    <?php submit_button(); ?>
</form>
<hr/>
<div style="margin-left: 20px;">
    <p>Click the button below to sync all the products</p>
</div>

  <?php
  $migration = (get_option('sendy_fulfillment_environment') == 'Test' && get_option('sendy_fulfillment_sales_channel_id_test')) || (get_option('sendy_fulfillment_environment') == 'Live' && get_option('sendy_fulfillment_sales_channel_id_live'));
    if (isset($_POST['sync_all_products'])) {
        if ($migration) {
            sendy_fulfillment_product_sync('all');
        } else {
            echo '<script>alert("Looks like your account has not been migrated to a sales channel. Kindly enter your API username in the API settings page and click on save.")</script>';
        }
    }
    if (isset($_POST['sync_remaining_products'])) {
        if ($migration) {
            sendy_fulfillment_product_sync('remaining');
        } else {
            echo '<script>alert("Looks like your account has not been migrated to a sales channel. Kindly enter your API username in the API settings page and click on save.")</script>';
        }
    }
    
  ?>
  <p style="margin-left: 20px;">
      <?php 
    if (sendy_fulfillment_synced_product_count() <> sendy_fulfillment_product_count()) {
        echo 'You have synced ' . sendy_fulfillment_synced_product_count() . '/' . sendy_fulfillment_product_count() . ' products';
    } else if (sendy_fulfillment_synced_product_count() == sendy_fulfillment_product_count() && sendy_fulfillment_product_count() <> 0) {
        echo 'You have synced all ' . sendy_fulfillment_product_count() . ' products';
    } else if (sendy_fulfillment_synced_product_count() <> sendy_fulfillment_product_count() && sendy_fulfillment_synced_product_count() == 0) {
        echo 'You haven`t synced your products';
    }
    ?>
    </p>
  <div style="display: flex;">
  <form method="post">
    <input class="button button-sucess" type="button" id="sync_all_products" onclick="document.getElementById('sync_all_products').value = 'Syncing All Products'; document.getElementById('sync_all_products').setAttribute('disabled', ''); document.getElementById('sync_all_products_hidden').click();" value="Sync All Products">
    <input class="button button-sucess" style="display: none;" id="sync_all_products_hidden" type="submit" name="sync_all_products" value="Sync all products">
  </form>
  <?php
  if (sendy_fulfillment_synced_product_count() <> sendy_fulfillment_product_count() && sendy_fulfillment_product_count() <> 0) {
    ?>
        <form method="post">
            <input class="button button-sucess" type="button" id="sync_remaining_products" onclick="document.getElementById('sync_remaining_products').value = 'Syncing Remaining Products Only'; document.getElementById('sync_remaining_products').setAttribute('disabled', ''); document.getElementById('sync_remaining_products_hidden').click();" value="Sync Remaining Products Only">
            <input class="button button-sucess" style="display: none;" id="sync_remaining_products_hidden" type="submit" name="sync_remaining_products" value="Sync remaining products">
        </form>
    </div>
    <?php
  }
  ?>

            <?php
    } elseif ($active_tab == 'orders')
    { ?>
                <h1>Order Settings</h1>

                <div style="display:none;" class="notice inline sendy-top-message notice-info"> <p>info on how this works</p> </div>

<form method="post" action="options.php">
    <?php settings_fields('order-settings'); ?>
    <?php do_settings_sections('order-settings'); ?>
    <?php 
        sendy_fulfillment_add_pickup_scripts();
        sendyFulfillmentSavePickUpLocation();
    ?>
    <table class="form-table">
        <tr valign="top">
        <td style="width:200px;" scope="row">Place Order On Fulfillment </td>
        <td><?php
        $options = get_option( 'sendy_fulfillment_place_order_on_fulfillment' );?>
        <input type="checkbox" id="sendy_fulfillment_place_order_on_fulfillment" name="sendy_fulfillment_place_order_on_fulfillment" value="1" <?php echo esc_html(checked( 1, $options, false ))?>/>
        <div class="description-reason"> When checked, a fulfillment order will be placed once a customer places
        an order on your e-commerce site </div>
        </td>
        </tr>



        <tr valign="top">
        <td scope="row">Include Tracking Link & Status</td>
        <td><?php
        $options = get_option( 'sendy_fulfillment_include_tracking' );?>
        <input type="checkbox" id="sendy_fulfillment_include_tracking" name="sendy_fulfillment_include_tracking" value="1" <?php echo esc_html(checked( 1, $options, false ))?>/>
        <div class="description-reason"> When checked tracking link and status will be added to the order received page </div>
        </td>
        </tr>

        <tr valign="top">
        <td scope="row">Include Collect Amount</td>
        <td><?php
        $options = get_option( 'sendy_fulfillment_include_collect_amount' );?>
        <input type="checkbox" id="sendy_fulfillment_include_collect_amount" name="sendy_fulfillment_include_collect_amount" value="1" <?php echo esc_html(checked( 1, $options, false ))?>/>
        <div class="description-reason"> When checked the rider will receive a note indicating the amount to collect from
        the customer </div>
        </td>
        </tr>

        <tr valign="top">
        <td scope="row">Pick Up Address </td>
        <td>
            <input type="text" style="width: 500px; height: 40px; border-color: #dddddd;" id="sendy_fulfillment_pickup_address" class="form-row-wide my-custom-class" name="sendy_fulfillment_pickup_address_name" value="<?php echo esc_attr(get_option('sendy_fulfillment_pickup_address_name')); ?>" />
            <input type="text" style="display: none;" id="sendy_fulfillment_pickup_address_lat" label="Pick up latitude" class="form-row-wide my-custom-class" name="sendy_fulfillment_pickup_address_lat" value="<?php echo esc_attr(get_option('sendy_fulfillment_pickup_address_lat')); ?>" />
            <input type="text" style="display: none;" id="sendy_fulfillment_pickup_address_long" label="Pick up longitude" class="form-row-wide my-custom-class" name="sendy_fulfillment_pickup_address_long" value="<?php echo esc_attr(get_option('sendy_fulfillment_pickup_address_long')); ?>" />
        </td>
        </tr>

        <tr valign="top">
        <td scope="row">Additional Delivery Info (optional) </td>
        <td><?php
        $options = get_option( 'sendy_fulfillment_delivery_info' );?>
        <textarea class="sendy-custom-input" style="width:500px" id="sendy_fulfillment_delivery_info" name="sendy_fulfillment_delivery_info" <?php echo esc_html($options)?>><?php echo esc_html($options) ?></textarea>
        </td>
        </tr>
    </table>

    <?php submit_button(); ?>

</form>

<hr>
<p class="lower-info-section">  </p>
            <?php
    } elseif ($active_tab == 'Faqs' || $active_tab == 'faqs'){  include_once 'pages/faq.php'; }
?>


</div>
<?php
} ?>
