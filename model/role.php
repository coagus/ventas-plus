<?php
require_once __DIR__ . '/../modules/database/table.php';

class Role extends Table
{
    public $id = '';
    public $role = '';

    public function __CONSTRUCT()
    {
        parent::__construct('roles');
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