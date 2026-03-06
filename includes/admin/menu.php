<?php
/**
 * Admin menu registration
 */

defined('ABSPATH') || exit;

// Load the dashboard controller so the callback exists.
require_once SITE_PULSE_PRO_PATH . 'includes/admin/dashboard.php';

add_action('admin_menu', 'site_pulse_pro_register_admin_menu');

/**
 * Register Site Pulse Pro admin menu.
 *
 * @return void
 */
function site_pulse_pro_register_admin_menu(): void {
    add_menu_page(
        __('Site Pulse Pro', 'site-pulse-pro'),
        __('Site Pulse Pro', 'site-pulse-pro'),
        'manage_options',
        'site-pulse-pro',
        'site_pulse_pro_render_dashboard',
        'dashicons-heart',
        58
    );
}
