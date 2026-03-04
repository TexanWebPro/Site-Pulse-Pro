<?php
/**
 * Uptime collector - checks remote site responsiveness
 */

defined('ABSPATH') || exit;

/**
 * Simple uptime check.
 *
 * Returns status code and response time in ms
 *
 * @return array
 */
function site_pulse_pro_collect_uptime(): array {
    $url = home_url();

    $start = microtime(true);

    $response = wp_safe_remote_get($url, [
        'timeout' => 10,
        'redirection' => 5,
        'sslverify' => true,
    ]);

    $elapsed = (microtime(true) - $start) * 1000; // ms

    if (is_wp_error($response)) {
        return [
            'status_code' => 0,
            'response_time_ms' => null,
        ];
    }

    return [
        'status_code' => wp_remote_retrieve_response_code($response),
        'response_time_ms' => round($elapsed),
    ];
}