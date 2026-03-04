<?php
/**
 * DB helper functions
 */

defined('ABSPATH') || exit;

global $wpdb;

function site_pulse_pro_insert_metric(string $type, float $value, ?int $status_code = null): void {
    global $wpdb;
    $wpdb->insert(
        $wpdb->prefix . 'sitepulse_metrics',
        [
            'metric_type' => $type,
            'metric_value' => $value,
            'status_code' => $status_code,
            'recorded_at' => current_time('mysql', 1),
        ],
        ['%s', '%f', '%d', '%s']
    );
}

function site_pulse_pro_insert_scan(array $scan_data): void {
    global $wpdb;
    $wpdb->insert(
        $wpdb->prefix . 'sitepulse_scans',
        array_merge(
            $scan_data,
            ['scanned_at' => current_time('mysql', 1)]
        )
    );
}