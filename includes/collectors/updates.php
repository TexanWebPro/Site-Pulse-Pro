<?php
/**
 * Collector for plugin/theme/core updates
 */

defined('ABSPATH') || exit;

/**
 * Collect outdated updates
 *
 * @return array
 */
function site_pulse_pro_collect_updates(): array {
    include_once ABSPATH . 'wp-admin/includes/update.php';
    include_once ABSPATH . 'wp-admin/includes/plugin.php';
    include_once ABSPATH . 'wp-admin/includes/theme.php';

    $core_updates = get_core_updates();
    $plugin_updates = get_plugin_updates();
    $theme_updates = wp_get_themes();

    // Count core update availability
    $core_available = isset($core_updates[0]->response) && $core_updates[0]->response === 'upgrade' ? 1 : 0;

    // Count plugins needing update
    $plugins_outdated = count($plugin_updates);

    // Count themes needing update
    $themes_outdated = 0;
    foreach ($theme_updates as $theme) {
        if ($theme->get('Version') < $theme->get('Version')) {
            $themes_outdated++;
        }
    }

    return [
        'core_update_needed' => $core_available,
        'plugins_outdated' => $plugins_outdated,
        'themes_outdated' => $themes_outdated,
    ];
}