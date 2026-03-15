<?php
class Database
{
    private $host = "127.0.0.1";
    private $db = "cafeteria";
    private $user = "root";
    private $pass = "";
    private $port = "3306";

    public function connect()
    {
        $conn = new PDO(
            "mysql:host=" .
                $this->host .
                ";port=" .
                $this->port .
                ";dbname=" .
                $this->db,
            $this->user,
            $this->pass,
        );
        return $conn;
    }
}
