<?php
/**
 * Admin dashboard controller
 */

defined('ABSPATH') || exit;

require_once SITE_PULSE_PRO_PATH . 'includes/database/queries.php';
require_once SITE_PULSE_PRO_PATH . 'includes/scoring/calculator.php';
require_once SITE_PULSE_PRO_PATH . 'includes/utils/permissions.php';
require_once SITE_PULSE_PRO_PATH . 'includes/admin/modules.php';

/**
 * Render the main dashboard page
 */
function site_pulse_pro_render_dashboard(): void {
    site_pulse_pro_enforce_access();

    // Pull latest metrics
    global $wpdb;
    $latest_metrics = [];

    $metric_rows = $wpdb->get_results("SELECT metric_type, metric_value FROM {$wpdb->prefix}sitepulse_metrics ORDER BY recorded_at DESC LIMIT 100");
    foreach ($metric_rows as $row) {
        $latest_metrics[$row->metric_type] = $row->metric_value;
    }

    // Calculate score
    $score_data = site_pulse_pro_calculate_score($latest_metrics);
    $total_score = $score_data['total_score'];
    $categories = $score_data['category_scores'];
    $weights = require SITE_PULSE_PRO_PATH . 'includes/scoring/weights.php';
    $modules = site_pulse_pro_build_modules($latest_metrics, $categories, $weights);

    // Render template
    include SITE_PULSE_PRO_PATH . 'templates/dashboard.php';
}