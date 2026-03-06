<?php
/**
 * Cron runner - execute periodic scans
 */

defined('ABSPATH') || exit;

require_once SITE_PULSE_PRO_PATH . 'includes/scans/run-scan.php';

add_action('site_pulse_pro_cron_hook', 'site_pulse_pro_run_cron');

function site_pulse_pro_run_cron(): void {
    site_pulse_pro_run_scan();
}

