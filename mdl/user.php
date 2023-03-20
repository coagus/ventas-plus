<?php
require_once __DIR__ . '/../lib/mvc/table.php';

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
        return $this->saveData($this->id, get_object_vars($this));
    }

    public function getWhere()
    {
        return $this->getRowsWhere(get_object_vars($this));
    }

    public function getAll($start = '0', $limit = '10')
    {
        $qry = "SELECT u.id, u.username, u.password, u.name, r.role 
                FROM users u JOIN roles r ON u.role_id = r.id 
                ORDER BY r.id";
        return $this->getQuery($qry, $start, $limit);
    }

    public function getFilter($filter, $start = '0', $limit = '10')
    {
        $qry = "SELECT u.id, u.username, u.password, u.name, r.role 
                FROM users u JOIN roles r ON u.role_id = r.id 
                WHERE u.username LIKE '%$filter%' 
                OR u.name LIKE '%$filter%'
                OR r.role LIKE '%$filter%'
                ORDER BY r.id";
        return $this->getQuery($qry, $start, $limit);
    }

    public function getCountFilter($filter)
    {
        $qry = "FROM users u JOIN roles r ON u.role_id = r.id 
                WHERE u.username LIKE '%$filter%' 
                OR u.name LIKE '%$filter%'
                OR r.role LIKE '%$filter%'";
        return $this->getCountQuery($qry);
    }
}
?>