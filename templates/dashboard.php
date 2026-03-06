<?php
defined('ABSPATH') || exit;

$weights = require SITE_PULSE_PRO_PATH . 'includes/scoring/weights.php';
?>

<div class="wrap">
    <h1><?php echo esc_html__('Site Pulse Pro', 'site-pulse-pro'); ?></h1>

    <?php
        // Total status (based on % of 100)
        $total_pct = (int) round(($total_score / 100) * 100);

        $total_status = 'critical';
        if ($total_pct >= 80) {
            $total_status = 'healthy';
        } elseif ($total_pct >= 50) {
            $total_status = 'at-risk';
        }

        $total_label = ($total_status === 'healthy')
            ? 'Healthy'
            : (($total_status === 'at-risk') ? 'At Risk' : 'Critical');
    ?>

        <h2 class="spp-total">
            <?php echo esc_html__('Total Site Pulse Score', 'site-pulse-pro'); ?>:
            <span class="spp-total-score"><?php echo esc_html($total_score); ?>/100</span>
            <span class="spp-badge spp-<?php echo esc_attr($total_status); ?>">
                <?php echo esc_html($total_label); ?>
            </span>
        </h2>

    <div class="site-pulse-pro-modules">
        <?php foreach ($modules as $module): ?>
            <?php
                $pct = ($module['max'] > 0)
                    ? (int) round(($module['score'] / $module['max']) * 100)
                    : 0;

                $bar_status = 'bad';
                if ($pct >= 80) {
                    $bar_status = 'good';
                } elseif ($pct >= 50) {
                    $bar_status = 'warn';
                }
            ?>

            <div class="site-pulse-pro-module">
                <div class="spp-module-head">
                    <h3><?php echo esc_html($module['title']); ?></h3>
                    <div class="spp-score">
                        <?php echo esc_html($module['score']); ?>/<?php echo esc_html($module['max']); ?>
                    </div>
                </div>

                <div class="spp-bar" role="progressbar"
                     aria-valuenow="<?php echo esc_attr($pct); ?>"
                     aria-valuemin="0"
                     aria-valuemax="100">
                    <div class="spp-bar-fill spp-<?php echo esc_attr($bar_status); ?>"
                         style="width: <?php echo esc_attr($pct); ?>%;">
                    </div>
                </div>

                <div class="spp-bar-meta">
                    <span><?php echo esc_html($module['status']); ?></span>
                    <span><?php echo esc_html($pct); ?>%</span>
                </div>

                <div class="spp-module-section">
                    <strong><?php echo esc_html__('Issues', 'site-pulse-pro'); ?></strong>
                    <ul class="spp-issues">
                        <?php foreach ($module['issues'] as $issue): ?>
                            <li><?php echo esc_html($issue); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="spp-module-section">
                    <strong><?php echo esc_html__('Why this matters', 'site-pulse-pro'); ?></strong>
                    <p><?php echo esc_html($module['why_it_matters']); ?></p>
                </div>

                <div class="spp-module-section">
                    <strong><?php echo esc_html__('Recommended action', 'site-pulse-pro'); ?></strong>
                    <p><?php echo esc_html($module['recommended_action']); ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>