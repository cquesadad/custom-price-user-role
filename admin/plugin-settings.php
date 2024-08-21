<?php

defined( 'ABSPATH' ) || exit;

// Función para agregar la opción de ajustes en el menú de administración
function cpur_add_plugin_settings_page() {
    add_options_page(
        esc_html__('Settings Custom Price By User Role', 'custom-price-user-role'), 
        esc_html__('Custom Price By User Role', 'custom-price-user-role'), 
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
        <h2><?php echo esc_html__('Custom Price by User Rol', 'custom-price-user-role'); ?></h2>
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
        esc_html__('Main configuration', 'custom-price-user-role'), 
        'cpur_plugin_main_section_cb',
        'cpur_plugin_settings'
    );
    add_settings_field(
        'cpur_show_hide_prices_field',
        esc_html__('Show/hide custom prices', 'custom-price-user-role'),
        'cpur_show_hide_prices_field_cb',
        'cpur_plugin_settings',
        'cpur_plugin_main_section'
    );
}
add_action('admin_init', 'cpur_register_settings');

// Función de callback para la sección principal de ajustes
function cpur_plugin_main_section_cb() {
    echo esc_html__('Select whether you want to show or hide prices by role:', 'custom-price-user-role');
}

// Función de callback para el campo de ajustes de mostrar/ocultar precios
function cpur_show_hide_prices_field_cb() {
    $show_hide_prices = get_option('cpur_show_hide_prices', 1); // Set default value to 1 (active)
    ?>

    <label>
        <input type="radio" name="cpur_show_hide_prices" value="1" <?php checked($show_hide_prices, 1); ?>>
        <?php echo esc_html__('Show Price by User Role', 'custom-price-user-role'); ?>
    </label>
    <br>
    <label>
        <input type="radio" name="cpur_show_hide_prices" value="0" <?php checked($show_hide_prices, 0); ?>>
        <?php echo esc_html__('Hide Price by User Role', 'custom-price-user-role'); ?>
    </label>
    <br>
    
    <?php
}