<?php
/**
 * Cron registration and scheduling helpers
 */

defined('ABSPATH') || exit;

add_filter('cron_schedules', 'site_pulse_pro_cron_intervals');

/**
 * Register custom intervals.
 */
function site_pulse_pro_cron_intervals(array $schedules): array {
    $schedules['fifteen_minutes'] = [
        'interval' => 900,
        'display'  => __('Every 15 Minutes', 'site-pulse-pro'),
    ];
    return $schedules;
}

/**
 * Schedule plugin cron events.
 * Call on activation.
 */
function site_pulse_pro_schedule_events(): void {
    // 15-minute scan runner
    if (!wp_next_scheduled('site_pulse_pro_cron_hook')) {
        wp_schedule_event(time(), 'fifteen_minutes', 'site_pulse_pro_cron_hook');
    }

    // Optional: Daily PDF report hook (only if you’re using it)
    if (!wp_next_scheduled('site_pulse_pro_daily_report')) {
        // midnight site time; simple MVP approach
        $midnight = strtotime('tomorrow 00:00:00');
        wp_schedule_event($midnight, 'daily', 'site_pulse_pro_daily_report');
    }
}

/**
 * Unschedule plugin cron events.
 * Call on deactivation and uninstall.
 */
function site_pulse_pro_unschedule_events(): void {
    $timestamp = wp_next_scheduled('site_pulse_pro_cron_hook');
    while ($timestamp) {
        wp_unschedule_event($timestamp, 'site_pulse_pro_cron_hook');
        $timestamp = wp_next_scheduled('site_pulse_pro_cron_hook');
    }

    $daily = wp_next_scheduled('site_pulse_pro_daily_report');
    while ($daily) {
        wp_unschedule_event($daily, 'site_pulse_pro_daily_report');
        $daily = wp_next_scheduled('site_pulse_pro_daily_report');
    }
}