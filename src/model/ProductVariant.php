<?php
require_once __DIR__ . '/../config/database.php';

class ProductVariant
{
    private $conn;
    private $table_name = "product_variants";

    public $variant_id;
    public $product_id;
    public $variant_type; // e.g. 'Iced', 'Hot', or NULL
    public $size_label;   // e.g. 'Grande', 'Venti', or NULL
    public $price;
    public $is_available;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    /**
     * Insert a single variant row for a product.
     */
    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . "
                  (product_id, variant_type, size_label, price, is_available)
                  VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);

        $this->product_id   = (int) $this->product_id;
        $this->variant_type = $this->variant_type !== null ? trim(strip_tags($this->variant_type)) : null;
        $this->size_label   = $this->size_label   !== null ? trim(strip_tags($this->size_label))   : null;
        $this->price        = (float) $this->price;
        $this->is_available = isset($this->is_available) ? (int) $this->is_available : 1;

        $stmt->bind_param("issdi",
            $this->product_id,
            $this->variant_type,
            $this->size_label,
            $this->price,
            $this->is_available
        );

        if ($stmt->execute()) {
            $this->variant_id = $this->conn->insert_id;
            return true;
        }
        return false;
    }

    /**
     * Get all variants for a product.
     */
    public function readByProduct($product_id)
    {
        $query = "SELECT * FROM " . $this->table_name . "
                  WHERE product_id = ?
                  ORDER BY variant_type, size_label";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    /**
     * Find one variant row by its option combination; NULL options match NULL.
     */
    public function findOne($product_id, $variant_type, $size_label)
    {
        $query = "SELECT * FROM " . $this->table_name . "
                  WHERE product_id = ? AND variant_type <=> ? AND size_label <=> ?
                  LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iss", $product_id, $variant_type, $size_label);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Delete all variants for a product (used before re-saving).
     */
    public function deleteByProduct($product_id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE product_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $product_id);
        return $stmt->execute();
    }

    /**
     * Replace all variants for a product.
     * $variants = array of ['variant_type'=>..., 'size_label'=>..., 'price'=>..., 'is_available'=>...]
     */
    public function saveVariants($product_id, array $variants)
    {
        $this->deleteByProduct($product_id);

        foreach ($variants as $v) {
            $this->product_id   = $product_id;
            $this->variant_type = $v['variant_type'] ?? null;
            $this->size_label   = $v['size_label']   ?? null;
            $this->price        = $v['price']         ?? 0;
            $this->is_available = $v['is_available']  ?? 1;
            $this->create();
        }
        return true;
    }
}
?>
