<?php
/**
 * Shared admin top bar: sidebar toggle, messages, notifications, profile menu.
 * Expects $adminPage (see sidebar.php).
 */
require_once __DIR__ . '/../../src/controller/NotificationController.php';
require_once __DIR__ . '/../../src/controller/MessageController.php';

$topNotificationCtrl = new NotificationController();
$topNotifications    = array_slice($topNotificationCtrl->getNotifications('all'), 0, 5);
$topUnreadNotifs     = (int) $topNotificationCtrl->getUnreadCount();

$topMessageCtrl   = new MessageController();
$topMessages      = $topMessageCtrl->index(5);
$topUnreadMessages = $topMessageCtrl->getUnreadCount();

$topNotifIcons = [
    'order'   => ['icon-blue', 'bi-cart-fill'],
    'message' => ['icon-green', 'bi-envelope-fill'],
    'stock'   => ['icon-orange', 'bi-exclamation-triangle-fill'],
    'user'    => ['icon-green', 'bi-person-fill-check'],
];

$topNames = explode(' ', trim($_SESSION['full_name'] ?? ''));
$topInitials = !empty($topNames[0]) ? strtoupper(substr($topNames[0], 0, 1)) : '';
if (count($topNames) > 1) {
    $topInitials .= strtoupper(substr(end($topNames), 0, 1));
}
?>
<nav class="top-navbar">
    <div class="d-flex align-items-center justify-content-between">
        <i id="sidebarToggle" class="bi bi-layout-sidebar-inset fs-5" style="cursor: pointer;"></i>
        <div class="d-flex align-items-center gap-2 top-navbar-actions">
            <!-- Messages Center -->
            <div class="notification-wrapper">
                <button type="button" class="btn btn-light btn-sm top-navbar-icon" id="messageToggle" aria-label="Messages">
                    <i class="bi bi-envelope"></i>
                    <?php if ($topUnreadMessages > 0): ?>
                        <span class="notification-badge"></span>
                    <?php endif; ?>
                </button>
                <div class="notification-dropdown" id="messageDropdown">
                    <div class="notification-header">
                        <h6>Messages</h6>
                        <?php if ($topUnreadMessages > 0): ?>
                            <span class="badge bg-primary-subtle text-primary small"><?= $topUnreadMessages ?> New</span>
                        <?php endif; ?>
                    </div>
                    <div class="notification-body">
                        <?php if (empty($topMessages)): ?>
                            <div class="p-4 text-center">
                                <p class="text-white-50 small mb-0">No messages</p>
                            </div>
                        <?php endif; ?>
                        <?php foreach ($topMessages as $msg): ?>
                            <a href="messages.php?open=<?= (int) $msg['message_id'] ?>" class="notification-item">
                                <div class="notification-icon-box <?= $msg['is_read'] ? 'icon-orange' : 'icon-green' ?>">
                                    <i class="bi <?= $msg['is_read'] ? 'bi-envelope-open' : 'bi-envelope-fill' ?>"></i>
                                </div>
                                <div class="notification-content">
                                    <span class="notification-title"><?= htmlspecialchars($msg['name']) ?><?= $msg['is_read'] ? '' : ' &bull;' ?></span>
                                    <span class="notification-desc"><?= htmlspecialchars($msg['subject']) ?></span>
                                    <span class="notification-time"><?= date('M d, g:i A', strtotime($msg['created_at'])) ?></span>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    <div class="notification-footer">
                        <a href="messages.php">View All Messages</a>
                    </div>
                </div>
            </div>

            <!-- Notifications -->
            <div class="notification-wrapper">
                <button type="button" class="btn btn-light btn-sm top-navbar-icon" id="notificationToggle" aria-label="Notifications">
                    <i class="bi bi-bell"></i>
                    <?php if ($topUnreadNotifs > 0): ?>
                        <span class="notification-badge"></span>
                    <?php endif; ?>
                </button>
                <div class="notification-dropdown" id="notificationDropdown">
                    <div class="notification-header">
                        <h6>Notifications</h6>
                        <?php if ($topUnreadNotifs > 0): ?>
                            <span class="badge bg-primary-subtle text-primary small"><?= $topUnreadNotifs ?> New</span>
                        <?php endif; ?>
                    </div>
                    <div class="notification-body">
                        <?php if (empty($topNotifications)): ?>
                            <div class="p-4 text-center">
                                <p class="text-white-50 small mb-0">No notifications</p>
                            </div>
                        <?php endif; ?>
                        <?php foreach ($topNotifications as $notif):
                            [$iconClass, $icon] = $topNotifIcons[$notif['type']] ?? ['icon-blue', 'bi-bell']; ?>
                            <a href="notification.php?action=mark_read&id=<?= (int) $notif['id'] ?>" class="notification-item">
                                <div class="notification-icon-box <?= $iconClass ?>">
                                    <i class="bi <?= $icon ?>"></i>
                                </div>
                                <div class="notification-content">
                                    <span class="notification-title"><?= htmlspecialchars($notif['title']) ?></span>
                                    <span class="notification-desc"><?= htmlspecialchars($notif['message']) ?></span>
                                    <span class="notification-time"><?= date('M d, g:i A', strtotime($notif['created_at'])) ?></span>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    <div class="notification-footer">
                        <a href="notification.php">View All Notifications</a>
                    </div>
                </div>
            </div>

            <div class="profile-wrapper">
                <div class="profile-menu d-flex align-items-center gap-3" id="profileToggle">
                    <div class="profile-avatar"><?= htmlspecialchars($topInitials) ?></div>
                    <div class="d-none d-sm-flex align-items-center gap-2">
                        <div class="profile-text">
                            <span class="fw-bold"><?= htmlspecialchars($_SESSION['full_name'] ?? '') ?></span>
                        </div>
                        <i class="bi bi-chevron-down small opacity-50"></i>
                    </div>
                </div>
                <div class="profile-dropdown" id="profileDropdown">
                    <div class="profile-info">
                        <span class="profile-info-name"><?= htmlspecialchars($_SESSION['full_name'] ?? '') ?></span>
                        <span class="profile-info-email"><?= htmlspecialchars($_SESSION['email'] ?? '') ?></span>
                    </div>
                    <a href="account.php" class="profile-dropdown-item<?= ($adminPage ?? '') === 'account' ? ' active' : '' ?>">
                        <i class="bi bi-person"></i>
                        My Account
                    </a>
                    <div class="profile-dropdown-divider"></div>
                    <a href="logout.php" class="profile-dropdown-item logout-item">
                        <i class="bi bi-box-arrow-right"></i>
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>
