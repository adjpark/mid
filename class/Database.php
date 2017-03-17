<?php

//https://gist.github.com/jonashansen229/4534794

class Database
{
    private $connection;
    private static $instance;
    private $database_host = "localhost";
    private $database_user = "root";
    private $database_pass = "root";
    private $database_name = "mid_app";
    
    //Get instance
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    //Constructor
    private function __construct()
    {
        try {
            $this->connection = new PDO('mysql:host=' . $this->database_host . ';dbname=' . $this->database_name, $this->database_user, $this->database_pass);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            if(session_id() == '' || !isset($_SESSION)) {
                session_start();
            }
        }
        catch (PDOException $e) {
            die($e->getMessage());
        }
    }
    
    //Clone is empty to prevent duplication of connection
    private function __clone()
    {
    }
    
    //Get PDO connection
    public function getConnection()
    {
        return $this->connection;
    }
}

$database = Database::getInstance();
$conn = $database->getConnection();
?>