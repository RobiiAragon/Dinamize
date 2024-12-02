<?php
// helpers/DbHelper.php
class DbHelper {
    private static $conn = null;
    
    public static function getConnection() {
        if (self::$conn === null) {
            try {
                self::$conn = new mysqli(
                    DB_CREDENTIALS['host'],
                    DB_CREDENTIALS['user'],
                    DB_CREDENTIALS['pass'],
                    DB_CREDENTIALS['db']
                );
                self::$conn->set_charset("utf8mb4");
            } catch (Exception $e) {
                error_log("Error de conexión: " . $e->getMessage());
                return null;
            }
        }
        return self::$conn;
    }

    public static function closeConnection() {
        if (self::$conn !== null) {
            self::$conn->close();
            self::$conn = null;
        }
    }
}