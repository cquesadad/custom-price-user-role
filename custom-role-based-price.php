<?php
/**
 * Plugin Name: Custom Role Based Price
 * Description: Adds custom price fields based on user role and allows updating via WooCommerce REST API.
 * Version: 1.0
 * Author: Carlos Quesada
 */

defined( 'ABSPATH' ) || exit;

// Verify if required plugins are active
function mi_plugin_check_required_plugin() {
    // Check if requiered plugin is active
    if (is_plugin_active('members/members.php')) {
        // Add here code that depends on members.php
    } else {
        // El plugin requerido no está activo, muestra un mensaje de advertencia
        add_action('admin_notices', 'mi_plugin_missing_required_plugin_notice');
    }
}
add_action('plugins_loaded', 'mi_plugin_check_required_plugin');

// Show warning message
function mi_plugin_missing_required_plugin_notice() {
    ?>
    <div class="notice notice-error">
        <p>The plugin <b>Members</b> is required to make <b>Custom Role Based Pricing</b> work correctly. Please, activate Member Plugin.</p>
    </div>
    <?php
}

// Register custom price fields on product page.
function crbp_add_custom_price_fields() {
    global $product;
    
    // Obtener todos los roles de usuario
    $editable_roles = get_editable_roles();
    foreach ($editable_roles as $role => $details) {
        // Mostrar campos de precio específicos para cada rol de usuario
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


// Allow updating custom price fields via WooCommerce REST API.
// function crbp_update_custom_price_fields_via_rest($data, $post, $request) {
//     if (isset($data['id'])) {
//         $product_id = $data['id'];
//         $editable_roles = get_editable_roles();
//         foreach ($editable_roles as $role => $details) {
//             if (isset($data['custom_price_' . $role])) {
//                 $custom_price_role = $data['custom_price_' . $role];
//                 update_post_meta($product_id, 'custom_price_' . $role, sanitize_text_field($custom_price_role));
//             }
//         }
//     }
//     return $data;
// }
// add_filter('woocommerce_rest_prepare_product_object', 'crbp_update_custom_price_fields_via_rest', 10, 3);
