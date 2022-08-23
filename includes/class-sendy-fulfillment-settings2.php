<?php
// create custom plugin settings menu
add_action('admin_menu', 'my_cool_plugin_create_menu');

function my_cool_plugin_create_menu()
{

    //create new top-level menu
    add_menu_page('My Cool Plugin Settings', 'Sendy Fulfillment', 'administrator', __FILE__, 'my_cool_plugin_settings_page', plugins_url('/images/icon.png', __FILE__));

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
<div class="wrap">
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

        <form method="post" action="options.php">
            <?php settings_fields('plugin-api-settings'); ?>
            <?php do_settings_sections('plugin-api-settings'); ?>
            <table class="form-table">
                <tr valign="top">
                <th scope="row">API Key</th>
                <td><input type="text" name="sendy_fulfillment_api_key" value="<?php echo esc_attr(get_option('sendy_fulfillment_api_key')); ?>" /></td>
                </tr>

                <tr valign="top">
                <th scope="row">API Username</th>
                <td><input type="text" name="sendy_fulfillment_api_username" value="<?php echo esc_attr(get_option('sendy_fulfillment_api_username')); ?>" /></td>
                </tr>

                <tr valign="top">
                <th scope="row">Environment</th>
                <td>
                    <?php
                        $options = get_option( 'sendy_fulfillment_environment' );
                    ?>
                    <select name='sendy_fulfillment_environment'>
                        <option value='Test' <?php selected( $options, 1 ); ?>>Test</option>
                        <option value='Live' <?php selected( $options, 2 ); ?>>Live</option>
                    </select>
                </td>
                </tr>
            </table>

            <?php submit_button(); ?>

        </form>
            <?php
    }
    elseif ($active_tab == 'inventory')
    { ?>
                <h1>Inventory Settings</h1>

<form method="post" action="options.php">
    <?php settings_fields('inventory-settings'); ?>
    <?php do_settings_sections('inventory-settings'); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Sync Products On Adding</th>
        <td><?php
        $options = get_option( 'sendy_fulfillment_sync_products_on_add' );
        $html = '<input type="checkbox" id="sendy_fulfillment_sync_products_on_add" name="sendy_fulfillment_sync_products_on_add" value="1"' . checked( 1, $options, false ) . '/>';
        echo $html;?>
        </td>
        </tr>

        <tr valign="top">
        <th scope="row">Default Currency</th>
        <td>
                    <?php
                        $options = get_option( 'sendy_fulfillment_default_currency' );
                    ?>
                    <select name='sendy_fulfillment_default_currency'>
                        <option value='KES' <?php selected( $options, 1 ); ?>>KES</option>
                    </select>
                </td>
        </tr>

        <tr valign="top">
        <th scope="row">Default Quantity Type</th>
        <td>
                    <?php
                        $options = get_option( 'sendy_fulfillment_default_quantity_type' );
                    ?>
                    <select name='sendy_fulfillment_default_quantity_type'>
                        <option value='KILOGRAM' <?php selected( $options, 1 ); ?>>KILOGRAM</option>
                        <option value='GRAM' <?php selected( $options, 2 ); ?>>GRAM</option>
                        <option value='LITRE' <?php selected( $options, 3 ); ?>>LITRE</option>
                        <option value='MILLILITRE' <?php selected( $options, 4 ); ?>>MILLILITRE</option>
                    </select>
                </td>
        </tr>

        <tr valign="top">
        <th scope="row">Default Quantity</th>
        <td><input type="text" name="sendy_fulfillment_default_quantity" value="<?php echo esc_attr(get_option('sendy_fulfillment_default_quantity')); ?>" /></td>
        </tr>
    </table>

    <?php submit_button(); ?>
</form>
<hr/>
<p>Click the button below to sync all the products</p>
<button type ="button">Sync All Product</button>
            <?php
    } elseif ($active_tab == 'orders')
    { ?>
                <h1>Order Settings</h1>

<form method="post" action="options.php">
    <?php settings_fields('order-settings'); ?>
    <?php do_settings_sections('order-settings'); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Place Order On Fulfillment</th>
        <td><?php
        $options = get_option( 'sendy_fulfillment_place_order_on_fulfillment' );
        $html = '<input type="checkbox" id="sendy_fulfillment_place_order_on_fulfillment" name="sendy_fulfillment_place_order_on_fulfillment" value="1"' . checked( 1, $options, false ) . '/>';
        echo $html;?>
        </td>
        </tr>

        <tr valign="top">
        <th scope="row">Delivery Info</th>
        <td><?php
        $options = get_option( 'sendy_fulfillment_delivery_info' );
        $html = '<textarea id="sendy_fulfillment_delivery_info" name="sendy_fulfillment_delivery_info"' . $options . '>'. $options .'</textarea>';
        echo $html;?>
        </td>
        </tr>

        <tr valign="top">
        <th scope="row">Include Tracking Link & Status</th>
        <td><?php
        $options = get_option( 'sendy_fulfillment_include_tracking' );
        $html = '<input type="checkbox" id="sendy_fulfillment_include_tracking" name="sendy_fulfillment_include_tracking" value="1"' . checked( 1, $options, false ) . '/>';
        echo $html;?>
        </td>
        </tr>

        <tr valign="top">
        <th scope="row">Include Collect Amount</th>
        <td><?php
        $options = get_option( 'sendy_fulfillment_include_collect_amount' );
        $html = '<input type="checkbox" id="sendy_fulfillment_include_collect_amount" name="sendy_fulfillment_include_collect_amount" value="1"' . checked( 1, $options, false ) . '/>';
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
