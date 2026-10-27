<?php
/**
 * Shared admin sidebar.
 * Expects $adminPage: dashboard | orders | inventory | categories | contacts | messages | (other)
 */
require_once __DIR__ . '/../../src/controller/MessageController.php';

$adminPage = $adminPage ?? '';
$productsOpen = in_array($adminPage, ['inventory', 'categories'], true);
$sidebarUnreadMessages = (new MessageController())->getUnreadCount();
$navActive = fn($page) => $adminPage === $page ? ' active' : '';
?>
<aside class="sidebar">
    <div class="sidebar-brand mb-4 d-flex align-items-center gap-2">
        <i class="bi bi-cup-hot-fill fs-4" style="color: #D35400;"></i>
        <div class="h4 mb-0 brand-logo text-white">Daily Spot Cafe</div>
    </div>
    <div class="menu-card p-0">
        <div class="list-group sidebar-nav">
            <a href="dashboard.php" class="list-group-item list-group-item-action<?= $navActive('dashboard') ?>">
                <i class="nav-icon bi bi-house-door-fill"></i>
                <span class="nav-label">Dashboard</span>
            </a>
            <a href="orders.php" class="list-group-item list-group-item-action<?= $navActive('orders') ?>">
                <i class="nav-icon bi bi-box-seam-fill"></i>
                <span class="nav-label">Orders</span>
            </a>
            <div id="products-toggle" class="list-group-item list-group-item-action<?= $productsOpen ? ' active' : '' ?>"
                data-bs-toggle="collapse"
                data-bs-target="#productsSubmenu"
                aria-expanded="<?= $productsOpen ? 'true' : 'false' ?>"
                style="cursor: pointer;">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <span>
                        <i class="nav-icon bi bi-bag-fill me-2"></i>
                        <span class="nav-label">Products</span>
                    </span>
                    <i id="products-arrow" class="bi bi-chevron-<?= $productsOpen ? 'down' : 'right' ?> small"></i>
                </div>
            </div>

            <div class="collapse<?= $productsOpen ? ' show' : '' ?>" id="productsSubmenu">
                <div class="list-group list-group-flush ps-4">
                    <a href="inventory.php" class="list-group-item list-group-item-action border-0 py-2 small<?= $navActive('inventory') ?>">
                        <i class="bi bi-archive"></i>
                        Inventory
                    </a>
                    <a href="categories.php" class="list-group-item list-group-item-action border-0 py-2 small<?= $navActive('categories') ?>">
                        <i class="bi bi-tags"></i>
                        Categories
                    </a>
                </div>
            </div>
            <a href="contacts.php" class="list-group-item list-group-item-action<?= $navActive('contacts') ?>">
                <i class="nav-icon bi bi-people-fill"></i>
                <span class="nav-label">Contacts</span>
            </a>
            <a href="messages.php" class="list-group-item list-group-item-action<?= $navActive('messages') ?>">
                <i class="nav-icon bi bi-envelope-fill"></i>
                <span class="nav-label">Messages</span>
                <?php if ($sidebarUnreadMessages > 0): ?>
                    <span class="badge rounded-pill ms-auto nav-label" style="background:#D35400;"><?= $sidebarUnreadMessages ?></span>
                <?php endif; ?>
            </a>
        </div>
    </div>
</aside>
