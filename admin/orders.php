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
        <?php $adminPage = 'orders'; include __DIR__ . '/includes/sidebar.php'; ?>

        <div class="dashboard-wrapper">
            <?php include __DIR__ . '/includes/topbar.php'; ?>

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
                                    <td>
                                        <?php echo $payType === 'QRPH' ? 'QR PH' : $payType; ?>
                                        <?php if (!empty($order['reference_number'])): ?>
                                            <div class="small text-muted">Ref: <?= htmlspecialchars($order['reference_number']) ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end text-nowrap">
                                        <button class="btn btn-light btn-sm" title="View details" data-view-order="<?= (int) $order['order_id'] ?>">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <?php if ($order['payment_status'] !== 'Paid' && $order['order_status'] !== 'Cancelled'): ?>
                                            <button class="btn btn-light btn-sm text-success" title="Mark as paid" data-mark-paid="<?= (int) $order['order_id'] ?>">
                                                <i class="bi bi-cash-coin"></i>
                                            </button>
                                        <?php endif; ?>
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

    <!-- ORDER DETAILS MODAL -->
    <div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-labelledby="orderDetailsTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0 pb-0">
                    <div>
                        <h5 class="modal-title fw-bold" id="orderDetailsTitle">Order</h5>
                        <div class="small text-muted" id="orderDetailsDate"></div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="orderDetailsLoading" class="text-center text-muted py-4">Loading…</div>
                    <div id="orderDetailsContent" style="display: none;">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded h-100">
                                    <div class="small fw-bold text-muted mb-1">CUSTOMER</div>
                                    <div id="odCustomer"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded h-100">
                                    <div class="small fw-bold text-muted mb-1">PAYMENT</div>
                                    <div id="odPayment"></div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="table-light">
                                    <tr><th>Item</th><th>Options</th><th class="text-center">Qty</th><th class="text-end">Price</th><th class="text-end">Subtotal</th></tr>
                                </thead>
                                <tbody id="odItems"></tbody>
                                <tfoot>
                                    <tr><th colspan="4" class="text-end">Total</th><th class="text-end" id="odTotal"></th></tr>
                                </tfoot>
                            </table>
                        </div>
                        <div id="odNotes" class="mt-3 small"></div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <span class="me-auto" id="odStatus"></span>
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success px-4" id="odMarkPaidBtn" style="display: none;">
                        <i class="bi bi-cash-coin me-1"></i>Mark as Paid
                    </button>
                </div>
            </div>
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
                                <select class="form-select bg-light border-0" name="product_id" id="addProductSelect" required>
                                    <option value="">Select Product</option>
                                    <?php foreach ($products as $product):
                                        $variants = array_values(array_filter($product['variants'], fn($v) => $v['is_available']));
                                        $fromPrice = $variants ? min(array_map(fn($v) => (float) $v['price'], $variants)) : (float) $product['price']; ?>
                                        <option value="<?= $product['product_id'] ?>"
                                                data-price="<?= (float) $product['price'] ?>"
                                                data-variants="<?= htmlspecialchars(json_encode($variants)) ?>">
                                            <?= htmlspecialchars($product['product_name']) ?> — <?= $variants ? 'from ' : '' ?>₱<?= number_format($fromPrice, 2) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label small fw-bold">Quantity*</label>
                                <input type="number" class="form-control bg-light border-0" name="quantity" min="1" max="99" value="1" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label small fw-bold">Payment Type</label>
                                <select class="form-select bg-light border-0" name="payment_type" id="addPaymentType">
                                    <option value="COD">COD</option>
                                    <option value="QRPH">QR PH</option>
                                </select>
                            </div>
                        </div>

                        <!-- Shown only for products with Hot/Iced or size options -->
                        <div class="row" id="addOptionsRow" style="display: none;">
                            <div class="col-md-4 mb-3" id="addVariantCol">
                                <label class="form-label small fw-bold" for="addVariantType">Hot / Iced*</label>
                                <select class="form-select bg-light border-0" name="variant_type" id="addVariantType"></select>
                            </div>
                            <div class="col-md-4 mb-3" id="addSizeCol">
                                <label class="form-label small fw-bold" for="addSizeLabel">Size*</label>
                                <select class="form-select bg-light border-0" name="size_label" id="addSizeLabel"></select>
                            </div>
                        </div>

                        <div class="row" id="addReferenceRow" style="display: none;">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold" for="addReference">QR PH Reference No.</label>
                                <input type="text" class="form-control bg-light border-0" name="reference_number" id="addReference" maxlength="30" placeholder="Optional">
                            </div>
                        </div>

                        <div class="small text-muted mb-3" id="addPricePreview"></div>

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
                                    <option value="QRPH">QR PH</option>
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