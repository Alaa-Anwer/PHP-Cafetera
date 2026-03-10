<?php

class Database
{

    private $host = "localhost";
    private $db = "cafeteria";
    private $user = "root";
    private $pass = "";

    public function connect()
    {

        $conn = new PDO(
            "mysql:host=" . $this->host . ";dbname=" . $this->db,
            $this->user,
            $this->pass
        );

        return $conn;
    }
}
