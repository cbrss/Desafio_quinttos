<?php

class TaskModelDTO {
    public $id;
    public $title;
    public $description;
    public $status;

    public function __construct($id, $title, $description, $status) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->status = $status;
    }
}
?>