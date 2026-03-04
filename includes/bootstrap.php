<?php
/**
 * Plugin bootstrap file
 */

defined('ABSPATH') || exit;

/**
 * ------------------------------------------------------------
 * Constants
 * ------------------------------------------------------------
 */

define('SITE_PULSE_PRO_VERSION', '0.1.0');
define('SITE_PULSE_PRO_PATH', plugin_dir_path(__DIR__));
define('SITE_PULSE_PRO_URL', plugin_dir_url(__DIR__));
define('SITE_PULSE_PRO_BASENAME', plugin_basename(dirname(__DIR__)));

/**
 * ------------------------------------------------------------
 * Core Includes
 * ------------------------------------------------------------
 */

// Global constants
require_once SITE_PULSE_PRO_PATH . 'includes/constants.php';

// Utilities
require_once SITE_PULSE_PRO_PATH . 'includes/utils/permissions.php';

// Admin
if (is_admin()) {
    require_once SITE_PULSE_PRO_PATH . 'includes/admin/menu.php';
    require_once SITE_PULSE_PRO_PATH . 'includes/admin/assets.php';
}

/**
 * ------------------------------------------------------------
 * Activation / Deactivation Hooks
 * ------------------------------------------------------------
 */

register_activation_hook(
    SITE_PULSE_PRO_PATH . 'site-pulse-pro.php',
    'site_pulse_pro_activate'
);

register_deactivation_hook(
    SITE_PULSE_PRO_PATH . 'site-pulse-pro.php',
    'site_pulse_pro_deactivate'
);

/**
 * ------------------------------------------------------------
 * Lifecycle Callbacks
 * ------------------------------------------------------------
 */

function site_pulse_pro_activate(): void {
    // Reserved for:
    // - DB table creation
    // - Cron registration
}

function site_pulse_pro_deactivate(): void {
    // Reserved for:
    // - Cron unscheduling
}