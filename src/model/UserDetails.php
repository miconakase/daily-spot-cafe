<?php
require_once __DIR__ . '/../config/database.php';

class UserDetails
{
    private $conn;
    private $table_name = "user_details";

    public $user_id;
    public $first_name;
    public $middle_name;
    public $last_name;
    public $suffix;
    public $phone_number;
    public $email;
    public $updated_at;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . " SET user_id=?, first_name=?, middle_name=?, last_name=?, suffix=?, phone_number=?, email=?";
        $stmt = $this->conn->prepare($query);

        // Stored as typed; pages escape on output
        $this->sanitize();

        $stmt->bind_param("issssss", $this->user_id, $this->first_name, $this->middle_name, $this->last_name, $this->suffix, $this->phone_number, $this->email);

        return $stmt->execute();
    }

    private function sanitize()
    {
        foreach (['first_name', 'middle_name', 'last_name', 'suffix', 'phone_number', 'email'] as $field) {
            $this->$field = trim(strip_tags($this->$field ?? ''));
        }
    }

    public function read()
    {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY updated_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function readOne()
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->user_id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();

        if ($row) {
            $this->first_name = $row['first_name'];
            $this->middle_name = $row['middle_name'];
            $this->last_name = $row['last_name'];
            $this->suffix = $row['suffix'];
            $this->phone_number = $row['phone_number'];
            $this->email = $row['email'];
            $this->updated_at = $row['updated_at'];
        }
    }

    public function update()
    {
        $query = "UPDATE " . $this->table_name . " SET first_name=?, middle_name=?, last_name=?, suffix=?, phone_number=?, email=? WHERE user_id=?";
        $stmt = $this->conn->prepare($query);

        $this->sanitize();

        $stmt->bind_param("ssssssi", $this->first_name, $this->middle_name, $this->last_name, $this->suffix, $this->phone_number, $this->email, $this->user_id);

        return $stmt->execute();
    }

    public function delete()
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE user_id=?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->user_id);
        return $stmt->execute();
    }

    public function findUserIdByEmail($email)
    {
        $query = "SELECT user_id FROM " . $this->table_name . " WHERE email = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();

        return $row ? (int)$row['user_id'] : null;
    }

    public function getFullName()
    {
        $fullName = $this->first_name;
        if (!empty($this->middle_name)) {
            $fullName .= ' ' . $this->middle_name;
        }
        $fullName .= ' ' . $this->last_name;
        if (!empty($this->suffix)) {
            $fullName .= ' ' . $this->suffix;
        }
        return $fullName;
    }
}
?>