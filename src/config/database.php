<?php
date_default_timezone_set('Asia/Manila');
class Database
{
    private static $host = "localhost";
    private static $db_name = "daily_spot_cafe";
    private static $username = "root";
    private static $password = "admin123";
    private static $conn;

    public static function getConnection()
    {
        if (!self::$conn) {
            self::$conn = new mysqli(self::$host, self::$username, self::$password, self::$db_name);
            if (self::$conn->connect_error) {
                die("Connection failed: " . self::$conn->connect_error);
            }
            // Explicit so ₱, é, ñ work even where the server defaults to latin1 (e.g. XAMPP)
            self::$conn->set_charset('utf8mb4');
        }
        return self::$conn;
    }
}
?>