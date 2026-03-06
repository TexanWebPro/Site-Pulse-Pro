<?php
/**
 * Site Pulse scoring engine
 */

defined('ABSPATH') || exit;

/**
 * Calculate Site Pulse score
 *
 * @param array $metrics
 * @return array
 */
function site_pulse_pro_calculate_score(array $metrics): array {
    $weights = require SITE_PULSE_PRO_PATH . 'includes/scoring/weights.php';

    $scores = [];

    // ---------- Updates ----------
    $update_penalty = ($metrics['core_update'] ?? 0) * 30 +
                      ($metrics['plugins_outdated'] ?? 0) * 2 +
                      ($metrics['themes_outdated'] ?? 0) * 5;
    $scores['updates'] = max(0, $weights['updates'] - $update_penalty);

    // ---------- Uptime ----------
    $http_code = $metrics['uptime_status'] ?? 0;
    $response_time = $metrics['uptime_response_time'] ?? 9999;

    $uptime_score = $weights['uptime'];
    if ($http_code !== 200) {
        $uptime_score *= 0.5;
    }
    if ($response_time > 2000) { // >2s penalty
        $uptime_score *= 0.8;
    }
    $scores['uptime'] = round($uptime_score);

    // ---------- Backups ----------
    $scores['backups'] = !empty($metrics['backup_detected']) ? $weights['backups'] : 0;

    // ---------- Security ----------
    $security_score = $weights['security'];
    if (!($metrics['ssl_active'] ?? false)) {
        $security_score *= 0.5;
    }
    if ($metrics['debug_enabled'] ?? false) {
        $security_score *= 0.5;
    }
    $scores['security'] = round($security_score);

    // ---------- Tech ----------
    $tech_score = (float) $weights['tech'];

    // Expect these keys to exist in $metrics if you store them as metrics:
    // - php_version_id (e.g. 80210 for PHP 8.2.10)
    // - wp_memory_limit_bytes (e.g. 268435456)
    // - has_object_cache_dropin (0/1)
    // - has_page_cache_dropin (0/1)
    // - wp_cron_disabled (0/1)
    // - site_url_is_https (0/1) OR reuse ssl_active from security collector

    $php_version_id = (int) ($metrics['php_version_id'] ?? 0);
    $memory_bytes   = (int) ($metrics['wp_memory_limit_bytes'] ?? 0);
    $cron_disabled  = (int) ($metrics['wp_cron_disabled'] ?? 0);

    // Conservative PHP penalty: PHP < 8.0 => meaningful risk
    if ($php_version_id > 0 && $php_version_id < 80000) {
        $tech_score *= 0.6; // strong penalty
    }

    // Memory heuristic: < 128MB is often fragile for WP + plugins
    if ($memory_bytes > 0 && $memory_bytes < (128 * 1024 * 1024)) {
        $tech_score *= 0.8;
    }

    // Cron disabled without a real cron runner is a reliability risk.
    // We can't detect a real server cron in MVP, so we only apply a mild penalty.
    if ($cron_disabled === 1) {
        $tech_score *= 0.9;
    }

    // Bonus for caching drop-ins (capacity / stability signal)
    $has_object_cache = (int) ($metrics['has_object_cache_dropin'] ?? 0);
    $has_page_cache   = (int) ($metrics['has_page_cache_dropin'] ?? 0);

    if ($has_object_cache === 1) {
        $tech_score += 1.0; // small bump, keep conservative
    }
    if ($has_page_cache === 1) {
        $tech_score += 1.0;
    }

    $scores['tech'] = (int) max(0, min($weights['tech'], round($tech_score)));

    // ---------- Total ----------
    $total_score = array_sum($scores);

    return [
        'category_scores' => $scores,
        'total_score'    => min(100, $total_score),
    ];
}