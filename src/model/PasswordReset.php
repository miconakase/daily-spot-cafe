<?php
require_once __DIR__ . '/../config/database.php';

/**
 * One-time password reset tokens. Only a SHA-256 hash of each token is
 * stored, so a leaked table cannot be used to reset passwords.
 */
class PasswordReset
{
    private $conn;
    private $table_name = "password_resets";

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    /** Creates a token for the user and returns the raw token (to put in the link). */
    public function create($userId, $ttlMinutes)
    {
        // Only the newest link should work
        $query = "UPDATE " . $this->table_name . " SET used_at = NOW() WHERE user_id = ? AND used_at IS NULL";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();

        $token = bin2hex(random_bytes(32));
        $hash  = hash('sha256', $token);

        $query = "INSERT INTO " . $this->table_name . " (user_id, token_hash, expires_at)
                  VALUES (?, ?, DATE_ADD(NOW(), INTERVAL ? MINUTE))";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("isi", $userId, $hash, $ttlMinutes);

        return $stmt->execute() ? $token : null;
    }

    /** Returns the user_id for a valid, unused, unexpired token, or null. */
    public function findValidUserId($token)
    {
        $hash  = hash('sha256', (string)$token);
        $query = "SELECT user_id FROM " . $this->table_name . "
                  WHERE token_hash = ? AND used_at IS NULL AND expires_at > NOW()";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $hash);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();

        return $row ? (int)$row['user_id'] : null;
    }

    public function markUsed($token)
    {
        $hash  = hash('sha256', (string)$token);
        $query = "UPDATE " . $this->table_name . " SET used_at = NOW() WHERE token_hash = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $hash);
        return $stmt->execute();
    }
}
?>
