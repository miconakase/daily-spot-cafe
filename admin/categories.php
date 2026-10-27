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
        <?php $adminPage = 'categories'; include __DIR__ . '/includes/sidebar.php'; ?>

        <div class="dashboard-wrapper">
            <?php include __DIR__ . '/includes/topbar.php'; ?>

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
                                                data-is-active="<?= $category['is_active'] ?>"
                                                data-has-variants="<?= (int) ($category['has_variants'] ?? 0) ?>"
                                                data-has-sizes="<?= (int) ($category['has_sizes'] ?? 0) ?>">
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
                        <div class="mb-3">
                            <label class="form-label small fw-bold d-block">Drink options</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="has_variants" value="1" id="addHasVariants">
                                <label class="form-check-label small" for="addHasVariants">Hot / Iced</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="has_sizes" value="1" id="addHasSizes">
                                <label class="form-check-label small" for="addHasSizes">Grande / Venti sizes</label>
                            </div>
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
                        <div class="mb-3">
                            <label class="form-label small fw-bold d-block">Drink options</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="editHasVariants">
                                <label class="form-check-label small" for="editHasVariants">Hot / Iced</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="editHasSizes">
                                <label class="form-check-label small" for="editHasSizes">Grande / Venti sizes</label>
                            </div>
                            <div class="form-text">Changing these affects which options new products in this category get. Existing products keep their prices until you edit them.</div>
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
