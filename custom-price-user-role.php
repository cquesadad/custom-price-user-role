<?php
/**
 * Plugin Name: Custom Price by User Role
 * Description: Adds custom price fields based on user role and allows updating via WooCommerce REST API.
 * Version: 1.0.1
 * Author: Carlos Quesada
 * Author URI: https://cquesada.es
 * Text Domain: custom-price-user-role
 * Requires at least: 6.5
 * Requires PHP: 7.4
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 */

defined( 'ABSPATH' ) || exit;

// Includes file for plugin settings
include_once(plugin_dir_path(__FILE__) . 'admin/plugin-settings.php');

// Include file for frontend modifications
include_once(plugin_dir_path(__FILE__) . 'public/custom-price-display.php');

function cpur_load_textdomain() {
    load_plugin_textdomain(
        'custom-price-user-role', 
        false,             
        dirname(plugin_basename(__FILE__)) . '/languages/'
    );
}
add_action('plugins_loaded', 'cpur_load_textdomain');

// Verificar si los plugins requeridos están activos
function require_members_plugin() {
    // Asegurarse de que la función is_plugin_active esté disponible
    if (!function_exists('is_plugin_active')) {
        include_once(ABSPATH . 'wp-admin/includes/plugin.php');
    }

    // Verificar si el plugin requerido está activo
    if (is_plugin_active('members/members.php')) {
        // Agrega aquí el código que depende del plugin members.php
    } else {
        // El plugin requerido no está activo, muestra un mensaje de advertencia
        add_action('admin_notices', 'require_members_plugin_notice');
    }
}
add_action('plugins_loaded', 'require_members_plugin');

// Mostrar mensaje de advertencia si el plugin requerido no está activo
function require_members_plugin_notice() { 
    ?>
    <div class="notice notice-error">
        <p><?php echo esc_html__('The plugin <b>Members</b> is required to make <b>Custom Role Based Pricing</b> work correctly. Please, activate Member Plugin.', 'custom-price-user-role'); ?></p>
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
            // Translators: %s is the role name.
            'label' => sprintf(__('Price by role: %s', 'custom-price-user-role'), $details['name']),
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
    // Verificar que el nonce es válido.
    if (!isset($_POST['crbp_custom_price_nonce']) || !wp_verify_nonce($_POST['crbp_custom_price_nonce'], 'crbp_save_custom_price_action')) {
        return; // Si el nonce no es válido, detener la ejecución.
    }
    
    // Verificar permisos de usuario (opcional, pero recomendado).
    if (!current_user_can('edit_post', $product_id)) {
        return; // Si el usuario no tiene permisos, detener la ejecución.
    }

    $editable_roles = get_editable_roles();
    foreach ($editable_roles as $role => $details) {
        $custom_price_role = isset($_POST['custom_price_' . $role]) ? $_POST['custom_price_' . $role] : '';
        update_post_meta($product_id, 'custom_price_' . $role, sanitize_text_field($custom_price_role));
    }
}
add_action('woocommerce_process_product_meta', 'crbp_save_custom_price_fields');

//Add settings link to admin page
function cpur_settings_link( $links ) {
    $settings_link = '<a href="options-general.php?page=cpur-plugin-settings">' . __( 'Settings' ) . '</a>';
    array_unshift( $links, $settings_link );
    return $links;
}
$plugin = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_$plugin", 'cpur_settings_link' );