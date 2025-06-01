<?php

require __DIR__ . '/config.php';  // Load configuration and environment variables

class Database {
    private $host;
    private $user;
    private $pass;
    private $dbname;
    public $link;
    public $error;

    public function __construct() {
        $this->host = $_ENV['DB_HOST'];
        $this->user = $_ENV['DB_USER'];
        $this->pass = $_ENV['DB_PASS'];
        $this->dbname = $_ENV['DB_NAME'];
        $this->connectDB();
    }

    private function connectDB() {
        $this->link = new mysqli($this->host, $this->user, $this->pass, $this->dbname);
        if ($this->link->connect_error) {
            $this->error = "Connection failed: " . $this->link->connect_error;
            return false;
        }
    }

    public function select($query) {
        $result = $this->link->query($query) or die($this->link->error.__LINE__);
        return ($result->num_rows > 0) ? $result : false;
    }

    public function insert($query) {
    $insert_row = $this->link->query($query) or die($this->link->error.__LINE__);
        return $insert_row ? $insert_row : false;
    }

    public function update($query) {
        $update_row = $this->link->query($query) or die($this->link->error.__LINE__);
        return $update_row ? $update_row : false;
    }

    public function delete($query) {
        $delete_row = $this->link->query($query) or die($this->link->error.__LINE__);
        return $delete_row ? $delete_row : false;
    }

}
