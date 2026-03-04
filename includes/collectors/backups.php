<?php
/**
 * Backup collector - detects presence of known backup plugins
 */

defined('ABSPATH') || exit;

/**
 * Returns whether a known backup plugin is installed and active
 *
 * @return array
 */
function site_pulse_pro_collect_backups(): array {
    include_once ABSPATH . 'wp-admin/includes/plugin.php';

    $known_backup_plugins = [
        'updraftplus/updraftplus.php',
        'backupbuddy/backupbuddy.php',
        'duplicator/duplicator.php',
        'wpvivid-backuprestore/wpvivid-backuprestore.php',
        'blogvault/blogvault.php',
        'jetpack/jetpack.php',
        'vaultpress/vaultpress.php',
        'backwpup/backwpup.php',
    ];

    $active_plugins = get_option('active_plugins', []);

    $detected = [];
    foreach ($known_backup_plugins as $plugin_file) {
        if (in_array($plugin_file, $active_plugins, true)) {
            $detected[] = $plugin_file;
        }
    }

    return [
        'backup_detected' => !empty($detected),
        'plugins_detected' => $detected,
    ];
}