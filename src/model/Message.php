<?php
require_once __DIR__ . '/../config/database.php';

class Message
{
    private $conn;
    private $table_name = "messages";

    public $message_id;
    public $user_id;
    public $name;
    public $email;
    public $subject;
    public $message;
    public $is_read;
    public $created_at;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    // Values are stored as typed; escape them when displaying.
    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . " SET user_id=?, name=?, email=?, subject=?, message=?";
        $stmt = $this->conn->prepare($query);

        $stmt->bind_param("issss", $this->user_id, $this->name, $this->email, $this->subject, $this->message);

        if ($stmt->execute()) {
            $this->message_id = $this->conn->insert_id;
            return true;
        }
        return false;
    }

    public function read($limit = null)
    {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC, message_id DESC";
        if ($limit !== null) {
            $query .= " LIMIT " . (int) $limit;
        }
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function readOne($id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE message_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function unreadCount()
    {
        $query = "SELECT COUNT(*) AS count FROM " . $this->table_name . " WHERE is_read = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return (int) $stmt->get_result()->fetch_assoc()['count'];
    }

    public function markAsRead($id)
    {
        $query = "UPDATE " . $this->table_name . " SET is_read = 1 WHERE message_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function markAllAsRead()
    {
        $query = "UPDATE " . $this->table_name . " SET is_read = 1 WHERE is_read = 0";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute();
    }

    public function delete($id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE message_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>
