<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/app.php';

/**
 * Tracks failed logins per username. After LOGIN_MAX_ATTEMPTS failures the
 * username is locked for LOGIN_LOCKOUT_SECONDS.
 */
class LoginAttempt
{
    private $conn;
    private $table_name = "login_attempts";

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    /** Seconds left on the lock, or 0 if the username may try again. */
    public function secondsLocked($username)
    {
        $query = "SELECT TIMESTAMPDIFF(SECOND, NOW(), locked_until) AS remaining
                  FROM " . $this->table_name . " WHERE username = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();

        return ($row && $row['remaining'] > 0) ? (int)$row['remaining'] : 0;
    }

    /**
     * Record a failure. Returns the lock length in seconds when this failure
     * triggers a lock, otherwise 0.
     */
    public function recordFailure($username)
    {
        // A failure after an expired lock starts a fresh count
        $query = "INSERT INTO " . $this->table_name . " (username, failed_count) VALUES (?, 1)
                  ON DUPLICATE KEY UPDATE
                      failed_count = IF(locked_until IS NOT NULL AND locked_until <= NOW(), 1, failed_count + 1),
                      locked_until = IF(locked_until IS NOT NULL AND locked_until <= NOW(), NULL, locked_until)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();

        $maxAttempts = LOGIN_MAX_ATTEMPTS;
        $lockSeconds = LOGIN_LOCKOUT_SECONDS;
        $query = "UPDATE " . $this->table_name . "
                  SET locked_until = DATE_ADD(NOW(), INTERVAL ? SECOND)
                  WHERE username = ? AND failed_count >= ? AND locked_until IS NULL";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("isi", $lockSeconds, $username, $maxAttempts);
        $stmt->execute();

        return $stmt->affected_rows > 0 ? $lockSeconds : 0;
    }

    public function attemptsLeft($username)
    {
        $query = "SELECT failed_count FROM " . $this->table_name . " WHERE username = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();

        return max(0, LOGIN_MAX_ATTEMPTS - (int)($row['failed_count'] ?? 0));
    }

    public function clear($username)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE username = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $username);
        return $stmt->execute();
    }
}
?>
