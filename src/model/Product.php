<?php
require_once __DIR__ . '/../config/database.php';

class Product
{
    private $conn;
    private $table_name = "products";

    public $product_id;
    public $product_name;
    public $price;
    public $short_description;
    public $long_description;
    public $category_id;
    public $is_published;
    public $is_available;
    public $img_url;
    public $created_at;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . " SET product_name=?, price=?, short_description=?, long_description=?, category_id=?, is_published=?, is_available=?, img_url=?";
        $stmt = $this->conn->prepare($query);

        $this->product_name = trim(strip_tags($this->product_name ?? ""));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->short_description = trim(strip_tags($this->short_description ?? ""));
        // Rich text: ProductController stores it already cleaned by HtmlSanitizer
        $this->long_description = (string) $this->long_description;
        $this->category_id = htmlspecialchars(strip_tags($this->category_id));
        $this->is_published = htmlspecialchars(strip_tags($this->is_published));
        $this->is_available = htmlspecialchars(strip_tags($this->is_available));
        $this->img_url = htmlspecialchars(strip_tags($this->img_url));

        $stmt->bind_param("sdssiiis", $this->product_name, $this->price, $this->short_description, $this->long_description, $this->category_id, $this->is_published, $this->is_available, $this->img_url);

        if ($stmt->execute()) {
            $this->product_id = $this->conn->insert_id;
            return true;
        }
        return false;
    }

    public function read()
    {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function readPublished()
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE is_published = 1 AND is_available = 1 ORDER BY product_name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function readByCategory($category_id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE category_id = ? AND is_published = 1 AND is_available = 1 ORDER BY product_name";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $category_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function readOne()
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE product_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->product_id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();

        if ($row) {
            $this->product_name = $row['product_name'];
            $this->price = $row['price'];
            $this->short_description = $row['short_description'];
            $this->long_description = $row['long_description'];
            $this->category_id = $row['category_id'];
            $this->is_published = $row['is_published'];
            $this->is_available = $row['is_available'];
            $this->img_url = $row['img_url'];
            $this->created_at = $row['created_at'];
        }
    }

    public function update()
    {
        $query = "UPDATE " . $this->table_name . " SET product_name=?, price=?, short_description=?, long_description=?, category_id=?, is_published=?, is_available=?, img_url=? WHERE product_id=?";
        $stmt = $this->conn->prepare($query);

        $this->product_name = trim(strip_tags($this->product_name ?? ""));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->short_description = trim(strip_tags($this->short_description ?? ""));
        // Rich text: ProductController stores it already cleaned by HtmlSanitizer
        $this->long_description = (string) $this->long_description;
        $this->category_id = htmlspecialchars(strip_tags($this->category_id));
        $this->is_published = htmlspecialchars(strip_tags($this->is_published));
        $this->is_available = htmlspecialchars(strip_tags($this->is_available));
        $this->img_url = htmlspecialchars(strip_tags($this->img_url));

        $stmt->bind_param("sdssiiisi", $this->product_name, $this->price, $this->short_description, $this->long_description, $this->category_id, $this->is_published, $this->is_available, $this->img_url, $this->product_id);

        return $stmt->execute();
    }

    public function delete()
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE product_id=?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->product_id);
        return $stmt->execute();
    }

    public function search($keywords)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE product_name LIKE ? OR short_description LIKE ? OR long_description LIKE ? AND is_published = 1 AND is_available = 1 ORDER BY product_name";
        $stmt = $this->conn->prepare($query);

        $keywords = "%{$keywords}%";
        $stmt->bind_param("sss", $keywords, $keywords, $keywords);
        $stmt->execute();
        return $stmt->get_result();
    }
}
?>