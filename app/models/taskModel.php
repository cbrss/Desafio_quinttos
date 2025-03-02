<?php


class TaskModel {
    private $db;

    public function __construct(DatabaseInterface $db) {
        $this->db = $db;
    }

    public function findAll() {
        try{
            $this->db->connect();
            $tasks = $this->db->query("SELECT * FROM task")->fetchall();
            $this->db->disconnect();

            return $tasks;
        } catch (Exception $e) {
            throw new Exception("Error fetching tasks: " . $e->getMessage());
        }
    }
    
    public function findAllByUserId($userId) {
        try {
            $this->db->connect();
            $tasks = $this->db->query("SELECT * FROM task WHERE user_id = ?", [$userId])->fetchall();
            $this->db->disconnect();
    
            return $tasks;
        } catch (Exception $e) {
            throw new Exception("Error fetching tasks for user: " . $e->getMessage());
        }
    }

    public function find($id) {
        try {
            $this->db->connect();
            $task = $this->db->query("SELECT * FROM task WHERE id = ?", [$id])->fetch();
            $this->db->disconnect();

            return $task ? $task : null;
        } catch (Exception $e) {
            throw new Exception("Error fetching task: " . $e->getMessage());
        }
    }

    public function save($title, $description, $userId) {
        try {
            $this->db->connect();
            $ret = $this->db->query("INSERT INTO task (title, description, user_id) VALUES (?, ?, ?)", [$title, $description, $userId]);
            $this->db->disconnect();
            
            return $ret;
        } catch (Exception $e) {
            throw new Exception("Error saving task: " . $e->getMessage());
        }
    }

    public function update($id, $status) {
        try {
            $this->db->connect();
            $completedAt = ($status == 'completed' ? date("Y-m-d H:i:s"): null);
            $ret = $this->db->query("UPDATE task SET status = ?, completed_at = ? WHERE id = ?", [$status, $completedAt, $id]);
            $this->db->disconnect();

            return $ret;
        } catch (Exception $e) {
            throw new Exception("Error updating task: $e->getMessage()");
        }
        
    }

    public function delete($id) {
        try {
            $this->db->connect();

            $deletedAt = date("Y-m-d H:i:s");
            $ret = $this->db->query("UPDATE task SET status = 'deleted', deleted_at = ? WHERE id = ?", [$deletedAt, $id]);
            $this->db->disconnect();

            return $ret;
        } catch (Exception $e) {
            throw new Exception("Error deleting task: " . $e->getMessage());    
        }

     }
}
?>