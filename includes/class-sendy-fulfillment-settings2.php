<?php
// create custom plugin settings menu
add_action('admin_menu', 'my_cool_plugin_create_menu');

function my_cool_plugin_create_menu()
{

    //create new top-level menu
    add_menu_page('Sendy Fulfillment Settings', 'Sendy Fulfillment', 'administrator', __FILE__, 'my_cool_plugin_settings_page', 'dashicons-menu');

    //call register settings function
    add_action('admin_init', 'register_my_cool_plugin_settings');
}

function register_my_cool_plugin_settings()
{

    //register API settings
    register_setting('plugin-api-settings', 'sendy_fulfillment_api_key');
    register_setting('plugin-api-settings', 'sendy_fulfillment_api_username');
    register_setting('plugin-api-settings', 'sendy_fulfillment_environment');

    //register inventory settings
    register_setting('inventory-settings', 'sendy_fulfillment_sync_products_on_add');
    register_setting('inventory-settings', 'sendy_fulfillment_sync_all_products');
    register_setting('inventory-settings', 'sendy_fulfillment_default_currency');
    register_setting('inventory-settings', 'sendy_fulfillment_default_quantity_type');
    register_setting('inventory-settings', 'sendy_fulfillment_default_quantity');

    //register order settings
    register_setting('order-settings', 'sendy_fulfillment_place_order_on_fulfillment');
    register_setting('order-settings', 'sendy_fulfillment_delivery_info');
    register_setting('order-settings', 'sendy_fulfillment_include_tracking');
    register_setting('order-settings', 'sendy_fulfillment_include_collect_amount');
}

function my_cool_plugin_settings_page()
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

  padding: 10px;
  margin: 20px 20px 0px 20px;
  color: #2170b1;
  margin-bottom: 20px;
  background-color: #2271b11a;
  border: 1px solid transparent;
  border-radius: 4px;
  -webkit-box-shadow: 0 1px 1px rgb(0 0 0 / 5%);
  box-shadow: 0 1px 1px rgb(0 0 0 / 5%);
  border-color: #215db170;

}

.sendy-synch-now{ padding: 20px;}

.description-reason{ display: inline; margin-left: 30px; color:grey; font-style:italic;}

</style>


<div class="wrap">



<div>  </div>

    <?php
    if (isset($_GET))
    {
        $active_tab = $_GET['tab'];
    }
    if( strlen($active_tab) < 2 ) { $active_tab = 'general';}
?>
        <h2 class="nav-tab-wrapper">
            <a href="?page=<?php echo $_GET['page']; ?>&tab=general" class="nav-tab <?php echo $active_tab == 'general' ? 'nav-tab-active' : ''; ?>">General</a>
            <a href="?page=<?php echo $_GET['page']; ?>&tab=inventory" class="nav-tab <?php echo $active_tab == 'inventory' ? 'nav-tab-active' : ''; ?>">Inventory</a>
            <a href="?page=<?php echo $_GET['page']; ?>&tab=orders" class="nav-tab <?php echo $active_tab == 'orders' ? 'nav-tab-active' : ''; ?>">Orders</a>
        </h2>
        <?php if ($active_tab == 'general')
    { ?>
            <h1>API Settings</h1>

            <div class="sendy-top-message"> info on how to get the api </div>

        <form method="post" action="options.php">
            <?php settings_fields('plugin-api-settings'); ?>
            <?php do_settings_sections('plugin-api-settings'); ?>
            <table class="form-table">
                <tr valign="top">
                <td style="width:100px;" scope="row">API Key</td>
                <td><input class="sendy-custom-input" type="text" name="sendy_fulfillment_api_key" value="<?php echo esc_attr(get_option('sendy_fulfillment_api_key')); ?>" /></td>
                </tr>

                <tr valign="top">
                <td scope="row">API Username</td>
                <td><input class="sendy-custom-input" type="text" name="sendy_fulfillment_api_username" value="<?php echo esc_attr(get_option('sendy_fulfillment_api_username')); ?>" /></td>
                </tr>

                <tr valign="top">
                <td scope="row">Environment</td>
                <td>
                    <?php
                        $options = get_option( 'sendy_fulfillment_environment' );
                    ?>
                    <select class="sendy-custom-input" name='sendy_fulfillment_environment'>
                        <option value='Test' <?php selected( $options, 'Test' ); ?>>Test</option>
                        <option value='Live' <?php selected( $options, 'Live' ); ?>>Live</option>
                    </select>
                </td>
                </tr>
            </table>

            <?php submit_button(); ?>

        </form>

        <hr>
        <p class="sendy-synch-now"> more info on how to signup at sendy fulfullment. </p>
            <?php
    }
    elseif ($active_tab == 'inventory')
    { ?>
                <h1>Inventory Settings</h1>
                <div class="sendy-top-message"> info on how this works </div>


<form method="post" action="options.php">
    <?php settings_fields('inventory-settings'); ?>
    <?php do_settings_sections('inventory-settings'); ?>
    <table class="form-table">
        <tr valign="top">
        <td style="width:200px;" scope="row">Sync Products On Change</td>
        <td><?php
        $options = get_option( 'sendy_fulfillment_sync_products_on_add' );
        $html = '<input type="checkbox" id="sendy_fulfillment_sync_products_on_add" name="sendy_fulfillment_sync_products_on_add" value="1"' . checked( 1, $options, false ) . '/>';
        echo $html;?>
        <div class="description-reason"> More explanation on this variable </div>
        </td>
        </tr>

        <tr valign="top">
        <td scope="row">Default Currency</td>
        <td>
                    <?php
                        $options = get_option( 'sendy_fulfillment_default_currency' );
                    ?>
                    <select class="sendy-custom-input" name='sendy_fulfillment_default_currency'>
                        <option value='KES' <?php selected( $options, 'KES' ); ?>>KES</option>
                    </select>
                </td>
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
        <td><input type="text" class="sendy-custom-input" name="sendy_fulfillment_default_quantity" value="<?php echo esc_attr(get_option('sendy_fulfillment_default_quantity')); ?>" /></td>
        </tr>
    </table>

    <?php submit_button(); ?>
</form>
<hr/>
<div class="sendy-synch-now">
<p>Click the button below to sync all the products</p>
<br>
<button type ="button" class="button button-sucess">Sync All Products Now </button>
</div>
            <?php
    } elseif ($active_tab == 'orders')
    { ?>
                <h1>Order Settings</h1>

                <div class="sendy-top-message"> info on how this works </div>

<form method="post" action="options.php">
    <?php settings_fields('order-settings'); ?>
    <?php do_settings_sections('order-settings'); ?>
    <table class="form-table">
        <tr valign="top">
        <td style="width:200px;" scope="row">Place Order On Fulfillment </td>
        <td><?php
        $options = get_option( 'sendy_fulfillment_place_order_on_fulfillment' );
        $html = '<input type="checkbox" id="sendy_fulfillment_place_order_on_fulfillment" name="sendy_fulfillment_place_order_on_fulfillment" value="1"' . checked( 1, $options, false ) . '/>';
        echo $html;?>
        <div class="description-reason"> More explanation on this variable </div>
        </td>
        </tr>



        <tr valign="top">
        <td scope="row">Include Tracking Link & Status</td>
        <td><?php
        $options = get_option( 'sendy_fulfillment_include_tracking' );
        $html = '<input type="checkbox" id="sendy_fulfillment_include_tracking" name="sendy_fulfillment_include_tracking" value="1"' . checked( 1, $options, false ) . '/>';
        echo $html;?>
        <div class="description-reason"> More explanation on this variable </div>
        </td>
        </tr>

        <tr valign="top">
        <td scope="row">Include Collect Amount</td>
        <td><?php
        $options = get_option( 'sendy_fulfillment_include_collect_amount' );
        $html = '<input type="checkbox" id="sendy_fulfillment_include_collect_amount" name="sendy_fulfillment_include_collect_amount" value="1"' . checked( 1, $options, false ) . '/>';
        echo $html;?>
        <div class="description-reason"> More explanation on this variable </div>
        </td>
        </tr>

        <tr valign="top">
        <td scope="row">Delivery Info</td>
        <td><?php
        $options = get_option( 'sendy_fulfillment_delivery_info' );
        $html = '<textarea class="sendy-custom-input" id="sendy_fulfillment_delivery_info" name="sendy_fulfillment_delivery_info"' . $options . '>'. $options .'</textarea>';
        echo $html;?>
        </td>
        </tr>
    </table>

    <?php submit_button(); ?>

</form>
            <?php
    }
?>


</div>
<?php
} ?>
