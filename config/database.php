<?php

    class Database {
        private $db_host = "localhost";
        private $db_user = "root";
        private $db_pass = "";
        private $db_name = "api_project";
        public $conn;

        public function connectDatabase() {
            $this->conn = null;
            try {
                $dsn = "mysql:host=$this->db_host;dbname=$this->db_name";
                $this->conn = new PDO($dsn, $this->db_user, $this->db_pass);
                $this->conn->exec("set names UTF8");
            } catch (PDOException $err) {
                echo "Connection error: " . $err->getMessage();
            }
            return $this->conn;
        }
    }

?>
