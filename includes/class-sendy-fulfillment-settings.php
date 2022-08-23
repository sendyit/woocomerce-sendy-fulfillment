<?php
function add_settings_page() {
    add_options_page( 'Sendy Fulfillment page', 'Sendy Fulfillment Menu', 'manage_options', 'sendy-fulfillment-plugin', 'render_plugin_settings_page' );
}
add_action( 'admin_menu', 'add_settings_page' );

function render_plugin_settings_page() {
    ?>
    <h2>Sendy Fulfillment Settings</h2>

     <?php
        if ( isset( $_GET ) ) {
            $active_tab = $_GET['tab'];
        } 
        ?>
        <h2 class="nav-tab-wrapper">
            <a href="?page=<?php echo $_GET['page']; ?>&tab=general" class="nav-tab <?php echo $active_tab == 'general' ? 'nav-tab-active' : ''; ?>">General</a>
            <a href="?page=<?php echo $_GET['page']; ?>&tab=inventory" class="nav-tab <?php echo $active_tab == 'inventory' ? 'nav-tab-active' : ''; ?>">Inventory</a>
            <a href="?page=<?php echo $_GET['page']; ?>&tab=orders" class="nav-tab <?php echo $active_tab == 'orders' ? 'nav-tab-active' : ''; ?>">Orders</a>
        </h2>
    <form action="options.php" method="post">
        <?php 
        if ( $active_tab == 'general' ) {
            settings_fields( 'sendy_fulfillment_options' );
            do_settings_sections( 'sendy_fulfillment' );
            ?>
            <input name="submit" class="button button-primary" type="submit" value="<?php esc_attr_e( 'Save' ); ?>" /><?php
        } elseif ( $active_tab == 'inventory' ) {
            settings_fields( 'sendy_fulfillment_inventory' );
            do_settings_sections( 'fulfillment_inventory' );
            ?>
            <input name="submit" class="button button-primary" type="submit" value="<?php esc_attr_e( 'Save' ); ?>" /><?php
        } elseif ( $active_tab == 'orders' ) {
            settings_fields( 'sendy_fulfillment_orders' );
            do_settings_sections( 'fulfillment_orders' );
            ?>
            <input name="submit" class="button button-primary" type="submit" value="<?php esc_attr_e( 'Save' ); ?>" /><?php
        }
        ?>
    </form>
    <?php
}

function register_settings() {
    register_setting( 'sendy_fulfillment_options', 'sendy_fulfillment_options', 'sendy_fulfillment_options_validate' );
    register_setting( 'sendy_fulfillment_inventory', 'sendy_fulfillment_inventory' );
    register_setting( 'sendy_fulfillment_orders', 'sendy_fulfillment_orders' );
    add_settings_section( 'api_settings', 'API Settings', '__return_false', 'sendy_fulfillment' );
    add_settings_section( 'inventory_settings', 'Inventory Settings', '__return_false', 'fulfillment_inventory' );
    add_settings_section( 'order_settings', 'Order Settings', '__return_false', 'fulfillment_orders' );
/**
 * General Settings
 */
    add_settings_field( 'plugin_setting_api_key', 'API Key', 'plugin_setting_api_key', 'sendy_fulfillment', 'api_settings' );
    add_settings_field( 'plugin_setting_api_username', 'API Username', 'plugin_setting_api_username', 'sendy_fulfillment', 'api_settings' );
    add_settings_field( 'plugin_setting_environment', 'Environment', 'plugin_setting_environment', 'sendy_fulfillment', 'api_settings' );

/**
 * inventory Settings
 */
    add_settings_field( 'plugin_setting_sync_products', 'Sync Products on Adding', 'plugin_setting_sync_products', 'fulfillment_inventory', 'inventory_settings' );
    add_settings_field( 'plugin_setting_sync_all_products', 'Sync All Products', 'plugin_setting_sync_all_products', 'fulfillment_inventory', 'inventory_settings' );
    add_settings_field( 'plugin_setting_default_currency', 'Default Currency', 'plugin_setting_default_currency', 'fulfillment_inventory', 'inventory_settings' );
    add_settings_field( 'plugin_setting_default_quantity_type', 'Default Quantity Type', 'plugin_setting_default_quantity_type', 'fulfillment_inventory', 'inventory_settings' );
    add_settings_field( 'plugin_setting_default_quantity', 'Default Quantity', 'plugin_setting_default_quantity', 'fulfillment_inventory', 'inventory_settings' );

    /**
 * orders Settings
 */
    add_settings_field( 'plugin_setting_place_order', 'Place Order On Fulfillment', 'plugin_setting_place_order', 'fulfillment_orders', 'order_settings' );
    add_settings_field( 'plugin_setting_add_delivery_note', 'Add Delivery Info', 'plugin_setting_add_delivery_note', 'fulfillment_orders', 'order_settings' );
    add_settings_field( 'plugin_setting_include_tracking_link', 'Include Tracking Link & Status', 'plugin_setting_include_tracking_link', 'fulfillment_orders', 'order_settings' );
    add_settings_field( 'plugin_setting_include_collect_amount', 'Include Collect Amount Info', 'plugin_setting_include_collect_amount', 'fulfillment_orders', 'order_settings' );
}
add_action( 'admin_init', 'register_settings' );

/**
 * General Settings
 */

function sendy_fulfillment_options_validate( $input ) {
    $newinput['api_key'] = trim( $input['api_key'] );
    if ( ! preg_match( '/^[a-z0-9]{32}$/i', $newinput['api_key'] ) ) {
        $newinput['api_key'] = '';
    }

    return $newinput;
}

function plugin_section_text() {
    echo '<p>Here you can set all the options for using the API</p>';
}

function plugin_setting_api_key() {
    $options = get_option( 'sendy_fulfillment_options' );
    echo "<input id='plugin_setting_api_key' name='sendy_fulfillment_options[api_key]' type='text' value='" . esc_attr( $options['api_key'] ) . "' />";
}

function plugin_setting_api_username() {
    $options = get_option( 'sendy_fulfillment_options' );
    echo "<input id='plugin_setting_api_username' name='sendy_fulfillment_options[api_username]' type='text' value='" . esc_attr( $options['api_username'] ) . "' />";
}

function plugin_setting_environment() {
    $options = get_option( 'sendy_fulfillment_options' );
    echo"
        <select name='sendy_fulfillment_options[environment]' id='plugin_setting_environment'>
        <option value='" . esc_attr( $options['environment'] ) . "'>Testing</option>
        <option value='" . esc_attr( $options['environment'] ) . "'>Live</option>
        </select>";
}


/**
 * Inventory Settings
 */

function inventory_section_text() {
    echo '<p>Here you can set all the options for your inventory</p>';
}

function plugin_setting_sync_products() {
    $options = get_option( 'sendy_fulfillment_inventory' );
    echo "<input id='plugin_setting_sync_products' name='sendy_fulfillment_inventory[sync_products]' type='checkbox' value='" . esc_attr( $options['sync_products'] ) . "' />";
}

function plugin_setting_sync_all_products() {
    $options = get_option( 'sendy_fulfillment_inventory' );
    echo "<button id='plugin_setting_sync_all_products' name='sendy_fulfillment_inventory[sync_all_products]' type='button' value='" . esc_attr( $options['sync_all_products'] ) . "' >Sync All Products</button>";
}

function plugin_setting_default_currency() {
    $options = get_option( 'sendy_fulfillment_inventory' );
    echo"
        <select name='sendy_fulfillment_inventory[default_currency]' id='plugin_setting_default_currency'>
        <option value='" . esc_attr( $options['default_currency'] ) . "'>KES</option>
        </select>";
}

function plugin_setting_default_quantity_type() {
    $options = get_option( 'sendy_fulfillment_inventory' );
    echo"
        <select name='sendy_fulfillment_inventory[default_quantity_type]' id='plugin_setting_default_quantity_type'>
        <option value='" . esc_attr( $options['default_quantity_type'] ) . "'>KILOGRAM</option>
        <option value='" . esc_attr( $options['default_quantity_type'] ) . "'>GRAM</option>
        <option value='" . esc_attr( $options['default_quantity_type'] ) . "'>LITRE</option>
        <option value='" . esc_attr( $options['default_quantity_type'] ) . "'>MILLILITRE</option>
        </select>";
}
function plugin_setting_default_quantity() {
    $options = get_option( 'sendy_fulfillment_inventory' );
    echo "<input id='plugin_setting_default_quantity' name='sendy_fulfillment_inventory[default_quantity]' type='text' value='" . esc_attr( $options['default_quantity'] ) . "' />";
}

/**
 * Order Settings
 */

function order_section_text() {
    echo '<p>Here you can set all the options for your orders</p>';
}

function plugin_setting_place_order() {
    $options = get_option( 'sendy_fulfillment_orders' );
    echo "<input id='plugin_setting_place_order' name='sendy_fulfillment_orders[place_order]' type='checkbox' value='" . esc_attr( $options['place_order'] ) . "' />";
}

function plugin_setting_add_delivery_note() {
    $options = get_option( 'sendy_fulfillment_orders' );
    echo "<textarea id='plugin_setting_add_delivery_note' name='sendy_fulfillment_orders[add_delivery_note]' rows='4' cols='50' value='" . esc_attr( $options['add_delivery_note'] ) . "' ></textarea>";
}

function plugin_setting_include_tracking_link() {
    $options = get_option( 'sendy_fulfillment_orders' );
    echo "<input id='plugin_setting_include_tracking_link' name='sendy_fulfillment_orders[include_tracking_link]' type='checkbox' value='" . esc_attr( $options['include_tracking_link'] ) . "' />";
}

function plugin_setting_include_collect_amount() {
    $options = get_option( 'sendy_fulfillment_orders' );
    echo "<input id='plugin_setting_include_collect_amount' name='sendy_fulfillment_orders[include_collect_amount]' type='checkbox' value='" . esc_attr( $options['include_collect_amount'] ) . "' />";
}