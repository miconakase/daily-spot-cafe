<?php require_once 'auth/auth_check.php'; ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Account | Daily Spot Cafe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../src/assets/css/admin/admin-dashboard.css" rel="stylesheet">
    <link href="../src/assets/css/admin/left-side-bar.css" rel="stylesheet">
    <link href="../src/assets/css/admin/top-navbar.css" rel="stylesheet">
    <script>if(localStorage.getItem('sidebarCollapsed')==='true')document.documentElement.classList.add('sidebar-collapsed');</script>
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
                                <span class="notification-badge"></span>
                            </button>
                            <div class="notification-dropdown" id="notificationDropdown">
                                <div class="notification-header">
                                    <h6>Notifications</h6>
                                    <span class="badge bg-primary-subtle text-primary small">3 New</span>
                                </div>
                                <div class="notification-body">
                                    <a href="#" class="notification-item">
                                        <div class="notification-icon-box icon-blue">
                                            <i class="bi bi-cart-fill"></i>
                                        </div>
                                        <div class="notification-content">
                                            <span class="notification-title">New Order Received</span>
                                            <span class="notification-desc">Order #1234 has been placed by Kurt Angel.</span>
                                            <span class="notification-time">2 mins ago</span>
                                        </div>
                                    </a>
                                    <a href="#" class="notification-item">
                                        <div class="notification-icon-box icon-orange">
                                            <i class="bi bi-exclamation-triangle-fill"></i>
                                        </div>
                                        <div class="notification-content">
                                            <span class="notification-title">Low Stock Alert</span>
                                            <span class="notification-desc">Java Chip Frappuccino is running low on stock.</span>
                                            <span class="notification-time">1 hour ago</span>
                                        </div>
                                    </a>
                                    <a href="#" class="notification-item">
                                        <div class="notification-icon-box icon-green">
                                            <i class="bi bi-person-fill-check"></i>
                                        </div>
                                        <div class="notification-content">
                                            <span class="notification-title">New User Registered</span>
                                            <span class="notification-desc">A new customer has created an account.</span>
                                            <span class="notification-time">5 hours ago</span>
                                        </div>
                                    </a>
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
                                <a href="account.php" class="profile-dropdown-item active">
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
                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="account-profile-card">
                            <div class="account-header-bg"></div>
                            <div class="account-profile-info">
                                <div class="account-avatar-wrapper">
                                    <div class="account-avatar-lg"><?php 
                                        $names = explode(' ', trim($_SESSION['full_name'] ?? ''));
                                        $initials = !empty($names[0]) ? strtoupper(substr($names[0], 0, 1)) : '';
                                        if (count($names) > 1) {
                                            $initials .= strtoupper(substr(end($names), 0, 1));
                                        }
                                        echo htmlspecialchars($initials);
                                    ?></div>
                                    <button class="account-avatar-edit">
                                        <i class="bi bi-camera-fill"></i>
                                    </button>
                                </div>
                                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                                    <div>
                                        <h2 class="account-name"><?php echo htmlspecialchars($_SESSION['full_name']); ?></h2>
                                        <span class="account-role-badge">Administrator</span>
                                    </div>
                                    <button class="btn btn-primary btn-update">
                                        <i class="bi bi-check2-circle me-2"></i>Save Changes
                                    </button>
                                </div>

                                <div class="mt-5">
                                    <h3 class="account-section-title">
                                        <i class="bi bi-person-lines-fill"></i>
                                        Personal Information
                                    </h3>
                                    <div class="account-info-grid">
                                        <div class="form-group">
                                            <label class="form-label-custom">First Name</label>
                                            <input type="text" class="form-control form-control-custom" value="<?php echo htmlspecialchars($_SESSION['first_name'] ?? ''); ?>">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label-custom">Last Name</label>
                                            <input type="text" class="form-control form-control-custom" value="<?php echo htmlspecialchars($_SESSION['last_name'] ?? ''); ?>">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label-custom">Email Address</label>
                                            <input type="email" class="form-control form-control-custom" value="<?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?>">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label-custom">Phone Number</label>
                                            <input type="tel" class="form-control form-control-custom" value="<?php echo htmlspecialchars($_SESSION['phone_number'] ?? ''); ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="security-card">
                            <h3 class="account-section-title">
                                <i class="bi bi-shield-lock-fill"></i>
                                Account Security
                            </h3>
                            <p class="text-muted small mb-4">Update your password to keep your account secure.</p>
                            
                            <div class="form-group mb-3">
                                <label class="form-label-custom">Current Password</label>
                                <input type="password" class="form-control form-control-custom" placeholder="••••••••">
                            </div>
                            
                            <div class="form-group mb-3">
                                <label class="form-label-custom">New Password</label>
                                <input type="password" class="form-control form-control-custom" placeholder="Min. 8 characters">
                                <div class="password-requirement">
                                    <i class="bi bi-circle-fill"></i> At least 8 characters long
                                </div>
                            </div>
                            
                            <div class="form-group mb-4">
                                <label class="form-label-custom">Confirm New Password</label>
                                <input type="password" class="form-control form-control-custom" placeholder="••••••••">
                            </div>
                            
                            <button class="btn btn-outline-dark w-100 btn-update">
                                Update Password
                            </button>
                        </div>

                        <div class="dashboard-card p-4 mt-4 bg-light border-0">
                            <h4 class="h6 fw-bold mb-3">Account Activity</h4>
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="p-2 rounded-circle bg-white text-success">
                                    <i class="bi bi-laptop fs-5"></i>
                                </div>
                                <div>
                                    <div class="small fw-bold">Last login</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">Today, 10:45 AM from Manila, PH</div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <div class="p-2 rounded-circle bg-white text-primary">
                                    <i class="bi bi-shield-check fs-5"></i>
                                </div>
                                <div>
                                    <div class="small fw-bold">2FA Status</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">Enabled via Authenticator App</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="../src/assets/javascript/admin/left-side-bar.js?v=5"></script>
    <script src="../src/assets/javascript/admin/top-navbar.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
