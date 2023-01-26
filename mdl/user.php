<?php
require_once __DIR__ . '/../lib/mvc/table.php';

class User extends Table
{
    public $id = '';
    public $username = '';
    public $password = '';
    public $name = '';
    //public $Rol = '';

    public function __CONSTRUCT()
    {
        parent::__construct('users');
    }

    public function save()
    {
        return $this->saveData($this->id, get_object_vars($this));
    }

    public function getWhere()
    {
        return $this->getRowsWhere(get_object_vars($this));
    }

    public function getFilter($filter, $start = '0', $limit = '10')
    {
        $qry = "SELECT * 
                FROM users 
                WHERE username LIKE '%$filter%' OR name LIKE '%$filter%'";
        return $this->getQuery($qry, $start, $limit);
    }

    public function getCountFilter($filter)
    {
        $qry = "FROM users WHERE username LIKE '%$filter%' OR name LIKE '%$filter%'";
        return $this->getCountQuery($qry);
    }
}
?>