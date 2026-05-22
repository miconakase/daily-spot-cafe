<?php require_once 'auth/auth_check.php'; ?>
<?php
require_once '../src/controller/NotificationController.php';
$notificationController = new NotificationController();

// Handle Actions
if (isset($_GET['action'])) {
    if ($_GET['action'] === 'mark_read' && isset($_GET['id'])) {
        $notificationController->markAsRead($_GET['id']);
        header('Location: notification.php');
        exit;
    } elseif ($_GET['action'] === 'mark_all_read') {
        $notificationController->markAllAsRead();
        header('Location: notification.php');
        exit;
    } elseif ($_GET['action'] === 'delete' && isset($_GET['id'])) {
        $notificationController->deleteNotification($_GET['id']);
        header('Location: notification.php');
        exit;
    }
}

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$notifications = $notificationController->getNotifications($filter);
$unreadCount = $notificationController->getUnreadCount();

// Mock data if empty for first-time use
if (empty($notifications) && $filter === 'all') {
    $notificationController->addNotification('New Order Received', 'Order #1234 has been placed by Kurt Angel.', 'order');
    $notificationController->addNotification('Low Stock Alert', 'Java Chip Frappuccino is running low on stock.', 'stock');
    $notificationController->addNotification('New User Registered', 'A new customer has created an account.', 'user');
    $notifications = $notificationController->getNotifications('all');
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Notifications Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../src/assets/css/admin/admin-dashboard.css" rel="stylesheet">
    <link href="../src/assets/css/admin/left-side-bar.css" rel="stylesheet">
    <link href="../src/assets/css/admin/top-navbar.css" rel="stylesheet">
    <script>if(localStorage.getItem('sidebarCollapsed')==='true')document.documentElement.classList.add('sidebar-collapsed');</script>
    <style>
        .badge-unread { background-color: rgba(211, 84, 0, 0.1); color: #D35400; }
        .badge-read { background-color: rgba(108, 117, 125, 0.1); color: #6c757d; }
        
        /* Theme consistent dropdown */
        .dropdown-item.active, .dropdown-item:active {
            background-color: #D35400;
            color: #fff;
        }
        .dropdown-item:hover {
            background-color: #f8f1eb;
            color: #3D2B1F;
        }
        .btn-filter {
            background-color: #f8f1eb;
            border: 1px solid rgba(61, 43, 31, 0.1);
            color: #3D2B1F;
            font-weight: 600;
        }
        .btn-filter:hover, .btn-filter:focus {
            background-color: #efe0d5;
            border-color: rgba(61, 43, 31, 0.2);
        }
    </style>
</head>
<body>
    <div class="dashboard-app">
        <aside class="sidebar">
            <div class="sidebar-brand mb-4 d-flex align-items-center gap-2">
                <i class="bi bi-cup-hot-fill fs-4" style="color: #D35400;"></i>
                <div class="h4 mb-0 brand-logo text-white">Daily Spot Cafe</div>
            </div>
            <div class="menu-card p-0">
                <div class="list-group sidebar-nav">
                    <a href="dashboard.php" class="list-group-item list-group-item-action">
                        <i class="nav-icon bi bi-house-door-fill"></i>
                        <span class="nav-label">Dashboard</span>
                    </a>
                    <a href="orders.php" class="list-group-item list-group-item-action">
                        <i class="nav-icon bi bi-box-seam-fill"></i>
                        <span class="nav-label">Orders</span>
                    </a>
                    <div id="products-toggle" class="list-group-item list-group-item-action" 
                        data-bs-toggle="collapse" 
                        data-bs-target="#productsSubmenu" 
                        aria-expanded="false" 
                        style="cursor: pointer;">
                        <div class="d-flex justify-content-between align-items-center w-100">
                            <span>
                                <i class="nav-icon bi bi-bag-fill me-2"></i>
                                <span class="nav-label">Products</span>
                            </span>
                            <i id="products-arrow" class="bi bi-chevron-right small"></i>
                        </div>
                    </div>

                    <div class="collapse" id="productsSubmenu">
                        <div class="list-group list-group-flush ps-4">
                            <a href="inventory.php" class="list-group-item list-group-item-action border-0 py-2 small">
                                <i class="bi bi-archive"></i>
                                Inventory
                            </a>
                            <a href="categories.php" class="list-group-item list-group-item-action border-0 py-2 small">
                                <i class="bi bi-tags"></i>
                                Categories
                            </a>
                        </div>
                    </div>
                    <a href="contacts.php" class="list-group-item list-group-item-action">
                        <i class="nav-icon bi bi-people-fill"></i>
                        <span class="nav-label">Contacts</span>
                    </a>
                </div>
            </div>
        </aside>

        <div class="dashboard-wrapper">
            <nav class="top-navbar">
                <div class="d-flex align-items-center justify-content-between">
                    <i id="sidebarToggle" class="bi bi-layout-sidebar-inset fs-5" style="cursor: pointer;"></i>
                    <div class="d-flex align-items-center gap-2 top-navbar-actions">
                        <div class="notification-wrapper">
                            <button type="button" class="btn btn-light btn-sm top-navbar-icon" id="notificationToggle">
                                <i class="bi bi-bell"></i>
                                <?php if ($unreadCount > 0): ?>
                                    <span class="notification-badge"></span>
                                <?php endif; ?>
                            </button>
                            <div class="notification-dropdown" id="notificationDropdown">
                                <div class="notification-header">
                                    <h6>Notifications</h6>
                                    <?php if ($unreadCount > 0): ?>
                                        <span class="badge bg-primary-subtle text-primary small"><?php echo $unreadCount; ?> New</span>
                                    <?php endif; ?>
                                </div>
                                <div class="notification-body">
                                    <?php if (empty($notifications)): ?>
                                        <div class="p-4 text-center">
                                            <p class="text-white-50 small mb-0">No notifications</p>
                                        </div>
                                    <?php else: ?>
                                        <?php 
                                        $dropdownItems = array_slice($notifications, 0, 5);
                                        foreach ($dropdownItems as $notif): 
                                            $iconClass = 'icon-blue';
                                            $icon = 'bi-bell';
                                            if ($notif['type'] === 'order') { $iconClass = 'icon-blue'; $icon = 'bi-cart-fill'; }
                                            elseif ($notif['type'] === 'stock') { $iconClass = 'icon-orange'; $icon = 'bi-exclamation-triangle-fill'; }
                                            elseif ($notif['type'] === 'user') { $iconClass = 'icon-green'; $icon = 'bi-person-fill-check'; }
                                        ?>
                                        <a href="notification.php?action=mark_read&id=<?php echo $notif['id']; ?>" class="notification-item">
                                            <div class="notification-icon-box <?php echo $iconClass; ?>">
                                                <i class="bi <?php echo $icon; ?>"></i>
                                            </div>
                                            <div class="notification-content">
                                                <span class="notification-title"><?php echo htmlspecialchars($notif['title']); ?></span>
                                                <span class="notification-desc"><?php echo htmlspecialchars($notif['message']); ?></span>
                                                <span class="notification-time"><?php echo date('M d, g:i A', strtotime($notif['created_at'])); ?></span>
                                            </div>
                                        </a>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                                <div class="notification-footer">
                                    <a href="notification.php">View All Notifications</a>
                                </div>
                            </div>
                        </div>
                        <div class="profile-wrapper">
                            <div class="profile-menu d-flex align-items-center gap-3" id="profileToggle">
                                <div class="profile-avatar"><?php 
                                    $names = explode(' ', trim($_SESSION['full_name'] ?? ''));
                                    $initials = !empty($names[0]) ? strtoupper(substr($names[0], 0, 1)) : '';
                                    if (count($names) > 1) {
                                        $initials .= strtoupper(substr(end($names), 0, 1));
                                    }
                                    echo htmlspecialchars($initials);
                                ?></div>
                                <div class="d-none d-sm-flex align-items-center gap-2">
                                    <div class="profile-text">
                                        <span class="fw-bold"><?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                                    </div>
                                    <i class="bi bi-chevron-down small opacity-50"></i>
                                </div>
                            </div>
                            <div class="profile-dropdown" id="profileDropdown">
                                <div class="profile-info">
                                    <span class="profile-info-name"><?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                                    <span class="profile-info-email"><?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?></span>
                                </div>
                                <a href="account.php" class="profile-dropdown-item">
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

            <main class="dashboard-main">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
                    <div>
                        <h1 class="h5 mb-1 fw-bold">Notifications
                            <span class="dashboard-tooltip ms-2">
                                <i class="bi bi-question-circle-fill text-secondary" aria-hidden="true"></i>
                                <span class="dashboard-tooltip-text" role="tooltip">
                                    View and manage system alerts, order updates, and customer activities.
                                </span>
                            </span>
                        </h1>
                    </div>
                </div>

                <div class="dashboard-card p-4 mt-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
                        <div class="input-group" style="max-width: 400px;">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="bi bi-search text-muted"></i>
                            </span>
                            <input type="text" 
                                id="notificationSearch" 
                                class="form-control border-start-0 ps-0" 
                                placeholder="Search notifications...">
                        </div>
                        
                        <div class="d-flex gap-2">
                            <div class="dropdown">
                                <button class="btn btn-filter btn-sm dropdown-toggle px-3" type="button" data-bs-toggle="dropdown">
                                    Filter: <?php echo ucfirst($filter); ?>
                                </button>
                                <ul class="dropdown-menu shadow-sm border-0">
                                    <li><a class="dropdown-item <?php echo $filter === 'all' ? 'active' : ''; ?>" href="notification.php?filter=all">All</a></li>
                                    <li><a class="dropdown-item <?php echo $filter === 'unread' ? 'active' : ''; ?>" href="notification.php?filter=unread">Unread</a></li>
                                </ul>
                            </div>
                            <a href="notification.php?action=mark_all_read" class="btn btn-primary btn-sm px-4">
                                <i class="bi bi-check-all me-1"></i> Mark All as Read
                            </a>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Notification</th>
                                    <th>Type</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="notificationTableBody">
                                <?php if (empty($notifications)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">No notifications found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($notifications as $notif): 
                                        $typeBadge = 'bg-info-subtle text-info';
                                        if ($notif['type'] === 'order') $typeBadge = 'bg-primary-subtle text-primary';
                                        elseif ($notif['type'] === 'stock') $typeBadge = 'bg-warning-subtle text-warning';
                                        elseif ($notif['type'] === 'user') $typeBadge = 'bg-success-subtle text-success';
                                    ?>
                                        <tr class="notification-row">
                                            <td><?php echo $notif['id']; ?></td>
                                            <td>
                                                <div class="fw-bold"><?php echo htmlspecialchars($notif['title']); ?></div>
                                                <div class="small text-muted"><?php echo htmlspecialchars($notif['message']); ?></div>
                                            </td>
                                            <td><span class="badge <?php echo $typeBadge; ?>"><?php echo ucfirst($notif['type']); ?></span></td>
                                            <td><?php echo date('Y-m-d H:i', strtotime($notif['created_at'])); ?></td>
                                            <td>
                                                <span class="badge <?php echo $notif['is_read'] ? 'bg-secondary-subtle text-secondary' : 'bg-danger-subtle text-danger'; ?>">
                                                    <?php echo $notif['is_read'] ? 'Read' : 'Unread'; ?>
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <?php if (!$notif['is_read']): ?>
                                                    <a href="notification.php?action=mark_read&id=<?php echo $notif['id']; ?>" class="btn btn-light btn-sm" title="Mark as Read">
                                                        <i class="bi bi-check-lg"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <button class="btn btn-light btn-sm text-danger" title="Delete" data-notif-id="<?= $notif['id'] ?>">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- DELETE CONFIRM MODAL -->
    <div class="modal fade" id="deleteNotifModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-body text-center p-4">
                    <div class="mb-3 mt-2">
                        <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Delete Notification</h5>
                    <p class="text-muted mb-0">Are you sure you want to delete this notification? This action cannot be undone.</p>
                    <input type="hidden" id="deleteNotifId">
                    <div class="d-flex justify-content-center gap-2 mt-4">
                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger px-4" id="confirmDeleteNotifBtn">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../src/assets/javascript/admin/left-side-bar.js?v=5"></script>
    <script src="../src/assets/javascript/admin/top-navbar.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../src/assets/javascript/admin/shared.js"></script>
    <script>
        document.getElementById('notificationSearch').addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll('.notification-row');
            
            rows.forEach(row => {
                let text = row.innerText.toLowerCase();
                if (text.includes(filter)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        document.addEventListener('click', function(e) {
            const btn = e.target.closest('[data-notif-id]');
            if (!btn) return;
            document.getElementById('deleteNotifId').value = btn.getAttribute('data-notif-id');
            new bootstrap.Modal(document.getElementById('deleteNotifModal')).show();
        });

        document.getElementById('confirmDeleteNotifBtn').addEventListener('click', function() {
            const id = document.getElementById('deleteNotifId').value;
            bootstrap.Modal.getInstance(document.getElementById('deleteNotifModal')).hide();
            window.location.href = 'notification.php?action=delete&id=' + id;
        });
    </script>
</body>
</html>