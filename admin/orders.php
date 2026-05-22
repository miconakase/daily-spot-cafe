<?php require_once 'auth/auth_check.php'; ?>
<?php
require_once '../src/controller/OrderController.php';
require_once '../src/controller/UserDetailsController.php';
require_once '../src/controller/ProductController.php';

$orderController = new OrderController();
$orders = $orderController->index();

$userDetailsController = new UserDetailsController();
$customers = $userDetailsController->index();

$productController = new ProductController();
$products = $productController->getPublishedProducts();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Orders Dashboard</title>
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
                    <a href="orders.php" class="list-group-item list-group-item-action active">
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
                    <h1 class="h5 mb-1 fw-bold">Orders
                        <span class="dashboard-tooltip ms-2">
                            <i class="bi bi-question-circle-fill text-secondary" aria-hidden="true"></i>
                            <span class="dashboard-tooltip-text" role="tooltip">
                                View, edit, and track your cafe's orders. Manage customer requests and ensure timely fulfillment.</span>
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
                    
                    <button class="btn btn-primary btn-sm px-4" data-bs-toggle="modal" data-bs-target="#addOrderModal">
                        <i class="bi bi-plus-lg me-1"></i> Add Order
                    </button>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Total Amount</th>
                                <th>Order Status</th>
                                <th>Payment Status</th>
                                <th>Payment Type</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="orderTableBody">
                            <?php if (empty($orders)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">No orders found</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($orders as $order):
                                    $status = htmlspecialchars($order['order_status']);
                                    $badgeClass = match($status) {
                                        'Pending'    => 'bg-warning-subtle text-warning',
                                        'Processing' => 'bg-info-subtle text-info',
                                        'Ready'      => 'bg-primary-subtle text-primary',
                                        'Completed'  => 'bg-success-subtle text-success',
                                        'Cancelled'  => 'bg-danger-subtle text-danger',
                                        default      => 'bg-secondary-subtle text-secondary',
                                    };
                                    $payStatus = htmlspecialchars($order['payment_status'] ?? '—');
                                    $payStatusClass = match($payStatus) {
                                        'Paid'     => 'bg-success-subtle text-success',
                                        'Pending'  => 'bg-warning-subtle text-warning',
                                        'Refunded' => 'bg-secondary-subtle text-secondary',
                                        default    => 'bg-secondary-subtle text-secondary',
                                    };
                                    $payType = htmlspecialchars($order['payment_type'] ?? '—');
                                    $customer = !empty($order['customer_name']) ? htmlspecialchars($order['customer_name']) : '—';
                                    $dateFormatted = date('Y-m-d', strtotime($order['created_at'])) . ' | ' . date('h:i A', strtotime($order['created_at']));
                                ?>
                                <tr class="order-row">
                                    <td>#<?php echo htmlspecialchars($order['order_id']); ?></td>
                                    <td><?php echo $customer; ?></td>
                                    <td><?php echo htmlspecialchars($dateFormatted); ?></td>
                                    <td>₱<?php echo number_format((float)$order['total_amount'], 2); ?></td>
                                    <td><span class="badge <?php echo $badgeClass; ?>"><?php echo $status; ?></span></td>
                                    <td><span class="badge <?php echo $payStatusClass; ?>"><?php echo $payStatus; ?></span></td>
                                    <td><?php echo $payType; ?></td>
                                    <td class="text-end">
                                        <button class="btn btn-light btn-sm" title="Edit"
                                            data-order-id="<?= $order['order_id'] ?>"
                                            data-customer="<?= htmlspecialchars($customer) ?>"
                                            data-payment-type="<?= htmlspecialchars($payType) ?>"
                                            data-payment-status="<?= htmlspecialchars($payStatus) ?>"
                                            data-order-status="<?= htmlspecialchars($status) ?>"
                                            data-notes="<?= htmlspecialchars($order['notes'] ?? '') ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-light btn-sm text-danger" title="Delete"
                                            onclick="deleteOrder(<?= $order['order_id'] ?>)">
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

    <!-- ADD ORDER MODAL -->
    <div class="modal fade" id="addOrderModal" tabindex="-1" aria-labelledby="addOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="addOrderModalLabel">Add New Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="addOrderForm">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Customer</label>
                            <select class="form-select bg-light border-0" name="user_id" required>
                                <option value="">Select Customer</option>
                                <?php foreach ($customers as $customer): ?>
                                    <option value="<?= $customer['user_id'] ?>">
                                        <?= str_pad(htmlspecialchars($customer['full_name']), 35) ?> | <?= htmlspecialchars($customer['email'] ?? '') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold">Product*</label>
                                <select class="form-select bg-light border-0" name="product_id" required>
                                    <option value="">Select Product</option>
                                    <?php foreach ($products as $product): ?>
                                        <option value="<?= $product['product_id'] ?>">
                                            <?= htmlspecialchars($product['product_name']) ?> — ₱<?= number_format((float)$product['price'], 2) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label small fw-bold">Quantity*</label>
                                <input type="number" class="form-control bg-light border-0" name="quantity" min="1" value="1" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label small fw-bold">Payment Type</label>
                                <select class="form-select bg-light border-0" name="payment_type">
                                    <option value="COD">COD</option>
                                    <option value="GCash">GCash</option>
                                    <option value="Card">Card</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold">Payment Status</label>
                                <select class="form-select bg-light border-0" name="payment_status">
                                    <option value="Pending">Pending</option>
                                    <option value="Paid">Paid</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold">Order Status</label>
                                <select class="form-select bg-light border-0" name="order_status">
                                    <option value="Pending">Pending</option>
                                    <option value="Processing">Processing</option>
                                    <option value="Ready">Ready</option>
                                    <option value="Completed">Completed</option>
                                    <option value="Cancelled">Cancelled</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Notes</label>
                            <textarea class="form-control bg-light border-0" name="notes" rows="3" placeholder="Optional notes..."></textarea>
                        </div>

                        <div class="mb-0 text-end pt-3">
                            <button type="button" class="btn btn-light px-4 me-2" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary px-4">Save Order</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- DELETE CONFIRM MODAL -->
    <div class="modal fade" id="deleteOrderModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-body text-center p-4">
                    <div class="mb-3 mt-2">
                        <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Delete Order</h5>
                    <p class="text-muted mb-0" id="deleteOrderMessage">Are you sure you want to delete this order? This action cannot be undone.</p>
                    <input type="hidden" id="deleteOrderId">
                    <div class="d-flex justify-content-center gap-2 mt-4">
                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger px-4" id="confirmDeleteBtn">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- EDIT ORDER MODAL -->
    <div class="modal fade" id="editOrderModal" tabindex="-1" aria-labelledby="editOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="editOrderModalLabel">Edit Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="editOrderForm">
                        <input type="hidden" name="order_id" id="editOrderId">

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Order ID</label>
                            <input type="text" class="form-control bg-light border-0" id="editOrderDisplayId" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Customer</label>
                            <input type="text" class="form-control bg-light border-0" id="editOrderCustomer" readonly>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold">Payment Type</label>
                                <select class="form-select bg-light border-0" name="payment_type" id="editPaymentType">
                                    <option value="COD">COD</option>
                                    <option value="GCash">GCash</option>
                                    <option value="Card">Card</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold">Payment Status</label>
                                <select class="form-select bg-light border-0" name="payment_status" id="editPaymentStatus">
                                    <option value="Pending">Pending</option>
                                    <option value="Paid">Paid</option>
                                    <option value="Refunded">Refunded</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Order Status</label>
                            <select class="form-select bg-light border-0" name="order_status" id="editOrderStatus">
                                <option value="Pending">Pending</option>
                                <option value="Processing">Processing</option>
                                <option value="Ready">Ready</option>
                                <option value="Completed">Completed</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Notes</label>
                            <textarea class="form-control bg-light border-0" name="notes" id="editOrderNotes" rows="3" placeholder="Optional notes..."></textarea>
                        </div>

                        <div class="mb-0 text-end pt-3">
                            <button type="button" class="btn btn-light px-4 me-2" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary px-4">Update Order</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="../src/assets/javascript/admin/left-side-bar.js?v=5"></script>
    <script src="../src/assets/javascript/admin/top-navbar.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-8DBwxvghb+f8w824cDtgFXtW+eLk+ifaFVIJ9ai0SyxgbpPzJblwXERQ8GHKq2ya" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../src/assets/javascript/admin/shared.js"></script>
    <script src="../src/assets/javascript/admin/orders.js"></script></body>
</html>