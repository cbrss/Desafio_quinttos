<?php

class UserModelDTO {
    public $id;
    public $username;

    public function __construct($id, $username) {
        $this->id = $id;
        $this->username = $username;
    }
}
?>