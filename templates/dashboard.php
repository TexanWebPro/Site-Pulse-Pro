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
        <?php foreach ($categories as $category => $score): ?>
            <?php
                $max = (int) ($weights[$category] ?? 0);
                $pct = ($max > 0) ? (int) round(($score / $max) * 100) : 0;

                // Status bands (tune later if you want)
                // >= 80% = good, >= 50% = warn, else bad
                $status = 'bad';
                if ($pct >= 80) {
                    $status = 'good';
                } elseif ($pct >= 50) {
                    $status = 'warn';
                }
            ?>

            <div class="site-pulse-pro-module">
                <div class="spp-module-head">
                    <h3><?php echo esc_html(ucfirst($category)); ?></h3>
                    <div class="spp-score">
                        <?php echo esc_html($score); ?>/<?php echo esc_html($max); ?>
                    </div>
                </div>

                <div class="spp-bar" role="progressbar"
                     aria-valuenow="<?php echo esc_attr($pct); ?>"
                     aria-valuemin="0"
                     aria-valuemax="100">
                    <div class="spp-bar-fill spp-<?php echo esc_attr($status); ?>"
                         style="width: <?php echo esc_attr($pct); ?>%;">
                    </div>
                </div>

                <div class="spp-bar-meta">
                    <span><?php echo esc_html($pct); ?>%</span>
                    <span class="spp-status spp-<?php echo esc_attr($status); ?>">
                        <?php echo esc_html(ucfirst($status)); ?>
                    </span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>