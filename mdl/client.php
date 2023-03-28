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

    public function getFilter($filter = '', $start = '0', $limit = '10')
    {
        $filtered = $filter == '' ? '' : "WHERE name LIKE '%$filter%'";

        $qry = "SELECT * 
                FROM clients 
                $filtered";

        return $this->getQuery($qry, $start, $limit);
    }

    public function getCountFilter($filter)
    {
        $filtered = $filter == '' ? '' : "WHERE name LIKE '%$filter%'";

        $qry = "FROM clients $filtered";

        return $this->getCountQuery($qry);
    }
}
?>