<?php

class TaskModel {
    private $db;

    public function __construct(DatabaseInterface $db) {
        $this->db = $db;
    }

    public function findAll() {
        return $this->db->query("SELECT * FROM task")->fetchall();
    }

    public function find($id) {
        return $this->db->query("SELECT * FROM task WHERE id = ?", [$id])->fetch();
    }

    public function save($title, $description) {
        return $this->db->query("INSERT INTO task (title, description) VALUES (?, ?)", [$title, $description]);
    }

    public function update($id, $status) {
        $completedAt = ($status == 'completed' ? date("Y-m-d H:i:s"): null);

        return $this->db->query("UPDATE task SET status = ?, completed_at = ? WHERE id = ?", [$status, $completedAt, $id]);
    }

    public function delete($id) {
        $deletedAt = date("Y-m-d H:i:s");

        return $this->db->query("UPDATE task SET status = 'deleted', deleted_at = ? WHERE id = ?", [$deletedAt, $id]);
    }
}
?>


<?php
