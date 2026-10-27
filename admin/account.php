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
        <?php $adminPage = 'account'; include __DIR__ . '/includes/sidebar.php'; ?>

        <div class="dashboard-wrapper">
            <?php include __DIR__ . '/includes/topbar.php'; ?>

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
