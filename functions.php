<?php
/**
 * Thème enfant Storefront
 */
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'storefront-child-style',
        get_stylesheet_uri(),
        ['storefront-style'],
        wp_get_theme()->get('Version')
    );
});
