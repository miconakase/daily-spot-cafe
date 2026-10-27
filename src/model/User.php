<?php
require_once __DIR__ . '/../config/database.php';

class User
{
    private $conn;
    private $table_name = "users";

    public $user_id;
    public $username;
    public $password;
    public $role_id;
    public $created_at;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . " SET username=?, password=?, role_id=?";
        $stmt = $this->conn->prepare($query);

        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->password = htmlspecialchars(strip_tags($this->password));
        $this->role_id = htmlspecialchars(strip_tags($this->role_id));

        $stmt->bind_param("ssi", $this->username, $this->password, $this->role_id);

        if ($stmt->execute()) {
            $this->user_id = $this->conn->insert_id;
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

    public function readOne()
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->user_id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();

        if ($row) {
            $this->username = $row['username'];
            $this->password = $row['password'];
            $this->role_id = $row['role_id'];
            $this->created_at = $row['created_at'];
        }
    }

    public function update()
    {
        $query = "UPDATE " . $this->table_name . " SET username=?, password=?, role_id=? WHERE user_id=?";
        $stmt = $this->conn->prepare($query);

        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->password = htmlspecialchars(strip_tags($this->password));
        $this->role_id = htmlspecialchars(strip_tags($this->role_id));

        $stmt->bind_param("ssii", $this->username, $this->password, $this->role_id, $this->user_id);

        return $stmt->execute();
    }

    public function delete()
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE user_id=?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->user_id);
        return $stmt->execute();
    }

    public function authenticate($username, $password)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE username = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $stored = $row['password'];
            $isHash = password_get_info($stored)['algo'] !== null;

            if ($isHash) {
                $valid = password_verify($password, $stored);
            } else {
                // Legacy plain-text password (old schema): accept once, then upgrade
                $valid = hash_equals($stored, (string) $password);
            }

            if ($valid && (!$isHash || password_needs_rehash($stored, PASSWORD_DEFAULT))) {
                $row['password'] = password_hash($password, PASSWORD_DEFAULT);
                $this->updatePassword($row['user_id'], $row['password']);
            }

            if ($valid) {
                $this->user_id = $row['user_id'];
                $this->username = $row['username'];
                $this->password = $row['password'];
                $this->role_id = $row['role_id'];
                $this->created_at = $row['created_at'];
                return true;
            }
        }
        return false;
    }

    public function updatePassword($userId, $passwordHash)
    {
        $query = "UPDATE " . $this->table_name . " SET password=? WHERE user_id=?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $passwordHash, $userId);
        return $stmt->execute();
    }

    public function findIdByUsername($username)
    {
        $query = "SELECT user_id FROM " . $this->table_name . " WHERE username = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return $row ? (int)$row['user_id'] : null;
    }

    public function usernameExists($username)
    {
        $query = "SELECT user_id FROM " . $this->table_name . " WHERE username = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    public function getUsersByRole($role_id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE role_id = ? ORDER BY username";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $role_id);
        $stmt->execute();
        return $stmt->get_result();
    }
}
?>