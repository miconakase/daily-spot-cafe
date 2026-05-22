<?php require_once 'auth/auth_check.php'; ?>
<?php
require_once '../src/controller/CategoryController.php';

$categoryController = new CategoryController();
$categories = $categoryController->index();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Categories Dashboard</title>
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
                    <div id="products-toggle" class="list-group-item list-group-item-action active"
                        data-bs-toggle="collapse"
                        data-bs-target="#productsSubmenu"
                        aria-expanded="true"
                        style="cursor: pointer;">
                        <div class="d-flex justify-content-between align-items-center w-100">
                            <span>
                                <i class="nav-icon bi bi-bag-fill me-2"></i>
                                <span class="nav-label">Products</span>
                            </span>
                            <i id="products-arrow" class="bi bi-chevron-down small"></i>
                        </div>
                    </div>

                    <div class="collapse show" id="productsSubmenu">
                        <div class="list-group list-group-flush ps-4">
                            <a href="inventory.php" class="list-group-item list-group-item-action border-0 py-2 small">
                                <i class="bi bi-archive"></i>
                                Inventory
                            </a>
                            <a href="categories.php" class="list-group-item list-group-item-action border-0 py-2 small active">
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
                                            <span class="notification-desc">Order #1234 has been placed.</span>
                                            <span class="notification-time">2 mins ago</span>
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
                        <h1 class="h5 mb-1 fw-bold">Categories
                            <span class="dashboard-tooltip ms-2">
                                <i class="bi bi-question-circle-fill text-secondary" aria-hidden="true"></i>
                                <span class="dashboard-tooltip-text" role="tooltip">
                                    Manage your cafe's product categories. Organize your menu items into groups for easy browsing.
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
                                id="categorySearch"
                                class="form-control border-start-0 ps-0"
                                placeholder="Search categories...">
                        </div>

                        <button class="btn btn-primary btn-sm px-4" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                            <i class="bi bi-plus-lg me-1"></i> Add Category
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Category ID</th>
                                    <th>Category Name</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Date Created</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="categoryTableBody">
                                <?php if (empty($categories)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">No categories found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($categories as $category): ?>
                                    <tr class="category-row">
                                        <td><?php echo htmlspecialchars($category['category_id']); ?></td>
                                        <td>
                                            <span class="fw-semibold"><?php echo htmlspecialchars($category['category_name']); ?></span>
                                        </td>
                                        <td class="text-muted"><?php echo !empty($category['description']) ? htmlspecialchars($category['description']) : '—'; ?></td>
                                        <td>
                                            <?php if ($category['is_active']): ?>
                                                <span class="badge bg-success-subtle text-success">Active</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars(date('Y-m-d', strtotime($category['created_at']))); ?></td>
                                        <td class="text-end">
                                            <button class="btn btn-light btn-sm" title="Edit"
                                                data-category-id="<?= $category['category_id'] ?>"
                                                data-category-name="<?= htmlspecialchars($category['category_name']) ?>"
                                                data-description="<?= htmlspecialchars($category['description'] ?? '') ?>"
                                                data-is-active="<?= $category['is_active'] ?>">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-light btn-sm text-danger" title="Delete"
                                                onclick="deleteCategory(<?= $category['category_id'] ?>)">
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

    <!-- ADD CATEGORY MODAL -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="addCategoryModalLabel">Add New Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="addCategoryForm">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Category Name*</label>
                            <input type="text" class="form-control bg-light border-0" name="category_name" placeholder="Enter category name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Description</label>
                            <textarea class="form-control bg-light border-0" name="description" rows="3" placeholder="Enter category description (optional)"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Status</label>
                            <select class="form-select bg-light border-0" name="is_active">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                        <div class="mb-0 text-end pt-3">
                            <button type="button" class="btn btn-light px-4 me-2" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary px-4">Save Category</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- EDIT CATEGORY MODAL -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="editCategoryModalLabel">Edit Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="editCategoryForm">
                        <input type="hidden" name="category_id" id="editCategoryId">

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Category ID</label>
                            <input type="text" class="form-control bg-light border-0" id="editCategoryDisplayId" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Category Name*</label>
                            <input type="text" class="form-control bg-light border-0" name="category_name" id="editCategoryName" placeholder="Enter category name" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Description</label>
                            <textarea class="form-control bg-light border-0" name="description" id="editCategoryDescription" rows="3" placeholder="Enter category description (optional)"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Status</label>
                            <select class="form-select bg-light border-0" name="is_active" id="editCategoryStatus">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>

                        <div class="mb-0 text-end pt-3">
                            <button type="button" class="btn btn-light px-4 me-2" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary px-4">Update Category</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- DELETE CONFIRM MODAL -->
    <div class="modal fade" id="deleteCategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-body text-center p-4">
                    <div class="mb-3 mt-2">
                        <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Delete Category</h5>
                    <p class="text-muted mb-0">Are you sure you want to delete this category? This action cannot be undone.</p>
                    <input type="hidden" id="deleteCategoryId">
                    <div class="d-flex justify-content-center gap-2 mt-4">
                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger px-4" id="confirmDeleteCategoryBtn">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../src/assets/javascript/admin/left-side-bar.js?v=5"></script>
    <script src="../src/assets/javascript/admin/top-navbar.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-8DBwxvghb+f8w824cDtgFXtW+eLk+ifaFVIJ9ai0SyxgbpPzJblwXERQ8GHKq2ya" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    <script src="https://quilljs.com"></script>
    <script src="../src/assets/javascript/admin/shared.js"></script>
    <script src="../src/assets/javascript/admin/categories.js"></script>
</body>
</html>
