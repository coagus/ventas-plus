<?php
require_once __DIR__ . '/../lib/mvc/table.php';

class Client extends Table
{
    public $id = '';
    public $name = '';
    public $phone = '';
    public $address = '';
    public $reference = '';

    public function __CONSTRUCT()
    {
        parent::__construct('clients');
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
                FROM clients 
                WHERE name LIKE '%$filter%'";
        return $this->getQuery($qry, $start, $limit);
    }

    public function getCountFilter($filter)
    {
        $qry = "FROM clients WHERE name LIKE '%$filter%'";
        return $this->getCountQuery($qry);
    }
}
?>