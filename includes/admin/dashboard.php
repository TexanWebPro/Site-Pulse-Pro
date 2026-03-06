<?php
/**
 * Admin dashboard controller
 */

defined('ABSPATH') || exit;

require_once SITE_PULSE_PRO_PATH . 'includes/database/queries.php';
require_once SITE_PULSE_PRO_PATH . 'includes/scoring/calculator.php';
require_once SITE_PULSE_PRO_PATH . 'includes/utils/permissions.php';
require_once SITE_PULSE_PRO_PATH . 'includes/admin/modules.php';
require_once SITE_PULSE_PRO_PATH . 'includes/scans/run-scan.php';

/**
 * Render the main dashboard page
 */
function site_pulse_pro_render_dashboard(): void {
    site_pulse_pro_enforce_access();

    // Manual refresh trigger
    if (
        $_SERVER['REQUEST_METHOD'] === 'POST' &&
        isset($_POST['site_pulse_pro_refresh_scan']) &&
        check_admin_referer('site_pulse_pro_refresh_scan_action', 'site_pulse_pro_refresh_scan_nonce')
    ) {
        site_pulse_pro_run_scan();

        echo '<div class="notice notice-success"><p>' .
            esc_html__('Site Pulse Pro scan refreshed.', 'site-pulse-pro') .
            '</p></div>';
    }

    // Pull latest metrics
    global $wpdb;
    $latest_metrics = [];

    $table = $wpdb->prefix . 'sitepulse_metrics';

    $rows = $wpdb->get_results(
        "SELECT m.metric_type, m.metric_value, m.id
        FROM {$table} m
        INNER JOIN (
            SELECT metric_type, MAX(id) AS max_id
            FROM {$table}
            GROUP BY metric_type
        ) latest
            ON m.metric_type = latest.metric_type
        AND m.id = latest.max_id"
    );

    foreach ($rows as $row) {
        $latest_metrics[$row->metric_type] = $row->metric_value;
    }
    
    error_log('Dashboard latest metrics: ' . print_r($latest_metrics, true));
    error_log('Dashboard latest backup_detected: ' . print_r($latest_metrics['backup_detected'] ?? null, true));
    // Calculate score
    $score_data = site_pulse_pro_calculate_score($latest_metrics);
    $total_score = $score_data['total_score'];
    $categories = $score_data['category_scores'];
    $weights = require SITE_PULSE_PRO_PATH . 'includes/scoring/weights.php';
    $modules = site_pulse_pro_build_modules($latest_metrics, $categories, $weights);

    // Render template
    include SITE_PULSE_PRO_PATH . 'templates/dashboard.php';
}