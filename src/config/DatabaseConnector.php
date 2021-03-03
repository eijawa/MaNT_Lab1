<?php
class DatabaseConnector {
    private $conn;

    public function __construct() {
        $host = getenv('DB_HOST');
        $db_name = getenv('DB_NAME');
        $username = getenv('DB_USERNAME');
        $userpassword = getenv('DB_USERPASSWORD');

        try {
            $this->conn = new PDO("mysql:host=$host;charset=utf8mb4;dbname=$db_name", $username, $userpassword);
        } catch(PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function getConn() {
        return $this->conn;
    }
}
?>