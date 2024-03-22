<?php

defined( 'ABSPATH' ) || exit;

// Function to get price based on user role
function crbp_get_custom_price_by_role($product_id) {
    $user = wp_get_current_user();
    $roles = $user->roles;
    $custom_price = '';

    // VErify each role and get custom price 
    foreach ($roles as $role) {
        $role_custom_price = get_post_meta($product_id, 'custom_price_' . $role, true);
        if (!empty($role_custom_price)) {
            // If exist custom price return it
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

?>