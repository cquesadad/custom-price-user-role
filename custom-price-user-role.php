<?php
/**
 * Plugin Name: Custom Price by User Role
 * Description: Adds custom price fields based on user role and allows updating via WooCommerce REST API.
 * Version: 1.0
 * Author: Carlos Quesada
 * Author URI: https://cquesada.es
 * Text Domain: custom-price-user-role
 */

defined( 'ABSPATH' ) || exit;

// Includes file for plugin settings
include_once(plugin_dir_path(__FILE__) . 'includes/plugin-settings.php');

// Include file for frontend modifications
include_once(plugin_dir_path(__FILE__) . 'public/custom-price-display.php');

// Verify if required plugins are active
function require_members_plugin() {
    // Check if requiered plugin is active
    if (is_plugin_active('members/members.php')) {
        // Add here code that depends on members.php
    } else {
        // El plugin requerido no está activo, muestra un mensaje de advertencia
        add_action('admin_notices', 'require_members_plugin_notice');
    }
}
add_action('plugins_loaded', 'require_members_plugin');

// Show warning message
function require_members_plugin_notice() { 
    ?>
    <div class="notice notice-error">
        <p>The plugin <b>Members</b> is required to make <b>Custom Role Based Pricing</b> work correctly. Please, activate Member Plugin.</p>
    </div>
    <?php 
} 

// Register custom price fields on product page.
function crbp_add_custom_price_fields() {
    global $product;
    
    // Get all user roles
    $editable_roles = get_editable_roles();
    foreach ($editable_roles as $role => $details) {
        // Show price field for each user role 
        woocommerce_wp_text_input(array(
            'id' => 'custom_price_' . $role,
            'class' => 'short',
            'label' => __('Precio para rol ' . $details['name'], 'woocommerce'),
            'type' => 'number',
            'custom_attributes' => array(
                'step' => 'any',
                'min' => '0'
            )
        ));
    }
}
add_action('woocommerce_product_options_pricing', 'crbp_add_custom_price_fields');

// Save custom price fields data.
function crbp_save_custom_price_fields($product_id) {
    $editable_roles = get_editable_roles();
    foreach ($editable_roles as $role => $details) {
        $custom_price_role = isset($_POST['custom_price_' . $role]) ? $_POST['custom_price_' . $role] : '';
        update_post_meta($product_id, 'custom_price_' . $role, sanitize_text_field($custom_price_role));
    }
}
add_action('woocommerce_process_product_meta', 'crbp_save_custom_price_fields');

// Update cart page price
function crbp_update_cart_item_price($cart_object) {
    foreach ($cart_object->get_cart() as $cart_item_key => $cart_item) {
        $product_id = $cart_item['product_id'];
        $custom_price = crbp_get_custom_price_by_role($product_id);
        if (!empty($custom_price)) {
            $cart_item['data']->set_price($custom_price);
        }
    }
}
add_action('woocommerce_before_calculate_totals', 'crbp_update_cart_item_price');

//Add settings link to admin page
function cpur_settings_link( $links ) {
    $settings_link = '<a href="options-general.php?page=cpur-plugin-settings">' . __( 'Settings' ) . '</a>';
    array_unshift( $links, $settings_link );
    return $links;
}
$plugin = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_$plugin", 'cpur_settings_link' );