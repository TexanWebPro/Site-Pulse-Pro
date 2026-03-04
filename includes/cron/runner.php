<?php
/**
 * Cron runner - execute periodic scans
 */

defined('ABSPATH') || exit;

add_action('site_pulse_pro_cron_hook', 'site_pulse_pro_run_cron');

function site_pulse_pro_run_cron(): void {
    // Collect updates
    $updates = site_pulse_pro_collect_updates();

    // Store metrics
    site_pulse_pro_insert_metric('core_update', (float) $updates['core_update_needed']);
    site_pulse_pro_insert_metric('plugins_outdated', (float) $updates['plugins_outdated']);
    site_pulse_pro_insert_metric('themes_outdated', (float) $updates['themes_outdated']);

    // Uptime
    $uptime = site_pulse_pro_collect_uptime();
    site_pulse_pro_insert_metric('uptime_status', (float) $uptime['status_code']);
    site_pulse_pro_insert_metric('uptime_response_time', (float) ($uptime['response_time_ms'] ?? 0));

    // Backups
    $backups = site_pulse_pro_collect_backups();
    site_pulse_pro_insert_metric('backup_detected', $backups['backup_detected'] ? 1 : 0);

    // Security
    $security = site_pulse_pro_collect_security();
    site_pulse_pro_insert_metric('ssl_active', $security['ssl_active'] ? 1 : 0);
    site_pulse_pro_insert_metric('debug_enabled', $security['debug_enabled'] ? 1 : 0);
}