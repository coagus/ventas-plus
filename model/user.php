<?php
require_once __DIR__ . '/../modules/database/table.php';

class User extends Table
{
    public $id = '';
    public $username = '';
    public $password = '';
    public $name = '';
    public $role_id = '';

    public function __CONSTRUCT()
    {
        parent::__construct('users');
    }

    public function save()
    {
        return $this->saveData(get_object_vars($this));
    }

    public function getWhere()
    {
        return $this->getDataWhere(get_object_vars($this));
    }
}
?>