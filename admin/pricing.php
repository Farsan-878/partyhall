<?php
/**
 * Cafe Coffea – Admin Pricing & Special Offer Management
 */
require_once __DIR__ . '/../config/db.php';
require_admin_auth();

$db = get_db();
$baseUrl = get_base_url();
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($csrf)) {
        $error = "Security token mismatch. Please try again.";
    } else {
        $partyHallPrice = trim($_POST['party_hall_price'] ?? '1000');
        $cleaningCharge = trim($_POST['cleaning_charge'] ?? '200');
        $offerThreshold = trim($_POST['offer_threshold'] ?? '5000');
        $offerFreeHours = trim($_POST['offer_free_hours'] ?? '1');
        $offerHeadline  = trim($_POST['offer_headline'] ?? 'Special Privilege Offer');
        $offerDesc      = trim($_POST['offer_description'] ?? '');
        $offerTerms     = trim($_POST['offer_terms'] ?? '');

        $updates = [
            'party_hall_price' => $partyHallPrice,
            'cleaning_charge' => $cleaningCharge,
            'offer_threshold' => $offerThreshold,
            'offer_free_hours' => $offerFreeHours,
            'offer_headline' => $offerHeadline,
            'offer_description' => $offerDesc,
            'offer_terms' => $offerTerms
        ];

        $stmt = $db->prepare("UPDATE settings SET value = :v WHERE key = :k");
        foreach ($updates as $k => $v) {
            $stmt->execute([':v' => $v, ':k' => $k]);
        }

        $message = "Pricing & Special Offer settings updated successfully! Live website has been refreshed.";
    }
}

$settings = get_all_settings();
$csrfToken = generate_csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pricing Management | Cafe Coffea Admin</title>
    <link rel="icon" type="image/png" href="<?= $baseUrl ?>/assets/images/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>/assets/css/admin.css">
</head>
<body>
<div class="admin-layout">
    <?php include __DIR__ . '/../includes/admin_nav.php'; ?>

    <main class="admin-main">
        <div class="admin-container">
            <div class="page-header">
                <div>
                    <h1 class="page-title">Pricing & Offer Management</h1>
                    <p class="page-subtitle">Configure hourly hall fees, cleaning charges, the ₹5,000 privilege offer, and terms.</p>
                </div>
                <div>
                    <a href="<?= $baseUrl ?>/index.php#pricing" target="_blank" class="adm-btn adm-btn-outline">
                        <i class="fas fa-eye"></i>
                        <span>Preview on Website</span>
                    </a>
                </div>
            </div>

            <?php if (!empty($message)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span><?= e($message) ?></span>
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?= e($error) ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

                <!-- Standard Charges -->
                <div class="card">
                    <div class="card-title">
                        <span><i class="fas fa-receipt text-gold"></i> Standard Booking Charges</span>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="party_hall_price">Party Hall Charge (₹ per 1 hour)</label>
                            <input type="number" id="party_hall_price" name="party_hall_price" class="form-control" value="<?= e($settings['party_hall_price'] ?? '1000') ?>" required min="0" step="50">
                            <div class="form-hint">Standard party hall hourly booking rate (Default: ₹1,000).</div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="cleaning_charge">Cleaning Charge (₹ per booking)</label>
                            <input type="number" id="cleaning_charge" name="cleaning_charge" class="form-control" value="<?= e($settings['cleaning_charge'] ?? '200') ?>" required min="0" step="10">
                            <div class="form-hint">Fixed sanitation and maintenance fee (Default: ₹200).</div>
                        </div>
                    </div>

                    <!-- Live Calculation Preview -->
                    <div style="background: rgba(10, 7, 5, 0.6); border: 1px solid var(--adm-border); border-radius: 8px; padding: 1.25rem; display: flex; align-items: center; justify-content: space-between; margin-top: 0.5rem;">
                        <div>
                            <div style="font-weight: 600; color: #FFF;">Calculated Total for 1-Hour Standard Booking</div>
                            <div style="font-size: 0.8rem; color: var(--adm-text-muted);">Party Hall Charge + Cleaning Charge</div>
                        </div>
                        <div id="liveTotalDisplay" style="font-family: 'Playfair Display', serif; font-size: 1.8rem; font-weight: 700; color: var(--adm-gold-light);">
                            ₹<?= number_format((float)($settings['party_hall_price'] ?? 1000) + (float)($settings['cleaning_charge'] ?? 200)) ?>
                        </div>
                    </div>
                </div>

                <!-- Special Offer Settings -->
                <div class="card">
                    <div class="card-title">
                        <span><i class="fas fa-gift text-gold"></i> Special Privilege Offer Configuration</span>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="offer_threshold">Qualifying Purchase Threshold (₹)</label>
                            <input type="number" id="offer_threshold" name="offer_threshold" class="form-control" value="<?= e($settings['offer_threshold'] ?? '5000') ?>" required min="0" step="100">
                            <div class="form-hint">Minimum Cafe Coffea order amount required to qualify (Default: ₹5,000).</div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="offer_free_hours">Complimentary Hall Duration</label>
                            <input type="text" id="offer_free_hours" name="offer_free_hours" class="form-control" value="<?= e($settings['offer_free_hours'] ?? '1') ?>" required>
                            <div class="form-hint">Free duration given upon reaching threshold (e.g. 1 Hour).</div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="offer_headline">Offer Badge Headline</label>
                        <input type="text" id="offer_headline" name="offer_headline" class="form-control" value="<?= e($settings['offer_headline'] ?? 'Special Privilege Offer') ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="offer_description">Offer Explanation</label>
                        <textarea id="offer_description" name="offer_description" class="form-control" rows="3"><?= e($settings['offer_description'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="offer_terms">Eligibility Conditions & Terms</label>
                        <textarea id="offer_terms" name="offer_terms" class="form-control" rows="3"><?= e($settings['offer_terms'] ?? '') ?></textarea>
                        <div class="form-hint">State clearly that free duration is limited to 1 hour and what purchases qualify.</div>
                    </div>
                </div>

                <div style="margin-bottom: 3rem;">
                    <button type="submit" class="adm-btn adm-btn-primary" style="font-size: 1.05rem; padding: 0.9rem 2.2rem;">
                        <i class="fas fa-save"></i>
                        <span>Save Pricing Changes</span>
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>

<script>
// Dynamic total calculation preview on input change
const hallInput = document.getElementById('party_hall_price');
const cleanInput = document.getElementById('cleaning_charge');
const totalDisplay = document.getElementById('liveTotalDisplay');

function updateTotal() {
    const h = parseFloat(hallInput.value) || 0;
    const c = parseFloat(cleanInput.value) || 0;
    totalDisplay.textContent = '₹' + (h + c).toLocaleString('en-IN');
}

hallInput.addEventListener('input', updateTotal);
cleanInput.addEventListener('input', updateTotal);
</script>
</body>
</html>

