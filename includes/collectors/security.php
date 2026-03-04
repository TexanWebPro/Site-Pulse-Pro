<?php
/**
 * Security collector - basic hygiene checks
 */

defined('ABSPATH') || exit;

/**
 * Returns array of simple security signals
 *
 * @return array
 */
function site_pulse_pro_collect_security(): array {
    $checks = [];

    // 1. WP version visible via generator meta tag?
    $checks['wp_version_exposed'] = false;
    $meta_generator = get_bloginfo('version');
    if (!empty($meta_generator)) {
        $checks['wp_version_exposed'] = true;
    }

    // 2. SSL active?
    $checks['ssl_active'] = is_ssl();

    // 3. Directory listing check (public wp-content)
    $uploads_index = ABSPATH . 'wp-content/uploads/index.php';
    $checks['uploads_index_exists'] = file_exists($uploads_index);

    // 4. Debug mode enabled?
    $checks['debug_enabled'] = defined('WP_DEBUG') && WP_DEBUG;

    return $checks;
}