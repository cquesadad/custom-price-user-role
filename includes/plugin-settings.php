<?php

defined( 'ABSPATH' ) || exit;

// Función para agregar la opción de ajustes en el menú de administración
function cpur_add_plugin_settings_page() {
    add_options_page(
        'Settings Custom Price By User Role',
        'Custom Price By User Role',
        'manage_options',
        'cpur-plugin-settings',
        'cpur_render_plugin_settings_page'
    );
}
add_action('admin_menu', 'cpur_add_plugin_settings_page');

// Función para renderizar la página de ajustes del plugin
function cpur_render_plugin_settings_page() {
    ?>
    <div class="wrap">
        <h2>Settings Custom Price By User Role</h2>
        <form method="post" action="options.php">
            <?php
            settings_fields('cpur_plugin_settings');
            do_settings_sections('cpur_plugin_settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Función para registrar y agregar campos de ajustes
function cpur_register_settings() {
    register_setting(
        'cpur_plugin_settings',
        'cpur_show_hide_prices',
        'intval' // Callback de validación para asegurar que se recibe un valor entero
    );
    add_settings_section(
        'cpur_plugin_main_section',
        'Main config',
        'cpur_plugin_main_section_cb',
        'cpur_plugin_settings'
    );
    add_settings_field(
        'cpur_show_hide_prices_field',
        'Show/hide custom prices',
        'cpur_show_hide_prices_field_cb',
        'cpur_plugin_settings',
        'cpur_plugin_main_section'
    );
}
add_action('admin_init', 'cpur_register_settings');

// Función de callback para la sección principal de ajustes
function cpur_plugin_main_section_cb() {
    echo 'Selecciona si deseas mostrar u ocultar los precios por rol:';
}

// Función de callback para el campo de ajustes de mostrar/ocultar precios
function cpur_show_hide_prices_field_cb() {
    $show_hide_prices = get_option('cpur_show_hide_prices', 1); // Set default value to 1 (active)
    ?>

    <label><input type="radio" name="cpur_show_hide_prices" value="1" <?php checked($show_hide_prices, 1); ?>>Show Price by User Role</label><br>
    <label><input type="radio" name="cpur_show_hide_prices" value="0" <?php checked($show_hide_prices, 0); ?>>Hide Price by User Role</label><br>
    
    <?php
}

// // Add settings link on plugin page
// function cpur_settings_link($links) {
//     $settings_link = '<a href="options-general.php?page=cpur-plugin-settings">Settings</a>';
//     array_unshift($links, $settings_link);
//     return $links;
// }
// $plugin = plugin_basename(__FILE__);
// add_filter("plugin_action_links_$plugin", 'cpur_settings_link');

// Add settings link on plugin page
function cpur_settings_link($links) {
    // Enlace de configuración
    $settings_link = '<a href="options-general.php?page=cpur-plugin-settings">Settings</a>';
    // Agregar el enlace de configuración al inicio de la lista de enlaces
    array_unshift($links, $settings_link);
    return $links;
}
// Obtener el nombre del plugin actual
$plugin = plugin_basename(__FILE__);
// Agregar el filtro para mostrar el enlace de configuración en la página de plugins
add_filter("plugin_action_links_$plugin", 'cpur_settings_link');
