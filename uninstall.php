<?php
/**
 * Site Pulse Pro uninstall cleanup.
 *
 * Runs when the plugin is uninstalled (not just deactivated).
 */

defined('WP_UNINSTALL_PLUGIN') || exit;

global $wpdb;

// 1) Unschedule cron hooks created by this plugin (if any)
require_once plugin_dir_path(__FILE__) . 'includes/cron/scheduler.php';

// always stop cron
site_pulse_pro_unschedule_events();

// 2) Drop custom tables
$tables = [
    $wpdb->prefix . 'sitepulse_metrics',
    $wpdb->prefix . 'sitepulse_scans',
    $wpdb->prefix . 'sitepulse_alerts', // future milestone table; safe to include
];

foreach ($tables as $table) {
    // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
    $wpdb->query("DROP TABLE IF EXISTS `{$table}`");
}

// 3) Delete plugin options
delete_option('site_pulse_pro_db_version');

// Branding options (future milestone; safe to delete even if never set)
delete_option('sitepulse_brand_logo');
delete_option('sitepulse_brand_name');
delete_option('sitepulse_brand_color');