<?php
require_once __DIR__ . '/../config/database.php';

class Order
{
    private $conn;
    private $table_name = "orders";

    public $order_id;
    public $user_id;
    public $payment_status;
    public $payment_type;
    public $reference_number;
    public $total_amount;
    public $order_status;
    public $notes;
    public $created_at;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . "
                  SET user_id=?, payment_status=?, payment_type=?, reference_number=?, total_amount=?, order_status=?, notes=?";
        $stmt = $this->conn->prepare($query);

        $this->payment_status = htmlspecialchars(strip_tags($this->payment_status ?? 'Pending'));
        $this->payment_type   = htmlspecialchars(strip_tags($this->payment_type   ?? 'COD'));
        $this->total_amount   = htmlspecialchars(strip_tags($this->total_amount));
        $this->order_status   = htmlspecialchars(strip_tags($this->order_status   ?? 'Pending'));
        // Free text is stored as typed; pages escape on output
        $this->notes          = trim(strip_tags($this->notes ?? ''));

        $stmt->bind_param(
            "isssdss",
            $this->user_id,
            $this->payment_status,
            $this->payment_type,
            $this->reference_number,
            $this->total_amount,
            $this->order_status,
            $this->notes
        );

        if ($stmt->execute()) {
            $this->order_id = $this->conn->insert_id;
            return true;
        }
        return false;
    }

    public function read()
    {
        $query = "SELECT o.*,
                         CONCAT(ud.first_name, ' ',
                                IFNULL(CONCAT(ud.last_name, ''), ud.last_name)) AS customer_name,
                         ud.first_name, ud.last_name
                  FROM " . $this->table_name . " o
                  LEFT JOIN user_details ud ON ud.user_id = o.user_id
                  ORDER BY o.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function readOne()
    {
        $query = "SELECT o.*,
                         CONCAT(ud.first_name, ' ', ud.last_name) AS customer_name
                  FROM " . $this->table_name . " o
                  LEFT JOIN user_details ud ON ud.user_id = o.user_id
                  WHERE o.order_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->order_id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();

        if ($row) {
            $this->user_id        = $row['user_id'];
            $this->payment_status = $row['payment_status'];
            $this->payment_type   = $row['payment_type'];
            $this->reference_number = $row['reference_number'];
            $this->total_amount   = $row['total_amount'];
            $this->order_status   = $row['order_status'];
            $this->notes          = $row['notes'];
            $this->created_at     = $row['created_at'];
        }
    }

    public function update()
    {
        $query = "UPDATE " . $this->table_name . "
                  SET payment_status=?, payment_type=?, total_amount=?, order_status=?, notes=?
                  WHERE order_id=?";
        $stmt = $this->conn->prepare($query);

        $this->payment_status = htmlspecialchars(strip_tags($this->payment_status));
        $this->payment_type   = htmlspecialchars(strip_tags($this->payment_type));
        $this->total_amount   = htmlspecialchars(strip_tags($this->total_amount));
        $this->order_status   = htmlspecialchars(strip_tags($this->order_status));
        $this->notes          = trim(strip_tags($this->notes ?? ''));

        $stmt->bind_param(
            "ssdssi",
            $this->payment_status,
            $this->payment_type,
            $this->total_amount,
            $this->order_status,
            $this->notes,
            $this->order_id
        );

        return $stmt->execute();
    }

    public function delete()
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE order_id=?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->order_id);
        return $stmt->execute();
    }

    public function readByUser($user_id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE user_id = ? ORDER BY created_at DESC, order_id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getOrdersByStatus($status)
    {
        $query = "SELECT o.*,
                         CONCAT(ud.first_name, ' ', ud.last_name) AS customer_name
                  FROM " . $this->table_name . " o
                  LEFT JOIN user_details ud ON ud.user_id = o.user_id
                  WHERE o.order_status = ?
                  ORDER BY o.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $status);
        $stmt->execute();
        return $stmt->get_result();
    }
}
?>