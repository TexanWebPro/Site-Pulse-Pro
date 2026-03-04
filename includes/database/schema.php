<?php
/**
 * Database schema creation for Site Pulse Pro
 */

defined('ABSPATH') || exit;

global $site_pulse_pro_db_version;
$site_pulse_pro_db_version = '0.1.0';

/**
 * Create tables on plugin activation
 */
function site_pulse_pro_create_tables(): void {
    global $wpdb;
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    $charset_collate = $wpdb->get_charset_collate();

    // Metrics table - time series data
    $table_metrics = $wpdb->prefix . 'sitepulse_metrics';
    $sql_metrics = "CREATE TABLE $table_metrics (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        metric_type VARCHAR(50) NOT NULL,
        metric_value FLOAT NOT NULL,
        status_code INT NULL,
        recorded_at DATETIME NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    // Scans table - aggregated results
    $table_scans = $wpdb->prefix . 'sitepulse_scans';
    $sql_scans = "CREATE TABLE $table_scans (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        pulse_score INT NOT NULL,
        updates_score INT NOT NULL,
        security_score INT NOT NULL,
        uptime_score INT NOT NULL,
        backup_score INT NOT NULL,
        tech_score INT NOT NULL,
        critical_issues INT NOT NULL,
        scan_summary LONGTEXT,
        scanned_at DATETIME NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    // Run table creation
    dbDelta($sql_metrics);
    dbDelta($sql_scans);

    add_option('site_pulse_pro_db_version', $GLOBALS['site_pulse_pro_db_version']);
}