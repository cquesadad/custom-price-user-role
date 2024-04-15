<?php

defined( 'ABSPATH' ) || exit;

// Function to get price based on user role
function crbp_get_custom_price_by_role($product_id) {
    $user = wp_get_current_user();
    $roles = $user->roles;
    $custom_price = '';

    // Verify if it needs to show custom price
    $show_custom_price = get_option('cpur_show_hide_prices', 1); // Valor predeterminado a 1 (activo)

    // Si la opción de mostrar precios está desactivada, retornar precio vacío
    if (!$show_custom_price) {
        return $custom_price;
    }

    // Verify each role and show the price
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
        // Get regular price
        $regular_price = $product->get_regular_price();
        // Format regular and custom prices
        $regular_price_formatted = wc_price($regular_price);
        $custom_price_formatted = wc_price($custom_price);
        
        // Check if discount prices should be shown
        $show_discount_prices = get_option('cpur_show_discount_prices', 0);
        
        // If discount prices should be shown, display regular price struck through and custom price
        if ($show_discount_prices) {
            $price = '<del>' . $regular_price_formatted . '</del> <span class="custom-price">' . $custom_price_formatted . '</span>';
        } else {
            // If not, just display custom price
            $price = '<span class="custom-price">' . $custom_price_formatted . '</span>';
        }
    }

    return $price;
}
add_filter('woocommerce_get_price_html', 'crbp_display_custom_price', 10, 2);

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

// Show custom price in mini cart
function crbp_elementor_pro_cart_item_price($price, $cart_item, $cart_item_key) {
    // Get Product ID
    $product_id = $cart_item['product_id'];

    // Get product object
    $product = wc_get_product($product_id);
    
    // Get regular price
    $regular_price = $product->get_regular_price();   
    // Get custom price
    $custom_price = crbp_get_custom_price_by_role($product_id);

    // Format regular and custom prices
    $regular_price_formatted = wc_price($regular_price);
    $custom_price_formatted = wc_price($custom_price);

    // Verify if there is custom price
    if (!empty($custom_price)) {
        //$price = wc_price($custom_price);

        // Check if discount prices should be shown
        $show_discount_prices = get_option('cpur_show_discount_prices', 0);

        // If discount prices should be shown, display regular price struck through and custom price
        if ($show_discount_prices) {
            $price = '<del>' . $regular_price_formatted . '</del> <span class="custom-price">' . $custom_price_formatted . '</span>';
        } else {
            // If not, just display custom price
            $price = '<span class="custom-price">' . $custom_price_formatted . '</span>';
        }
    }
    
    return $price;
}
add_filter('woocommerce_cart_item_price', 'crbp_elementor_pro_cart_item_price', 10, 3);

?>