<?php


class Database {

    protected $db;
    function __construct(){
        $this->openDatabaseConnection();
    }

    private function openDatabaseConnection()
    {
        $this->db = new mysqli($_SERVER['DB_HOST'], $_SERVER['DB_USER'], $_SERVER['DB_PASS'], $_SERVER['DB_NAME']);
        if ($this->db->connect_errno) {
            echo "Failed to connect to MySQL: " . $this->db->connect_error;
            die(500);
        }
    }
    public function getDbConnection(){
        return $this->db;
    }
    private function closeDatabaseConnection()
    {
        $this->db->close();    
    }

}