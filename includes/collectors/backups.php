<?php
/**
 * Backup collector - detects presence of known backup plugins
 */

defined('ABSPATH') || exit;

/**
 * Returns whether a known backup plugin is installed and active.
 *
 * @return array{
 *   backup_detected: bool,
 *   plugins_detected: array<int, string>,
 *   plugins_installed: array<int, string>
 * }
 */
function site_pulse_pro_collect_backups(): array {
    if (!function_exists('get_plugins')) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    $known_backup_plugins = [
        'updraftplus/updraftplus.php',
        'backupbuddy/backupbuddy.php',
        'duplicator/duplicator.php',
        'wpvivid-backuprestore/wpvivid-backuprestore.php',
        'blogvault-real-time-backup/blogvault-real-time-backup.php',
        'jetpack/jetpack.php',
        'vaultpress/vaultpress.php',
        'backwpup/backwpup.php',
    ];

    $installed_plugins = get_plugins();

    $detected = [];
    $installed = [];

    foreach ($known_backup_plugins as $plugin_file) {
        if (isset($installed_plugins[$plugin_file])) {
            $installed[] = $plugin_file;

            $is_active = is_plugin_active($plugin_file);

            if (is_multisite() && function_exists('is_plugin_active_for_network')) {
                $is_active = $is_active || is_plugin_active_for_network($plugin_file);
            }

            if ($is_active) {
                $detected[] = $plugin_file;
            }
        }
    }

    error_log('Site Pulse backup installed: ' . print_r($installed, true));
    error_log('Site Pulse backup detected: ' . print_r($detected, true));

    return [
        'backup_detected' => !empty($detected),
        'plugins_detected' => $detected,
        'plugins_installed' => $installed,
    ];
}