<?php
require_once __DIR__ . '/../lib/mvc/table.php';

class Supplier extends Table
{
    public $id = '';
    public $name = '';
    public $description = '';
    public $phone = '';

    public function __CONSTRUCT()
    {
        parent::__construct('suppliers');
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
                FROM suppliers 
                WHERE name LIKE '%$filter%' OR description LIKE '%$filter%'";
        return $this->getQuery($qry, $start, $limit);
    }

    public function getCountFilter($filter)
    {
        $qry = "FROM suppliers WHERE name LIKE '%$filter%' OR description LIKE '%$filter%'";
        return $this->getCountQuery($qry);
    }
}
?>