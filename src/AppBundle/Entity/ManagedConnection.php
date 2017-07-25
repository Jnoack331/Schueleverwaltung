<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 24.07.2017
 * Time: 15:35
 */

namespace AppBundle\Entity;

class ManagedConnection
{
    private $host = "localhost";
    private $user = "itverwaltung";
    private $password = "12345678";
    private $database = "itverwaltung";

    private $conn;

    public function getConnection() {
        return $this->conn;
    }

    public function __construct() {
        $this->conn = mysqli_connect($this->host, $this->user, $this->password, $this->database);
    }

    public function __destruct() {
        mysqli_close($this->conn);
    }
}