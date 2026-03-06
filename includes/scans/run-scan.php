<?php
/**
 * Run a full Site Pulse scan and persist metrics.
 */

defined('ABSPATH') || exit;

require_once SITE_PULSE_PRO_PATH . 'includes/collectors/updates.php';
require_once SITE_PULSE_PRO_PATH . 'includes/collectors/uptime.php';
require_once SITE_PULSE_PRO_PATH . 'includes/collectors/backups.php';
require_once SITE_PULSE_PRO_PATH . 'includes/collectors/security.php';
require_once SITE_PULSE_PRO_PATH . 'includes/collectors/tech.php';
require_once SITE_PULSE_PRO_PATH . 'includes/database/queries.php';

/**
 * Execute a full scan and store the latest metrics.
 *
 * @return void
 */
function site_pulse_pro_run_scan(): void {
    // Updates
    $updates = site_pulse_pro_collect_updates();
    site_pulse_pro_insert_metric('core_update', (float) ($updates['core_update_needed'] ?? 0));
    site_pulse_pro_insert_metric('plugins_outdated', (float) ($updates['plugins_outdated'] ?? 0));
    site_pulse_pro_insert_metric('themes_outdated', (float) ($updates['themes_outdated'] ?? 0));

    // Uptime
    $uptime = site_pulse_pro_collect_uptime();
    site_pulse_pro_insert_metric('uptime_status', (float) ($uptime['status_code'] ?? 0));
    site_pulse_pro_insert_metric('uptime_response_time', (float) ($uptime['response_time_ms'] ?? 0));

    // Backups
    $backups = site_pulse_pro_collect_backups();
    site_pulse_pro_insert_metric('backup_detected', !empty($backups['backup_detected']) ? 1.0 : 0.0);
    error_log('Site Pulse backup_detected value: ' . (!empty($backups['backup_detected']) ? '1' : '0'));

    // Security
    $security = site_pulse_pro_collect_security();
    site_pulse_pro_insert_metric('ssl_active', !empty($security['ssl_active']) ? 1.0 : 0.0);
    site_pulse_pro_insert_metric('debug_enabled', !empty($security['debug_enabled']) ? 1.0 : 0.0);
    site_pulse_pro_insert_metric('wp_version_exposed', !empty($security['wp_version_exposed']) ? 1.0 : 0.0);
    site_pulse_pro_insert_metric('uploads_index_exists', !empty($security['uploads_index_exists']) ? 1.0 : 0.0);

    // Tech
    $tech = site_pulse_pro_collect_tech();
    site_pulse_pro_insert_metric('php_version_id', (float) ($tech['php_version_id'] ?? 0));
    site_pulse_pro_insert_metric('wp_memory_limit_bytes', (float) ($tech['wp_memory_limit_bytes'] ?? 0));
    site_pulse_pro_insert_metric('has_object_cache_dropin', !empty($tech['has_object_cache_dropin']) ? 1.0 : 0.0);
    site_pulse_pro_insert_metric('has_page_cache_dropin', !empty($tech['has_page_cache_dropin']) ? 1.0 : 0.0);
    site_pulse_pro_insert_metric('wp_cron_disabled', !empty($tech['wp_cron_disabled']) ? 1.0 : 0.0);
    site_pulse_pro_insert_metric('site_url_is_https', (($tech['site_url_scheme'] ?? '') === 'https') ? 1.0 : 0.0);
}