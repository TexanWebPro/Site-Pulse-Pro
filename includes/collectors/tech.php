<?php
/**
 * Tech collector - basic platform exposure signals
 *
 * Purpose (MVP):
 * - Surface high-signal technical risk factors without deep host introspection.
 * - Keep checks fast and non-invasive.
 */

defined('ABSPATH') || exit;

/**
 * Collect basic tech stack exposure signals.
 *
 * @return array{
 *   php_version: string,
 *   php_version_id: int,
 *   php_supported: bool|null,
 *   php_eol_approx: bool|null,
 *   is_ssl: bool,
 *   site_url_scheme: string,
 *   wp_memory_limit_bytes: int|null,
 *   wp_memory_limit_raw: string|null,
 *   has_object_cache_dropin: bool,
 *   has_page_cache_dropin: bool,
 *   wp_cron_disabled: bool,
 *   wp_cron_alternate: bool,
 * }
 */
function site_pulse_pro_collect_tech(): array {
    $php_version = PHP_VERSION;
    $php_version_id = defined('PHP_VERSION_ID') ? (int) PHP_VERSION_ID : 0;

    // We cannot perfectly determine PHP support/EOL without an external source.
    // For MVP, use conservative heuristics:
    // - PHP < 8.0 is "likely EOL / higher risk" in modern WP hosting contexts.
    // You can tune this threshold later based on your compatibility policy.
    $php_eol_approx = ($php_version_id > 0) ? ($php_version_id < 80000) : null;

    // SSL signal
    $is_ssl = is_ssl();
    $site_url = site_url();
    $site_url_scheme = (string) parse_url($site_url, PHP_URL_SCHEME);

    // Memory limit parsing (WP_MEMORY_LIMIT / ini)
    $memory_raw = null;
    if (defined('WP_MEMORY_LIMIT')) {
        $memory_raw = (string) WP_MEMORY_LIMIT;
    } else {
        $ini = ini_get('memory_limit');
        $memory_raw = ($ini !== false) ? (string) $ini : null;
    }

    $memory_bytes = site_pulse_pro_parse_size_to_bytes($memory_raw);

    // Drop-ins (object cache, advanced cache)
    $wp_content = WP_CONTENT_DIR;
    $has_object_cache_dropin = file_exists($wp_content . '/object-cache.php');
    $has_page_cache_dropin = file_exists($wp_content . '/advanced-cache.php');

    // Cron config (reliability signal)
    $wp_cron_disabled = (defined('DISABLE_WP_CRON') && DISABLE_WP_CRON);
    $wp_cron_alternate = (defined('ALTERNATE_WP_CRON') && ALTERNATE_WP_CRON);

    return [
        'php_version' => $php_version,
        'php_version_id' => $php_version_id,
        // For MVP, we don't claim official support status:
        'php_supported' => null,
        'php_eol_approx' => $php_eol_approx,

        'is_ssl' => $is_ssl,
        'site_url_scheme' => $site_url_scheme,

        'wp_memory_limit_bytes' => $memory_bytes,
        'wp_memory_limit_raw' => $memory_raw,

        'has_object_cache_dropin' => $has_object_cache_dropin,
        'has_page_cache_dropin' => $has_page_cache_dropin,

        'wp_cron_disabled' => $wp_cron_disabled,
        'wp_cron_alternate' => $wp_cron_alternate,
    ];
}

/**
 * Parse a PHP/WordPress size string like "256M", "1G", "64K", "-1" into bytes.
 *
 * @param string|null $value
 * @return int|null Returns null if unknown/unparseable. Returns -1 as null (unlimited).
 */
function site_pulse_pro_parse_size_to_bytes(?string $value): ?int {
    if ($value === null) {
        return null;
    }

    $value = trim($value);

    // "-1" often means unlimited
    if ($value === '-1') {
        return null;
    }

    if ($value === '') {
        return null;
    }

    // Numeric bytes already
    if (ctype_digit($value)) {
        return (int) $value;
    }

    $unit = strtoupper(substr($value, -1));
    $number = substr($value, 0, -1);

    if (!is_numeric($number)) {
        return null;
    }

    $num = (float) $number;

    switch ($unit) {
        case 'K':
            return (int) round($num * 1024);
        case 'M':
            return (int) round($num * 1024 * 1024);
        case 'G':
            return (int) round($num * 1024 * 1024 * 1024);
        case 'T':
            return (int) round($num * 1024 * 1024 * 1024 * 1024);
        default:
            return null;
    }
}