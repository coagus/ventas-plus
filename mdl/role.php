<?php
require_once __DIR__ . '/../lib/mvc/table.php';

class Role extends Table
{
    public $id = '';
    public $role = '';

    public function __CONSTRUCT()
    {
        parent::__construct('roles');
    }
}
?>