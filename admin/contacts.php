<?php require_once 'auth/auth_check.php'; ?>
<?php
require_once '../src/controller/UserController.php';
$userController = new UserController();
$users = $userController->index();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contacts Dashboard</title>
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
                    <a href="contacts.php" class="list-group-item list-group-item-action active">
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
                                    <!-- Notifications -->
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
                                        <span class="fw-bold"><?php echo htmlspecialchars($_SESSION['full_name'] ?? ''); ?></span>
                                    </div>
                                    <i class="bi bi-chevron-down small opacity-50"></i>
                                </div>
                            </div>
                            <div class="profile-dropdown" id="profileDropdown">
                                <div class="profile-info">
                                    <span class="profile-info-name"><?php echo htmlspecialchars($_SESSION['full_name'] ?? ''); ?></span>
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
                        <h1 class="h5 mb-1 fw-bold">Contacts
                            <span class="dashboard-tooltip ms-2">
                                <i class="bi bi-question-circle-fill text-secondary" aria-hidden="true"></i>
                                <span class="dashboard-tooltip-text" role="tooltip">
                                    Manage your cafe's customers and users.
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
                            <input type="text" id="contactSearch" class="form-control border-start-0 ps-0" placeholder="Search contacts...">
                        </div>
                        
                        <button class="btn btn-primary btn-sm px-4">
                            <i class="bi bi-plus-lg me-1"></i> Add Contact
                        </button>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Role</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($users)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">No contacts found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($users as $user): 
                                        // Ensure full_name is properly set, fallback to username if not available
                                        $fullName = '';
                                        if (isset($user['details']['first_name']) && isset($user['details']['last_name'])) {
                                            $fullName = trim($user['details']['first_name'] . ' ' . $user['details']['last_name']);
                                        }
                                        if (empty($fullName) && !empty($user['username'])) {
                                            $fullName = $user['username'];
                                        }

                                        $email = !empty($user['details']['email']) ? $user['details']['email'] : 'No email';
                                        $phone = !empty($user['details']['phone_number']) ? $user['details']['phone_number'] : 'N/A';
                                        
                                        $names = explode(' ', trim($fullName));
                                        $initials = !empty($names[0]) ? strtoupper(substr($names[0], 0, 1)) : '';
                                        if (count($names) > 1) {
                                            $initials .= strtoupper(substr(end($names), 0, 1));
                                        }
                                        
                                        $roleText = $user['role_id'] == 1 ? 'Admin' : 'User';
                                        $roleBadge = $user['role_id'] == 1 ? 'bg-primary-subtle text-primary' : 'bg-secondary-subtle text-secondary';
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                                        <td>
                                            <a href="#" class="fw-semibold text-decoration-none text-dark user-details-link"
                                               data-bs-toggle="modal"
                                               data-bs-target="#userDetailsModal"
                                               data-user-id="<?php echo htmlspecialchars($user['user_id']); ?>"
                                               data-full-name="<?php echo htmlspecialchars($fullName); ?>"
                                               data-email="<?php echo htmlspecialchars($email); ?>"
                                               data-phone="<?php echo htmlspecialchars($phone); ?>"
                                               data-role="<?php echo htmlspecialchars($roleText); ?>">
                                                <?php echo htmlspecialchars($fullName); ?>
                                            </a>
                                        </td>
                                        <td><?php echo htmlspecialchars($email); ?></td>
                                        <td><?php echo htmlspecialchars($phone); ?></td>
                                        <td><span class="badge <?php echo $roleBadge; ?>"><?php echo htmlspecialchars($roleText); ?></span></td>
                                        <td class="text-end">
                                            <button class="btn btn-light btn-sm" title="Edit"
                                                data-user-id="<?= $user['user_id'] ?>"
                                                data-first-name="<?= htmlspecialchars($user['details']['first_name'] ?? '') ?>"
                                                data-last-name="<?= htmlspecialchars($user['details']['last_name'] ?? '') ?>"
                                                data-email="<?= htmlspecialchars($email) ?>"
                                                data-phone="<?= htmlspecialchars($phone) ?>"
                                                data-role-id="<?= $user['role_id'] ?>">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-light btn-sm text-danger" title="Delete"
                                                onclick="deleteContact(<?= $user['user_id'] ?>)">
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

    <!-- User Details Modal -->
    <div class="modal fade" id="userDetailsModal" tabindex="-1" aria-labelledby="userDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="userDetailsModalLabel">User Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Full Name:</label>
                        <p id="modalFullName" class="form-control-plaintext"></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Email:</label>
                        <p id="modalEmail" class="form-control-plaintext"></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Phone Number:</label>
                        <p id="modalPhone" class="form-control-plaintext"></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Role:</label>
                        <p id="modalRole" class="form-control-plaintext"></p>
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-bold">User ID:</label>
                        <p id="modalUserId" class="form-control-plaintext"></p>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- EDIT CONTACT MODAL -->
    <div class="modal fade" id="editContactModal" tabindex="-1" aria-labelledby="editContactModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="editContactModalLabel">Edit Contact</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="editContactForm">
                        <input type="hidden" name="user_id" id="editUserId">

                        <div class="mb-3">
                            <label class="form-label small fw-bold">User ID</label>
                            <input type="text" class="form-control bg-light border-0" id="editUserDisplayId" readonly>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold">First Name</label>
                                <input type="text" class="form-control bg-light border-0" name="first_name" id="editFirstName">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold">Last Name</label>
                                <input type="text" class="form-control bg-light border-0" name="last_name" id="editLastName">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Email</label>
                            <input type="text" class="form-control bg-light border-0" name="email" id="editEmail">
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Phone</label>
                            <input type="text" class="form-control bg-light border-0" name="phone_number" id="editPhone">
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Role</label>
                            <select class="form-select bg-light border-0" name="role_id" id="editRoleId">
                                <option value="2">User</option>
                                <option value="1">Admin</option>
                            </select>
                        </div>

                        <div class="mb-0 text-end pt-3">
                            <button type="button" class="btn btn-light px-4 me-2" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary px-4">Update Contact</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- DELETE CONFIRM MODAL -->
    <div class="modal fade" id="deleteContactModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-body text-center p-4">
                    <div class="mb-3 mt-2">
                        <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Delete Contact</h5>
                    <p class="text-muted mb-0">Are you sure you want to delete this contact? This action cannot be undone.</p>
                    <input type="hidden" id="deleteContactId">
                    <div class="d-flex justify-content-center gap-2 mt-4">
                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger px-4" id="confirmDeleteContactBtn">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../src/assets/javascript/admin/left-side-bar.js?v=5"></script>
    <script src="../src/assets/javascript/admin/top-navbar.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../src/assets/javascript/admin/shared.js"></script>
    <script src="../src/assets/javascript/admin/contacts.js"></script>
</body>
</html>
