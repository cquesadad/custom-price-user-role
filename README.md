# Custom Role Based Price

Adds custom price fields based on user role and allows updating via WooCommerce REST API.

## Añadir a functions.php

```
// Función para obtener el precio personalizado según el rol de usuario
function crbp_get_custom_price_by_role($product_id) {
    $user = wp_get_current_user();
    $roles = $user->roles;
    $custom_price = '';

    // Verificar cada rol y obtener el precio personalizado correspondiente
    foreach ($roles as $role) {
        $role_custom_price = get_post_meta($product_id, 'custom_price_' . $role, true);
        if (!empty($role_custom_price)) {
            $custom_price = $role_custom_price;
            break; // Si se encuentra un precio personalizado, salir del bucle
        }
    }

    return $custom_price;
}

// Función para mostrar el precio personalizado en el frontend
function crbp_display_custom_price($price, $product) {
    $user = wp_get_current_user();
    $roles = $user->roles;

    // Verificar si el usuario tiene algún precio personalizado asignado para su rol
    foreach ($roles as $role) {
        $role_custom_price = get_post_meta($product->get_id(), 'custom_price_' . $role, true);
        if (!empty($role_custom_price)) {
            // Si se encontró un precio personalizado, mostrarlo en lugar del precio regular
            $price = wc_price($role_custom_price);
            break; // Si se encuentra un precio personalizado, salir del bucle
        }
    }

    return $price;
}
add_filter('woocommerce_get_price_html', 'crbp_display_custom_price', 10, 2);
```