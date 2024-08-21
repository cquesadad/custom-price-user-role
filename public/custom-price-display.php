<?php

defined( 'ABSPATH' ) || exit;

// Function to get price based on user role
function crbp_get_custom_price_by_role($product_id) {
    $user = wp_get_current_user();
    $roles = $user->roles;
    $custom_price = '';

    // Verificar si se debe mostrar el precio personalizado
    $show_custom_price = get_option('cpur_show_hide_prices', 1); // Valor predeterminado a 1 (activo)

    // Si la opción de mostrar precios está desactivada, retornar precio vacío
    if (!$show_custom_price) {
        return $custom_price;
    }

    // Verificar cada rol y obtener el precio personalizado
    foreach ($roles as $role) {
        $role_custom_price = get_post_meta($product_id, 'custom_price_' . $role, true);
        if (!empty($role_custom_price)) {
            // Si existe un precio personalizado, devolverlo
            return $role_custom_price;
        }
    }

    // If there is no custom price for rolle retun empty custom price '' 
    return $custom_price;
}

// Show custom price in products list pages and single product page
function crbp_display_custom_price($price, $product) {
    $custom_price = crbp_get_custom_price_by_role($product->get_id());

    // Verify if exist custom price
    if (!empty($custom_price)) {
        // Format custom price
        $formatted_price = wc_price($custom_price);
        // Replace regular price with custom price
        $price = '<span class="custom-price">' . $formatted_price . '</span>';
    }

    return $price;
}
add_filter('woocommerce_get_price_html', 'crbp_display_custom_price', 10, 2);

// Actualizar el precio en el carrito
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

// Filtrar el precio en el carrito de Elementor Pro
function crbp_elementor_pro_cart_item_price($price, $cart_item, $cart_item_key) {
    // Obtener el ID del producto
    $product_id = $cart_item['product_id'];
    // Obtener el precio personalizado por rol
    $custom_price = crbp_get_custom_price_by_role($product_id);
    // Verificar si hay un precio personalizado y reemplazar el precio
    if (!empty($custom_price)) {
        $price = wc_price($custom_price);
    }
    
    return $price;
}
add_filter('woocommerce_cart_item_price', 'crbp_elementor_pro_cart_item_price', 10, 3);

?>