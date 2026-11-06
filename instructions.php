<?php
/**
 * Cafe Coffea – Cups & Moments
 * Dedicated Instructions & Terms and Conditions Page
 */
require_once __DIR__ . '/config/db.php';

$db = get_db();
$settings = get_all_settings();
$baseUrl = get_base_url();

$stmtRules = $db->query("SELECT * FROM instructions WHERE is_active = 1 AND category = 'rules' ORDER BY display_order ASC, rule_number ASC");
$rules = $stmtRules->fetchAll();

$stmtDecoration = $db->query("SELECT * FROM instructions WHERE is_active = 1 AND category = 'decoration' ORDER BY display_order ASC, rule_number ASC");
$decorationRules = $stmtDecoration->fetchAll();

$stmtCake = $db->query("SELECT * FROM instructions WHERE is_active = 1 AND category = 'cake' ORDER BY display_order ASC, rule_number ASC");
$cakeRules = $stmtCake->fetchAll();

$stmtGeneral = $db->query("SELECT * FROM instructions WHERE is_active = 1 AND category = 'general' ORDER BY display_order ASC, rule_number ASC");
$generalRules = $stmtGeneral->fetchAll();

include __DIR__ . '/includes/header.php';
?>
    <!-- Page Header Banner -->
    <div style="padding: 7rem 0 3rem; background: var(--color-bg-surface); border-bottom: 1px solid var(--border-subtle); text-align: center;">
        <div class="container">
            <div class="section-tag">
                <i class="fas fa-file-contract"></i>
                <span>Official Guidelines</span>
            </div>
            <h1 class="section-title">Instructions & Policies</h1>
            <p class="section-subtitle">
                Please review our party hall rules, decoration policies, and cake pre-booking terms to ensure a smooth celebration.
            </p>
        </div>
    </div>

    <div class="section section-dark">
        <div class="container instructions-container">
            <!-- 1. Party Hall Rules -->
            <div class="policy-card">
                <div class="policy-header">
                    <div class="policy-icon-wrap">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <h2 class="policy-title">Party Hall Rules</h2>
                        <p class="policy-subtitle">Standard tariffs, allotted duration, and schedule compliance</p>
                    </div>
                </div>
                <div class="policy-rules-list">
                    <?php foreach ($rules as $r): ?>
                    <div class="rule-item">
                        <div class="rule-number"><?= e($r['rule_number']) ?></div>
                        <div class="rule-text">
                            <strong><?= e($r['title']) ?>:</strong> <?= e($r['description']) ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- 2. Decoration Policy -->
            <div class="policy-card">
                <div class="policy-header">
                    <div class="policy-icon-wrap">
                        <i class="fas fa-paint-brush"></i>
                    </div>
                    <div>
                        <h2 class="policy-title">Decoration Policy</h2>
                        <p class="policy-subtitle">Wallpaper protection, property inspection, and damage prevention</p>
                    </div>
                </div>
                <div class="policy-rules-list">
                    <?php foreach ($decorationRules as $d): ?>
                    <div class="rule-item">
                        <div class="rule-number"><?= e($d['rule_number']) ?></div>
                        <div class="rule-text">
                            <strong><?= e($d['title']) ?>:</strong> <?= e($d['description']) ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- 3. Cake Policy -->
            <div class="policy-card">
                <div class="policy-header">
                    <div class="policy-icon-wrap">
                        <i class="fas fa-birthday-cake"></i>
                    </div>
                    <div>
                        <h2 class="policy-title">Cake Policy</h2>
                        <p class="policy-subtitle">Cafe Coffea specialty cakes, outside cakes, and advance notification</p>
                    </div>
                </div>
                <div class="policy-rules-list">
                    <?php foreach ($cakeRules as $c): ?>
                    <div class="rule-item">
                        <div class="rule-number"><?= e($c['rule_number']) ?></div>
                        <div class="rule-text">
                            <strong><?= e($c['title']) ?>:</strong> <?= e($c['description']) ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <?php if (!empty($generalRules)): ?>
            <!-- 4. General / Cancellation Policy -->
            <div class="policy-card">
                <div class="policy-header">
                    <div class="policy-icon-wrap">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <div>
                        <h2 class="policy-title">General Terms & Conditions</h2>
                        <p class="policy-subtitle">Additional guidelines and policies</p>
                    </div>
                </div>
                <div class="policy-rules-list">
                    <?php foreach ($generalRules as $g): ?>
                    <div class="rule-item">
                        <div class="rule-number"><?= e($g['rule_number']) ?></div>
                        <div class="rule-text">
                            <strong><?= e($g['title']) ?>:</strong> <?= e($g['description']) ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <div style="text-align: center; margin-top: 3.5rem;">
                <a href="<?= $baseUrl ?>/index.php" class="btn btn-outline btn-lg" style="margin-right: 0.5rem;">
                    <i class="fas fa-home"></i>
                    <span>Back to Homepage</span>
                </a>
                <a href="<?= $baseUrl ?>/index.php#pricing" class="btn btn-primary btn-lg">
                    <i class="fas fa-tag"></i>
                    <span>Check Packages & Rates</span>
                </a>
            </div>
        </div>
    </div>

<?php
include __DIR__ . '/includes/footer.php';
?>

