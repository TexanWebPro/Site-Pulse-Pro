<?php
/**
 * Admin menu registration
 */

defined('ABSPATH') || exit;

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

/**
 * Render the main dashboard page.
 *
 * @return void
 */
function site_pulse_pro_render_dashboard(): void {
    // Enforce permissions explicitly
    site_pulse_pro_enforce_access();

    // For Milestone 1, render a placeholder.
    // This will be replaced by real dashboard logic in Milestone 3.
    echo '<div class="wrap">';
    echo '<h1>' . esc_html__('Site Pulse Pro', 'site-pulse-pro') . '</h1>';
    echo '<p>' . esc_html__('Dashboard loading…', 'site-pulse-pro') . '</p>';
    echo '</div>';
}