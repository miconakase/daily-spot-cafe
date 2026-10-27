<?php require_once 'auth/auth_check.php'; ?>
<?php
require_once '../src/controller/DashboardController.php';

$dashboard = (new DashboardController())->getDashboard($_GET['range'] ?? '7d');
$rangeLabel = $dashboard['range_label'];

function peso($amount)
{
    return '₱' . number_format((float) $amount, 2);
}

/** "↑ 12% vs previous 7 days" with direction colour, or a neutral note when there is no baseline. */
function deltaLine($delta, $rangeKey)
{
    $period = ['today' => 'yesterday', '7d' => 'previous 7 days', '30d' => 'previous 30 days'][$rangeKey];
    if ($delta === null) {
        return '<span class="text-muted">No sales in the ' . ($rangeKey === 'today' ? 'day' : 'period') . ' before to compare</span>';
    }
    if ($delta == 0) {
        return '<span class="text-muted"><i class="bi bi-dash"></i> Same as ' . $period . '</span>';
    }
    $up = $delta > 0;
    return '<span class="' . ($up ? 'text-success' : 'text-danger') . ' fw-semibold">'
        . '<i class="bi bi-arrow-' . ($up ? 'up' : 'down') . '-short"></i>' . abs($delta) . '%</span>'
        . ' <span class="text-muted">vs ' . $period . '</span>';
}

$statusBadge = [
    'Pending'    => 'bg-warning-subtle text-warning',
    'Processing' => 'bg-info-subtle text-info',
    'Ready'      => 'bg-primary-subtle text-primary',
    'Completed'  => 'bg-success-subtle text-success',
    'Cancelled'  => 'bg-danger-subtle text-danger',
];
$topMax = max(array_map(fn($p) => (int) $p['quantity'], $dashboard['top_products']) ?: [1]);
$totals = $dashboard['totals'];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../src/assets/css/admin/admin-dashboard.css" rel="stylesheet">
    <link href="../src/assets/css/admin/left-side-bar.css" rel="stylesheet">
    <link href="../src/assets/css/admin/top-navbar.css" rel="stylesheet">
    <script>if(localStorage.getItem('sidebarCollapsed')==='true')document.documentElement.classList.add('sidebar-collapsed');</script>
    <style>
        .metric-sub { font-size: 0.85rem; }
        .range-switch .btn { font-size: 0.85rem; }
        .range-switch .btn.active { background: #3D2B1F; border-color: #3D2B1F; color: #fff; }

        /* Daily gross sales chart */
        .chart-wrap { position: relative; height: 240px; }
        .chart-wrap svg { display: block; width: 100%; height: 100%; overflow: visible; }
        .chart-grid { stroke: #ece4dc; stroke-width: 1; }
        .chart-axis-text { fill: #8a7a70; font-size: 11px; }
        .chart-bar { fill: #D35400; }
        .chart-hit { fill: transparent; cursor: default; outline: none; }
        .chart-hit:hover + .chart-bar, .chart-hit:focus + .chart-bar, .chart-bar.is-active { fill: #E67E22; }
        .chart-hit:focus-visible { stroke: #3D2B1F; stroke-width: 1; }
        .chart-tooltip { position: absolute; pointer-events: none; background: #fff; border: 1px solid #ece4dc;
            border-radius: 6px; box-shadow: 0 4px 14px rgba(61,43,31,0.12); padding: 6px 10px; font-size: 0.8rem;
            white-space: nowrap; transform: translate(-50%, -100%); display: none; }
        .chart-tooltip strong { display: block; font-size: 0.95rem; color: #3D2B1F; }
        .chart-tooltip span { color: #6c757d; }
        .chart-empty { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; color: #8a7a70; font-size: 0.9rem; }

        /* Top sellers meter: accent fill on a lighter step of the same hue */
        .seller-track { height: 6px; background: #f8e3d4; border-radius: 3px; overflow: hidden; }
        .seller-fill { height: 100%; background: #D35400; border-radius: 3px; }
        .paid-split { height: 8px; background: #f8e3d4; border-radius: 4px; overflow: hidden; }
        .paid-split div { height: 100%; background: #D35400; }
    </style>
</head>
<body>
    <div class="dashboard-app">
        <?php $adminPage = 'dashboard'; include __DIR__ . '/includes/sidebar.php'; ?>

        <div class="dashboard-wrapper">
            <?php include __DIR__ . '/includes/topbar.php'; ?>

            <main class="dashboard-main">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
                <div>
                    <h1 class="h5 mb-1 fw-bold">Dashboard
                        <span class="dashboard-tooltip ms-2">
                            <i class="bi bi-question-circle-fill text-secondary" aria-hidden="true"></i>
                            <span class="dashboard-tooltip-text" role="tooltip">
                                A quick snapshot of the cafe: sales, orders that need attention, best sellers and the latest orders. Cancelled orders are not counted in sales.
                            </span>
                        </span>
                    </h1>
                </div>
                <div class="btn-group range-switch" role="group" aria-label="Date range">
                    <?php foreach (DashboardController::RANGES as $key => [$label]): ?>
                        <a href="?range=<?= $key ?>" class="btn btn-outline-secondary btn-sm<?= $dashboard['range'] === $key ? ' active' : '' ?>"
                           <?= $dashboard['range'] === $key ? 'aria-current="true"' : '' ?>><?= $label ?></a>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="row gy-4">
                <div class="col-md-4">
                    <div class="dashboard-card p-3 h-100">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <div class="metric-label mb-2">Sales</div>
                                <div class="metric-value"><?= peso($dashboard['sales']) ?></div>
                            </div>
                            <a href="orders.php" class="btn btn-outline-dark btn-sm">View</a>
                        </div>
                        <div class="metric-sub"><?= deltaLine($dashboard['sales_delta'], $dashboard['range']) ?></div>
                        <p class="text-muted mb-0 small mt-1"><?= $rangeLabel ?></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="dashboard-card p-3 h-100">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <div class="metric-label mb-2">Orders</div>
                                <div class="metric-value"><?= number_format($dashboard['orders']) ?></div>
                            </div>
                            <a href="orders.php" class="btn btn-outline-dark btn-sm">View</a>
                        </div>
                        <div class="metric-sub"><?= deltaLine($dashboard['orders_delta'], $dashboard['range']) ?></div>
                        <p class="text-muted mb-0 small mt-1"><?= $rangeLabel ?></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="dashboard-card p-3 h-100">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <div class="metric-label mb-2">Unfulfilled Orders</div>
                                <div class="metric-value"><?= number_format($dashboard['unfulfilled']) ?></div>
                            </div>
                            <a href="orders.php" class="btn btn-outline-dark btn-sm">View</a>
                        </div>
                        <div class="metric-sub text-muted">
                            <?php if ($dashboard['unfulfilled'] > 0): ?>
                                <i class="bi bi-hourglass-split"></i> Pending, processing or ready for pickup
                            <?php else: ?>
                                <i class="bi bi-check2-circle"></i> All caught up
                            <?php endif; ?>
                        </div>
                        <p class="text-muted mb-0 small mt-1">Right now</p>
                    </div>
                </div>
            </div>

            <div class="row gy-4 mt-0">
                <div class="col-lg-7">
                    <div class="dashboard-card p-3 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div class="card-header-sm">Daily Gross Sales</div>
                            <span class="small text-muted"><?= count($dashboard['daily']) ?> days</span>
                        </div>
                        <div class="chart-wrap mt-3" id="dailyChart" role="img" aria-label="Bar chart of daily gross sales; the same values are in the table below">
                            <div class="chart-tooltip" id="chartTooltip" role="status"></div>
                        </div>
                        <details class="mt-3 small">
                            <summary class="text-muted">View as table</summary>
                            <table class="table table-sm mt-2 mb-0">
                                <thead><tr><th>Date</th><th class="text-end">Orders</th><th class="text-end">Gross sales</th></tr></thead>
                                <tbody>
                                    <?php foreach (array_reverse($dashboard['daily']) as $day): ?>
                                        <tr><td><?= htmlspecialchars($day['label']) ?></td><td class="text-end"><?= $day['orders'] ?></td><td class="text-end"><?= peso($day['sales']) ?></td></tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </details>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="dashboard-card p-3">
                        <div class="card-header-sm mb-2">Total Sales</div>
                        <div class="metric-value"><?= peso($totals['gross']) ?></div>
                        <div class="small text-muted mb-3">All time &middot; <?= number_format($totals['orders']) ?> orders</div>
                        <?php if ($totals['gross'] > 0): ?>
                            <div class="paid-split mb-2" role="img" aria-label="<?= round($totals['paid'] / $totals['gross'] * 100) ?>% of sales collected">
                                <div style="width: <?= round($totals['paid'] / $totals['gross'] * 100, 1) ?>%;"></div>
                            </div>
                        <?php endif; ?>
                        <div class="d-flex justify-content-between small">
                            <span><i class="bi bi-square-fill me-1" style="color:#D35400;"></i>Collected <strong><?= peso($totals['paid']) ?></strong></span>
                            <span><i class="bi bi-square-fill me-1" style="color:#f8e3d4;"></i>Awaiting payment <strong><?= peso($totals['unpaid']) ?></strong></span>
                        </div>
                    </div>
                    <div class="dashboard-card p-3 mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="card-header-sm">Top Selling Products</div>
                            <span class="small text-muted"><?= $rangeLabel ?></span>
                        </div>
                        <?php if (empty($dashboard['top_products'])): ?>
                            <div class="text-muted small">No items sold in this period yet.</div>
                        <?php endif; ?>
                        <?php foreach ($dashboard['top_products'] as $i => $product): ?>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between small mb-1">
                                    <span><span class="text-muted me-1"><?= $i + 1 ?>.</span><span class="fw-semibold"><?= htmlspecialchars($product['product_name']) ?></span></span>
                                    <span><strong><?= (int) $product['quantity'] ?></strong> sold &middot; <?= peso($product['revenue']) ?></span>
                                </div>
                                <div class="seller-track"><div class="seller-fill" style="width: <?= round($product['quantity'] / $topMax * 100, 1) ?>%;"></div></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="dashboard-card p-3 mt-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="card-header-sm">Latest Orders</div>
                    <a href="orders.php" class="btn btn-outline-dark btn-sm">View all</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr><th>Order</th><th>Customer</th><th>Placed</th><th>Total</th><th>Status</th><th>Payment</th></tr>
                        </thead>
                        <tbody>
                            <?php if (empty($dashboard['latest'])): ?>
                                <tr><td colspan="6" class="text-center text-muted py-4">No orders yet</td></tr>
                            <?php endif; ?>
                            <?php foreach ($dashboard['latest'] as $order): ?>
                                <tr>
                                    <td>#<?= (int) $order['order_id'] ?></td>
                                    <td><?= htmlspecialchars($order['customer_name'] !== '' ? $order['customer_name'] : '—') ?></td>
                                    <td class="text-nowrap"><?= date('M j, g:i A', strtotime($order['created_at'])) ?></td>
                                    <td><?= peso($order['total_amount']) ?></td>
                                    <td><span class="badge <?= $statusBadge[$order['order_status']] ?? 'bg-secondary-subtle text-secondary' ?>"><?= htmlspecialchars($order['order_status']) ?></span></td>
                                    <td>
                                        <?= $order['payment_type'] === 'QRPH' ? 'QR PH' : htmlspecialchars($order['payment_type']) ?>
                                        <span class="badge <?= $order['payment_status'] === 'Paid' ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' ?>"><?= htmlspecialchars($order['payment_status']) ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            </main>
        </div>
    </div>

    <script src="../src/assets/javascript/admin/left-side-bar.js?v=5"></script>
    <script src="../src/assets/javascript/admin/top-navbar.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>const DAILY_SALES = <?= json_encode($dashboard['daily']) ?>;</script>
    <script src="../src/assets/javascript/admin/dashboard-chart.js"></script>
</body>
</html>
