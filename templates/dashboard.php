<?php
defined('ABSPATH') || exit;

$weights = require SITE_PULSE_PRO_PATH . 'includes/scoring/weights.php';
?>

<div class="wrap">
    <h1><?php echo esc_html__('Site Pulse Pro', 'site-pulse-pro'); ?></h1>

    <h2><?php echo esc_html__('Total Site Pulse Score'); ?>: <?php echo esc_html($total_score); ?>/100</h2>

    <div class="site-pulse-pro-modules">

        <?php foreach ($categories as $category => $score): ?>
            <?php
                $max = $weights[$category] ?? 0;
            ?>
            <div class="site-pulse-pro-module">
                <h3><?php echo esc_html(ucfirst($category)); ?></h3>
                <p><?php echo esc_html("Score: $score / $max"); ?></p>
            </div>
        <?php endforeach; ?>

    </div>
</div>