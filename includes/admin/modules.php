<?php
/**
 * Dashboard module presentation helpers
 */

defined('ABSPATH') || exit;

/**
 * Build dashboard module data from metrics and scores.
 *
 * @param array $metrics
 * @param array $categories
 * @param array $weights
 * @return array
 */
function site_pulse_pro_build_modules(array $metrics, array $categories, array $weights): array {
    return [
        'updates' => site_pulse_pro_build_updates_module($metrics, $categories['updates'] ?? 0, $weights['updates'] ?? 0),
        'uptime' => site_pulse_pro_build_uptime_module($metrics, $categories['uptime'] ?? 0, $weights['uptime'] ?? 0),
        'backups' => site_pulse_pro_build_backups_module($metrics, $categories['backups'] ?? 0, $weights['backups'] ?? 0),
        'security' => site_pulse_pro_build_security_module($metrics, $categories['security'] ?? 0, $weights['security'] ?? 0),
        'tech' => site_pulse_pro_build_tech_module($metrics, $categories['tech'] ?? 0, $weights['tech'] ?? 0),
    ];
}

/**
 * Determine status label from score percentage.
 *
 * @param int $score
 * @param int $max
 * @return string
 */
function site_pulse_pro_score_status(int $score, int $max): string {
    if ($max <= 0) {
        return 'Unknown';
    }

    $pct = (int) round(($score / $max) * 100);

    if ($pct >= 80) {
        return 'Healthy';
    }

    if ($pct >= 50) {
        return 'At Risk';
    }

    return 'Critical';
}

function site_pulse_pro_build_updates_module(array $metrics, int $score, int $max): array {
    $issues = [];

    $core_update = (int) ($metrics['core_update'] ?? 0);
    $plugins_outdated = (int) ($metrics['plugins_outdated'] ?? 0);
    $themes_outdated = (int) ($metrics['themes_outdated'] ?? 0);

    if ($core_update > 0) {
        $issues[] = 'A WordPress core update is available.';
    }

    if ($plugins_outdated > 0) {
        $issues[] = sprintf('%d plugin(s) are outdated.', $plugins_outdated);
    }

    if ($themes_outdated > 0) {
        $issues[] = sprintf('%d theme(s) are outdated.', $themes_outdated);
    }

    if (empty($issues)) {
        $issues[] = 'No update issues detected.';
    }

    return [
        'title' => 'Updates',
        'score' => $score,
        'max' => $max,
        'status' => site_pulse_pro_score_status($score, $max),
        'issues' => $issues,
        'why_it_matters' => 'Outdated WordPress software increases security, compatibility, and downtime risk.',
        'recommended_action' => 'Review and apply pending updates after confirming a working backup is available.',
    ];
}

function site_pulse_pro_build_uptime_module(array $metrics, int $score, int $max): array {
    $issues = [];

    $status = (int) ($metrics['uptime_status'] ?? 0);
    $response_time = (int) ($metrics['uptime_response_time'] ?? 0);

    if ($status !== 200) {
        $issues[] = sprintf('Latest uptime check returned HTTP %d.', $status);
    }

    if ($response_time > 2000) {
        $issues[] = sprintf('Latest response time was %d ms, which is slow.', $response_time);
    } elseif ($response_time > 0) {
        $issues[] = sprintf('Latest response time was %d ms.', $response_time);
    }

    if (empty($issues)) {
        $issues[] = 'No uptime or performance issues detected.';
    }

    return [
        'title' => 'Uptime',
        'score' => $score,
        'max' => $max,
        'status' => site_pulse_pro_score_status($score, $max),
        'issues' => $issues,
        'why_it_matters' => 'Downtime and slow response times can reduce trust, hurt SEO, and cost leads or sales.',
        'recommended_action' => 'Investigate server stability, caching, and performance bottlenecks if uptime or speed is degrading.',
    ];
}

function site_pulse_pro_build_backups_module(array $metrics, int $score, int $max): array {
    $issues = [];

    $backup_detected = (int) ($metrics['backup_detected'] ?? 0);

    if ($backup_detected !== 1) {
        $issues[] = 'No known backup plugin was detected.';
    } else {
        $issues[] = 'A backup plugin appears to be active.';
    }

    return [
        'title' => 'Backups',
        'score' => $score,
        'max' => $max,
        'status' => site_pulse_pro_score_status($score, $max),
        'issues' => $issues,
        'why_it_matters' => 'Without a reliable backup path, recovery from plugin failures, hacks, or bad updates becomes much harder.',
        'recommended_action' => 'Confirm that backups are running successfully and that restores have been tested.',
    ];
}

function site_pulse_pro_build_security_module(array $metrics, int $score, int $max): array {
    $issues = [];

    $ssl_active = (int) ($metrics['ssl_active'] ?? 0);
    $debug_enabled = (int) ($metrics['debug_enabled'] ?? 0);
    $wp_version_exposed = (int) ($metrics['wp_version_exposed'] ?? 0);

    if ($ssl_active !== 1) {
        $issues[] = 'SSL does not appear to be active.';
    }

    if ($debug_enabled === 1) {
        $issues[] = 'WP_DEBUG appears to be enabled.';
    }

    if ($wp_version_exposed === 1) {
        $issues[] = 'Your WordPress version may be exposed.';
    }

    if (empty($issues)) {
        $issues[] = 'No major security hygiene issues detected.';
    }

    return [
        'title' => 'Security',
        'score' => $score,
        'max' => $max,
        'status' => site_pulse_pro_score_status($score, $max),
        'issues' => $issues,
        'why_it_matters' => 'Basic security hygiene reduces avoidable exposure and makes your site less fragile.',
        'recommended_action' => 'Ensure SSL is active, disable debug output in production, and review unnecessary exposure points.',
    ];
}

function site_pulse_pro_build_tech_module(array $metrics, int $score, int $max): array {
    $issues = [];

    $php_version_id = (int) ($metrics['php_version_id'] ?? 0);
    $memory_bytes = (int) ($metrics['wp_memory_limit_bytes'] ?? 0);
    $cron_disabled = (int) ($metrics['wp_cron_disabled'] ?? 0);
    $has_object_cache = (int) ($metrics['has_object_cache_dropin'] ?? 0);
    $has_page_cache = (int) ($metrics['has_page_cache_dropin'] ?? 0);

    if ($php_version_id > 0 && $php_version_id < 80000) {
        $issues[] = 'Your PHP version appears to be below 8.0.';
    }

    if ($memory_bytes > 0 && $memory_bytes < (128 * 1024 * 1024)) {
        $issues[] = 'Your WordPress memory limit appears low.';
    }

    if ($cron_disabled === 1) {
        $issues[] = 'WP-Cron is disabled.';
    }

    if ($has_object_cache !== 1) {
        $issues[] = 'No object cache drop-in detected.';
    }

    if ($has_page_cache !== 1) {
        $issues[] = 'No page cache drop-in detected.';
    }

    if (empty($issues)) {
        $issues[] = 'No major technical platform risks detected.';
    }

    return [
        'title' => 'Tech',
        'score' => $score,
        'max' => $max,
        'status' => site_pulse_pro_score_status($score, $max),
        'issues' => $issues,
        'why_it_matters' => 'Technical platform weaknesses can make a site slower, less stable, and harder to maintain safely.',
        'recommended_action' => 'Review PHP version, memory allocation, cron reliability, and caching strategy.',
    ];
}