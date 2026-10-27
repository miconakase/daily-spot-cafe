<?php
require_once __DIR__ . '/../config/database.php';

class OrderItem
{
    private $conn;
    private $table_name = "order_items";

    public $order_item_id;
    public $order_id;
    public $product_id;
    public $variant_type; // 'Hot', 'Iced', or NULL
    public $size_label;   // 'Grande', 'Venti', or NULL
    public $quantity;
    public $unit_price;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . " SET order_id=?, product_id=?, variant_type=?, size_label=?, quantity=?, unit_price=?";
        $stmt = $this->conn->prepare($query);

        $stmt->bind_param("iissid", $this->order_id, $this->product_id, $this->variant_type, $this->size_label, $this->quantity, $this->unit_price);

        if ($stmt->execute()) {
            $this->order_item_id = $this->conn->insert_id;
            return true;
        }
        return false;
    }

    public function read()
    {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY order_item_id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function readByOrderId($order_id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE order_id = ? ORDER BY order_item_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function updateQuantity($order_item_id, $quantity)
    {
        $query = "UPDATE " . $this->table_name . " SET quantity=? WHERE order_item_id=?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $quantity, $order_item_id);
        return $stmt->execute();
    }

    public function readOne()
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE order_item_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->order_item_id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();

        if ($row) {
            $this->order_id = $row['order_id'];
            $this->product_id = $row['product_id'];
            $this->quantity = $row['quantity'];
            $this->unit_price = $row['unit_price'];
        }
    }

    public function update()
    {
        $query = "UPDATE " . $this->table_name . " SET order_id=?, product_id=?, quantity=?, unit_price=? WHERE order_item_id=?";
        $stmt = $this->conn->prepare($query);

        $stmt->bind_param("iiidi", $this->order_id, $this->product_id, $this->quantity, $this->unit_price, $this->order_item_id);

        return $stmt->execute();
    }

    public function delete()
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE order_item_id=?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->order_item_id);
        return $stmt->execute();
    }
}
?>