<?php
require_once __DIR__ . '/../config/database.php';

/**
 * Read-only sales queries for the admin dashboard.
 * "Gross sales" counts every order that wasn't cancelled.
 * Dates use MySQL's clock so they match how created_at was stored.
 */
class DashboardStats
{
    private $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    public function today()
    {
        return $this->conn->query("SELECT CURDATE() AS d")->fetch_assoc()['d'];
    }

    /** Gross sales and order count for created_at in [$from, $to) (dates, Y-m-d). */
    public function salesBetween($from, $to)
    {
        $query = "SELECT COALESCE(SUM(total_amount), 0) AS sales, COUNT(*) AS orders
                  FROM orders
                  WHERE order_status <> 'Cancelled' AND created_at >= ? AND created_at < ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ss", $from, $to);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return array('sales' => (float) $row['sales'], 'orders' => (int) $row['orders']);
    }

    /** [Y-m-d => gross sales] for days that had sales in [$from, $to). */
    public function dailySales($from, $to)
    {
        $query = "SELECT DATE(created_at) AS day, SUM(total_amount) AS sales, COUNT(*) AS orders
                  FROM orders
                  WHERE order_status <> 'Cancelled' AND created_at >= ? AND created_at < ?
                  GROUP BY DATE(created_at)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ss", $from, $to);
        $stmt->execute();
        $result = $stmt->get_result();

        $days = array();
        while ($row = $result->fetch_assoc()) {
            $days[$row['day']] = array('sales' => (float) $row['sales'], 'orders' => (int) $row['orders']);
        }
        return $days;
    }

    /** Orders that still need staff action, regardless of date. */
    public function unfulfilledCount()
    {
        $query = "SELECT COUNT(*) AS c FROM orders WHERE order_status IN ('Pending', 'Processing', 'Ready')";
        return (int) $this->conn->query($query)->fetch_assoc()['c'];
    }

    /** All-time totals, split by payment status. */
    public function allTimeTotals()
    {
        $query = "SELECT COALESCE(SUM(total_amount), 0) AS gross,
                         COALESCE(SUM(CASE WHEN payment_status = 'Paid' THEN total_amount END), 0) AS paid,
                         COALESCE(SUM(CASE WHEN payment_status = 'Pending' THEN total_amount END), 0) AS unpaid,
                         COUNT(*) AS orders
                  FROM orders WHERE order_status <> 'Cancelled'";
        $row = $this->conn->query($query)->fetch_assoc();
        return array(
            'gross'  => (float) $row['gross'],
            'paid'   => (float) $row['paid'],
            'unpaid' => (float) $row['unpaid'],
            'orders' => (int) $row['orders'],
        );
    }

    /** Best sellers by quantity in [$from, $to). */
    public function topProducts($from, $to, $limit = 5)
    {
        $limit = (int) $limit;
        $query = "SELECT oi.product_id, COALESCE(p.product_name, 'Deleted product') AS product_name,
                         SUM(oi.quantity) AS quantity, SUM(oi.quantity * oi.unit_price) AS revenue
                  FROM order_items oi
                  JOIN orders o ON o.order_id = oi.order_id
                  LEFT JOIN products p ON p.product_id = oi.product_id
                  WHERE o.order_status <> 'Cancelled' AND o.created_at >= ? AND o.created_at < ?
                  GROUP BY oi.product_id, p.product_name
                  ORDER BY quantity DESC, revenue DESC
                  LIMIT $limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ss", $from, $to);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function latestOrders($limit = 5)
    {
        $limit = (int) $limit;
        $query = "SELECT o.order_id, o.total_amount, o.order_status, o.payment_status, o.payment_type, o.created_at,
                         TRIM(CONCAT(COALESCE(ud.first_name, ''), ' ', COALESCE(ud.last_name, ''))) AS customer_name
                  FROM orders o
                  LEFT JOIN user_details ud ON ud.user_id = o.user_id
                  ORDER BY o.created_at DESC, o.order_id DESC
                  LIMIT $limit";
        return $this->conn->query($query)->fetch_all(MYSQLI_ASSOC);
    }
}
?>
