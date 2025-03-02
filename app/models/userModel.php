<?php

class UserModel {
    private $db;

    public function __construct(DatabaseInterface $db) {
        $this->db = $db;
    }

    public function find($id) {
        try {
            $this->db->connect();
            $user = $this->db->query("SELECT * FROM user WHERE id = ?", [$id])->fetch();
            $this->db->disconnect();

            return $user ? $user : null;
        } catch (Exception $e) {
            throw new Exception("Error fetching task: " . $e->getMessage());
        }
    }

    public function findByUsername($username) {
        try {
            $this->db->connect();
            $user = $this->db->query("SELECT * FROM user WHERE username = ?", [$username])->fetch();
            $this->db->disconnect();

            return $user ? $user : null; 
        } catch (Exception $e) {
            throw new Exception("Error fetching user: " . $e->getMessage());
        }
    }

    public function register($username, $password) {
        try {
            $this->db->connect();
            $ret = $this->db->query("INSERT INTO user (username, password) VALUES (?, ?)", [$username, $password]);
            $this->db->disconnect();
    
            return $ret;
        } catch (Exception $e) {
            throw new Exception("Error saving user: " . $e->getMessage());
        }
    }

}
?>