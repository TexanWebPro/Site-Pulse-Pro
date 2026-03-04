<?php
/**
 * Enqueue admin scripts and styles for Site Pulse Pro
 */

defined('ABSPATH') || exit;

add_action('admin_enqueue_scripts', 'site_pulse_pro_enqueue_admin_assets');

/**
 * Enqueue plugin assets on the Site Pulse Pro admin pages only.
 *
 * @param string $hook_suffix
 */
function site_pulse_pro_enqueue_admin_assets(string $hook_suffix): void {
    // Load assets only on our plugin dashboard page
    if ($hook_suffix !== 'toplevel_page_site-pulse-pro') {
        return;
    }

    // Styles
    wp_enqueue_style(
        'site-pulse-pro-admin-css',
        SITE_PULSE_PRO_URL . 'assets/css/admin.css',
        [],
        SITE_PULSE_PRO_VERSION
    );

    // Scripts
    wp_enqueue_script(
        'site-pulse-pro-admin-js',
        SITE_PULSE_PRO_URL . 'assets/js/admin.js',
        ['jquery'],
        SITE_PULSE_PRO_VERSION,
        true
    );
}