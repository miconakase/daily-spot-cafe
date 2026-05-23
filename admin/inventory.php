<?php require_once 'auth/auth_check.php'; ?>
<?php
require_once '../src/controller/CategoryController.php';
require_once '../src/controller/ProductController.php';

$categoryController = new CategoryController();
$categories = $categoryController->index();

$productController = new ProductController();
$products = $productController->index();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inventory Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
    <link href="https://quilljs.com" rel="stylesheet">
    <link href="../src/assets/css/admin/admin-dashboard.css" rel="stylesheet">
    <link href="../src/assets/css/admin/left-side-bar.css" rel="stylesheet">
    <link href="../src/assets/css/admin/top-navbar.css" rel="stylesheet">
    <link href="../src/assets/css/admin/rich-text-editor.css" rel="stylesheet">
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
                            <a href="inventory.php" class="list-group-item list-group-item-action border-0 py-2 small active">
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
                    <h1 class="h5 mb-1 fw-bold">Inventory
                        <span class="dashboard-tooltip ms-2">
                            <i class="bi bi-question-circle-fill text-secondary" aria-hidden="true"></i>
                            <span class="dashboard-tooltip-text" role="tooltip">
                                View, edit, and track your cafe's products. Keep your stock count accurate to prevent overselling.</span>
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
                            id="inventorySearch" 
                            class="form-control border-start-0 ps-0" 
                            placeholder="Search...">
                    </div>
                    
                    <button class="btn btn-primary btn-sm px-4" data-bs-toggle="modal" data-bs-target="#addProductModal">
                        <i class="bi bi-plus-lg me-1"></i> Add Product
                    </button>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Product ID</th>
                                <th>Product</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Date Created</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($products)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">No products found</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($products as $product):
                                    $statusClass = $product['is_published'] ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary';
                                    $statusText = $product['is_published'] ? 'Published' : 'Draft';
                                ?>
                                <tr>
                                    <td><?= $product['product_id'] ?></td>
                                    <td><span class="fw-semibold"><?= htmlspecialchars($product['product_name']) ?></span></td>
                                    <td><?= htmlspecialchars($product['category_name']) ?></td>
                                    <td>$<?= number_format((float)$product['price'], 2) ?></td>
                                    <td><span class="badge <?= $statusClass ?>"><?= $statusText ?></span></td>
                                    <td><?= date('Y-m-d', strtotime($product['created_at'])) ?></td>
                                    <td class="text-end">
                                        <button class="btn btn-light btn-sm" title="Edit"
                                            data-product-id="<?= $product['product_id'] ?>"
                                            data-product-name="<?= htmlspecialchars($product['product_name']) ?>"
                                            data-category-id="<?= $product['category_id'] ?>"
                                            data-price="<?= $product['price'] ?>"
                                            data-short-description="<?= htmlspecialchars($product['short_description'] ?? '') ?>"
                                            data-long-description="<?= htmlspecialchars($product['long_description'] ?? '') ?>"
                                            data-img-url="<?= htmlspecialchars($product['img_url'] ?? '') ?>"
                                            data-status="<?= $product['is_published'] ?>"
                                            data-is-available="<?= $product['is_available'] ?>"
                                            data-has-variants="<?= $product['has_variants'] ?>"
                                            data-has-sizes="<?= $product['has_sizes'] ?>"
                                            data-variants='<?= htmlspecialchars(json_encode($product['variants'] ?? []), ENT_QUOTES) ?>'>
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-light btn-sm text-danger" title="Delete"
                                            onclick="deleteProduct(<?= $product['product_id'] ?>)">
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

    <!-- ADD PRODUCTS MODAL -->
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="addProductModalLabel">Add New Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="addProductForm">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Product Name*</label>
                        <input type="text" class="form-control bg-light border-0" id="productName" placeholder="Enter product name" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Category*</label>
                            <select class="form-select bg-light border-0" name="category_id" required>
                                <option value="">Select Category</option>
                                <?php foreach($categories as $category): ?>
                                    <option value="<?= $category['category_id'] ?>">
                                        <?= htmlspecialchars($category['category_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3" id="priceFieldWrapper">
                            <label class="form-label small fw-bold">Price (PHP)*</label>
                             <span class="dashboard-tooltip ms-2">
                                <i class="bi bi-question-circle-fill text-secondary" aria-hidden="true"></i>
                            <span class="dashboard-tooltip-text" role="tooltip">
                                The price that consumer will pay.</span>
                        </span>
                            <input type="number" step="0.01" class="form-control bg-light border-0" id="basePrice" placeholder="0.00" required>
                        </div>
                    </div>

                    <div id="variantsSection" class="mb-3" style="display:none;">
                        <label class="form-label small fw-bold">Variant Pricing</label>
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <thead class="table-light">
                                    <tr id="variantHeader"></tr>
                                </thead>
                                <tbody id="variantBody"></tbody>
                            </table>
                        </div>
                        <div class="mt-2">
                            <span class="small fw-bold">Lowest Price: </span>
                            <span id="addLowestPriceDisplay" class="small fw-bold" style="color: #D35400;">₱0.00</span>
                        </div>
                        <small class="text-muted">Set prices for each variant and size combination.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Short Description*</label>
                         <span class="dashboard-tooltip ms-2">
                            <i class="bi bi-question-circle-fill text-secondary" aria-hidden="true"></i>
                            <span class="dashboard-tooltip-text" role="tooltip">
                                A 1-sentence summary (max 60 chars) shown on the shop gallery and search results.</span>
                        </span>
                        <input type="text" class="form-control bg-light border-0" id="shortDescription" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Long Description*</label>
                         <span class="dashboard-tooltip ms-2">
                            <i class="bi bi-question-circle-fill text-secondary" aria-hidden="true"></i>
                            <span class="dashboard-tooltip-text" role="tooltip">
                                Detailed information including ingredients, allergens, and brewing or preparation notes.</span>
                        </span>
                            <div id="editor-container" style="height: 200px; border-radius: 0 0 8px 8px;"></div>
                        <input type="hidden" name="long_description" id="long_description">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Status</label>
                            <select class="form-select bg-light border-0" id="addStatus">
                                <option value="1">Published</option>
                                <option value="0">Draft</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Availability</label>
                            <select class="form-select bg-light border-0" id="addAvailability">
                                <option value="1">Available</option>
                                <option value="0">Unavailable</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Featured Image*</label>
                        <span class="dashboard-tooltip ms-2">
                            <i class="bi bi-question-circle-fill text-secondary" aria-hidden="true"></i>
                            <span class="dashboard-tooltip-text" role="tooltip">
                                Upload a high-quality photo. This will be the main image customers see on the menu.</span>
                        </span>
                        <div class="d-flex align-items-center gap-3">
                            <!-- Preview Box -->
                            <div id="image-preview" class="rounded bg-light d-flex align-items-center justify-content-center border" style="width: 80px; height: 80px; overflow: hidden;">
                                <i class="bi bi-image text-muted fs-3"></i>
                            </div>
                            <div class="flex-grow-1">
                                <input type="file" class="form-control form-control-sm" id="productImage" accept="image/*" required>
                                <small class="text-muted">Recommended: Square image (500x500px)</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-0 text-end pt-3">
                        <button type="button" class="btn btn-light px-4 me-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4">Save Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

    <!-- EDIT PRODUCT MODAL -->
    <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="editProductModalLabel">Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="editProductForm">
                        <input type="hidden" name="product_id" id="editProductId">

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Product ID</label>
                            <input type="text" class="form-control bg-light border-0" id="editProductDisplayId" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Product Name*</label>
                            <input type="text" class="form-control bg-light border-0" id="editProductName" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold">Category*</label>
                                <select class="form-select bg-light border-0" id="editCategoryId" required>
                                    <option value="">Select Category</option>
                                    <?php foreach($categories as $category): ?>
                                        <option value="<?= $category['category_id'] ?>">
                                            <?= htmlspecialchars($category['category_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3" id="editPriceFieldWrapper">
                                <label class="form-label small fw-bold">Price (PHP)*</label>
                                <input type="number" step="0.01" class="form-control bg-light border-0" id="editPrice" required>
                            </div>
                        </div>

                        <div id="editVariantsSection" class="mb-3" style="display:none;">
                            <label class="form-label small fw-bold">Variant Pricing</label>
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0">
                                    <thead class="table-light">
                                        <tr id="editVariantHeader"></tr>
                                    </thead>
                                    <tbody id="editVariantBody"></tbody>
                                </table>
                            </div>
                            <div class="mt-2">
                                <span class="small fw-bold">Lowest Price: </span>
                                <span id="editLowestPriceDisplay" class="small fw-bold" style="color: #D35400;">₱0.00</span>
                            </div>
                            <small class="text-muted">Set prices for each variant and size combination.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Short Description*</label>
                            <input type="text" class="form-control bg-light border-0" id="editShortDescription" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Long Description*</label>
                            <div id="edit-editor-container" style="height: 200px; border-radius: 0 0 8px 8px;"></div>
                            <input type="hidden" name="edit_long_description" id="edit_long_description">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold">Status</label>
                                <select class="form-select bg-light border-0" id="editStatus">
                                    <option value="1">Published</option>
                                    <option value="0">Draft</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold">Availability</label>
                                <select class="form-select bg-light border-0" id="editAvailability">
                                    <option value="1">Available</option>
                                    <option value="0">Unavailable</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Featured Image</label>
                            <div class="d-flex align-items-center gap-3">
                                <div id="edit-image-preview" class="rounded bg-light d-flex align-items-center justify-content-center border" style="width: 80px; height: 80px; overflow: hidden;">
                                    <i class="bi bi-image text-muted fs-3"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="file" class="form-control form-control-sm" id="editProductImage" accept="image/*">
                                    <small class="text-muted">Leave empty to keep current image. Recommended: Square image (500x500px)</small>
                                </div>
                            </div>
                        </div>

                        <div class="mb-0 text-end pt-3">
                            <button type="button" class="btn btn-light px-4 me-2" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary px-4">Update Product</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- DELETE CONFIRM MODAL -->
    <div class="modal fade" id="deleteProductModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-body text-center p-4">
                    <div class="mb-3 mt-2">
                        <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Delete Product</h5>
                    <p class="text-muted mb-0" id="deleteProductMessage">Are you sure you want to delete this product? This action cannot be undone.</p>
                    <input type="hidden" id="deleteProductId">
                    <div class="d-flex justify-content-center gap-2 mt-4">
                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger px-4" id="confirmDeleteProductBtn">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../src/assets/javascript/admin/left-side-bar.js?v=5"></script>
    <script src="../src/assets/javascript/admin/top-navbar.js"></script>
    <script src="../src/assets/javascript/admin/rich-text-editor.js"></script>
    <script src="../src/assets/javascript/admin/shared.js"></script>
    <script src="../src/assets/javascript/admin/inventory.js?v=5"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-8DBwxvghb+f8w824cDtgFXtW+eLk+ifaFVIJ9ai0SyxgbpPzJblwXERQ8GHKq2ya" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    <script src="https://quilljs.com"></script>
</body>
</html>
