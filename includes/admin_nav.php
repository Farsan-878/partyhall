<?php
/**
 * Cafe Coffea – Cups & Moments
 * Admin Navigation Sidebar & Topbar Component
 */
require_once __DIR__ . '/../config/db.php';
require_admin_auth();

$baseUrl = get_base_url();
$currentPage = basename($_SERVER['PHP_SELF']);
$adminUser = $_SESSION['admin_user'] ?? 'Admin';
?>
<div class="admin-backdrop" id="adminBackdrop"></div>

<!-- Admin Sidebar -->
<aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-brand">
        <img src="<?= $baseUrl ?>/assets/images/logo.png" alt="Cafe Coffea" class="sidebar-logo">
        <div>
            <div class="sidebar-title">Cafe Coffea</div>
            <span class="sidebar-badge">Admin Panel</span>
        </div>
    </div>

    <nav class="sidebar-menu">
        <a href="<?= $baseUrl ?>/admin/index.php" class="sidebar-link <?= ($currentPage === 'index.php') ? 'active' : '' ?>">
            <i class="fas fa-tachometer-alt"></i>
            <span>Overview</span>
        </a>
        <a href="<?= $baseUrl ?>/admin/photos.php" class="sidebar-link <?= ($currentPage === 'photos.php') ? 'active' : '' ?>">
            <i class="fas fa-camera"></i>
            <span>Photo Gallery</span>
        </a>
        <a href="<?= $baseUrl ?>/admin/videos.php" class="sidebar-link <?= ($currentPage === 'videos.php') ? 'active' : '' ?>">
            <i class="fas fa-video"></i>
            <span>Video Gallery</span>
        </a>
        <a href="<?= $baseUrl ?>/admin/pricing.php" class="sidebar-link <?= ($currentPage === 'pricing.php') ? 'active' : '' ?>">
            <i class="fas fa-tag"></i>
            <span>Pricing & Offers</span>
        </a>
        <a href="<?= $baseUrl ?>/admin/instructions.php" class="sidebar-link <?= ($currentPage === 'instructions.php') ? 'active' : '' ?>">
            <i class="fas fa-clipboard-list"></i>
            <span>Instructions & Rules</span>
        </a>
        <a href="<?= $baseUrl ?>/admin/contact.php" class="sidebar-link <?= ($currentPage === 'contact.php') ? 'active' : '' ?>">
            <i class="fas fa-address-book"></i>
            <span>Contact Details</span>
        </a>
        <a href="<?= $baseUrl ?>/admin/settings.php" class="sidebar-link <?= ($currentPage === 'settings.php') ? 'active' : '' ?>">
            <i class="fas fa-cog"></i>
            <span>Website Settings</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="<?= $baseUrl ?>/index.php" target="_blank" class="adm-btn adm-btn-outline adm-btn-sm" style="width: 100%;">
            <i class="fas fa-external-link-alt"></i>
            <span>View Public Website</span>
        </a>
        <a href="<?= $baseUrl ?>/admin/logout.php" class="adm-btn adm-btn-danger adm-btn-sm" style="width: 100%;">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </div>
</aside>

<!-- Admin Main Area Topbar -->
<header class="admin-topbar">
    <button type="button" class="topbar-toggle" id="adminSidebarToggle" aria-label="Toggle Sidebar">
        <i class="fas fa-bars"></i>
    </button>

    <div style="font-weight: 600; color: var(--adm-gold-light);">
        Cafe Coffea – Party Hall Management
    </div>

    <div class="topbar-user">
        <div class="user-badge">
            <div class="user-avatar"><?= strtoupper(substr($adminUser, 0, 1)) ?></div>
            <span class="d-none-mobile"><?= e($adminUser) ?></span>
        </div>
        <a href="<?= $baseUrl ?>/admin/logout.php" title="Logout" style="color: var(--adm-text-muted); font-size: 1.1rem; margin-left: 0.5rem;">
            <i class="fas fa-power-off"></i>
        </a>
    </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const toggleBtn = document.getElementById('adminSidebarToggle');
    const sidebar = document.getElementById('adminSidebar');
    const backdrop = document.getElementById('adminBackdrop');

    if (toggleBtn && sidebar && backdrop) {
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('open');
            backdrop.classList.toggle('active');
        });

        backdrop.addEventListener('click', () => {
            sidebar.classList.remove('open');
            backdrop.classList.remove('active');
        });
    }
});
</script>

