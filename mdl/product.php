<?php
require_once __DIR__ . '/../lib/mvc/table.php';

class Product extends Table
{
    public $id = '';
    public $name = '';
    public $description = '';
    public $cost = '';
    public $price = '';

    public function __CONSTRUCT()
    {
        parent::__construct('products');
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
                FROM products 
                WHERE name LIKE '%$filter%' OR description LIKE '%$filter%'";
        return $this->getQuery($qry, $start, $limit);
    }

    public function getCountFilter($filter)
    {
        $filtered = $filter == '' ? ''
            : "WHERE name LIKE '%$filter%' OR description LIKE '%$filter%''";

        $qry = "FROM products $filtered";
        return $this->getCountQuery($qry);
    }

    public function getProductsStock($filter = '', $start = '0', $limit = '10')
    {
        $filtered = $filter == '' ? ''
            : "WHERE name LIKE '%$filter%' OR description LIKE '%$filter%''";

        $qry = "SELECT * FROM v_stock $filtered";

        return $this->getQuery($qry, $start, $limit);
    }
}
?>