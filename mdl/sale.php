<?php
require_once __DIR__ . '/../lib/mvc/table.php';

class Sale extends Table
{
    public $id = '';
    public $status_id = '';
    public $client_id = '';
    public $user_id = '';
    public $date = '';

    public function __CONSTRUCT()
    {
        parent::__construct('sales');
    }

    public function save()
    {
        return $this->saveData($this->id, get_object_vars($this));
    }

    public function getWhere()
    {
        return $this->getRowsWhere(get_object_vars($this));
    }

    public function getSaleById($id)
    {
        $qry = "SELECT s.id, s.status_id, s.date, ss.status, c.name AS client, u.username
                FROM sales s 
                JOIN sales_status ss ON ss.id = s.status_id
                JOIN clients c ON c.id = s.client_id
                JOIN users u ON u.id = s.user_id
                WHERE s.id = $id";

        return $this->getQuery($qry)[0];
    }

    public function getSales($start = '0', $limit = '10', $filter = '')
    {
        $filtered = $filter == '' ? '' : "WHERE c.name LIKE '%$filter%'";

        $qry = "SELECT s.id, s.date, s.status_id, ss.status, c.name AS client, u.username
                FROM sales s 
                JOIN sales_status ss ON ss.id = s.status_id
                JOIN clients c ON c.id = s.client_id
                JOIN users u ON u.id = s.user_id
                $filtered
                ORDER BY ss.id ASC, s.date DESC";

        return $this->getQuery($qry, $start, $limit);
    }

    public function getCountSales($filter = '')
    {
        $filtered = $filter == '' ? '' : "WHERE c.name LIKE '%$filter%'";

        $qry = "FROM sales s 
                JOIN sales_status ss ON ss.id = s.status_id
                JOIN clients c ON c.id = s.client_id
                JOIN users u ON u.id = s.user_id
                $filtered";

        return $this->getCountQuery($qry);
    }

    public function getProductToAdd($saleId, $start = '0', $limit = '10', $filter = '')
    {
        $filtered = $filter == '' ? '' : "AND p.name LIKE '%$filter%'";
        $qry = "SELECT p.*
                FROM v_stock p 
                WHERE NOT EXISTS (
                SELECT 1 FROM movements m 
                WHERE m.sale_id = $saleId
                AND m.product_id = p.id)
                $filtered";
        return $this->getQuery($qry, $start, $limit);
    }

    public function getCountProductToAdd($saleId, $start = '0', $limit = '10', $filter = '')
    {
        $filtered = $filter == '' ? '' : "AND p.name LIKE '%$filter%'";
        $qry = "FROM v_stock p 
                WHERE NOT EXISTS (
                SELECT 1 FROM movements m 
                WHERE m.sale_id = $saleId
                AND m.product_id = p.id)
                $filtered";
        return $this->getCountQuery($qry);
    }

    public function getProduct($saleId, $start = '0', $limit = '10', $filter = '')
    {
        $filtered = $filter == '' ? '' : "AND p.name LIKE '%$filter%'";
        $qry = "SELECT p.*, m.quantity, m.id AS movId, m.product_price
                FROM v_stock p 
                JOIN movements m ON m.product_id = p.id AND m.type_id = '2'
                WHERE m.sale_id = $saleId
                $filtered";
        return $this->getQuery($qry, $start, $limit);
    }

    public function getCountProduct($saleId, $start = '0', $limit = '10', $filter = '')
    {
        $filtered = $filter == '' ? '' : "AND p.name LIKE '%$filter%'";
        $qry = "FROM v_stock p 
                JOIN movements m ON m.product_id = p.id AND m.type_id = '2'
                WHERE m.sale_id = $saleId
                $filtered";
        return $this->getCountQuery($qry);
    }

    public function finish($saleId)
    {
        $qry = "UPDATE sales SET status_id = 2 where id = $saleId";
        $this->executeQry($qry);
        date_default_timezone_set('America/Guatemala');
        $date = date("Y-m-d H:i:s");
        $qry = "UPDATE movements SET apply = 1, date_apply = '$date' WHERE sale_id = $saleId";
        $this->executeQry($qry);
    }

    public function edit($saleId)
    {
        $qry = "UPDATE sales SET status_id = 1 where id = $saleId";
        $this->executeQry($qry);
        $qry = "UPDATE movements SET apply = 0, date_apply = null WHERE sale_id = $saleId";
        $this->executeQry($qry);
    }

}
?>