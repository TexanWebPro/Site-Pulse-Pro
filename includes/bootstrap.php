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

// DB + Cron core (loaded always because activation/deactivation needs them)
require_once SITE_PULSE_PRO_PATH . 'includes/database/schema.php';
require_once SITE_PULSE_PRO_PATH . 'includes/cron/scheduler.php';

/**
 * ------------------------------------------------------------
 * Activation / Deactivation Hooks
 * ------------------------------------------------------------
 */

register_activation_hook(SITE_PULSE_PRO_MAIN_FILE, 'site_pulse_pro_activate');

register_deactivation_hook(SITE_PULSE_PRO_MAIN_FILE, 'site_pulse_pro_deactivate');

/**
 * ------------------------------------------------------------
 * Lifecycle Callbacks
 * ------------------------------------------------------------
 */

function site_pulse_pro_activate(): void {
 // 1) Create / upgrade DB tables
    site_pulse_pro_create_tables();

    // 2) Schedule cron events immediately
    site_pulse_pro_schedule_events();
}

function site_pulse_pro_deactivate(): void {
    site_pulse_pro_unschedule_events();
}
