<?php
/**
 * Permissions helpers
 */

defined('ABSPATH') || exit;

/**
 * Check whether the current user can access Site Pulse Pro.
 *
 * @return bool
 */
function site_pulse_pro_user_can_access(): bool {
    return current_user_can('manage_options');
}

/**
 * Enforce access control.
 * Call this at the top of admin pages.
 *
 * @return void
 */
function site_pulse_pro_enforce_access(): void {
    if (!site_pulse_pro_user_can_access()) {
        wp_die(
            esc_html__('You do not have permission to access this page.', 'site-pulse-pro'),
            esc_html__('Access Denied', 'site-pulse-pro'),
            [
                'response' => 403,
            ]
        );
    }
}