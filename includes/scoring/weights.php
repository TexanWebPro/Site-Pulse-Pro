<?php
/**
 * Weight configuration for scoring engine
 */

defined('ABSPATH') || exit;

return [
    'updates'   => 25,  // Core/plugins/themes
    'uptime'    => 20,  // HTTP status & response time
    'backups'   => 20,  // Backup plugin detected
    'security'  => 20,  // SSL, debug, wp version exposure
    'tech'      => 15,  // PHP version, TLS, etc.
];