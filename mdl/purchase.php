<?php
require_once __DIR__ . '/../lib/mvc/table.php';

class Purchase extends Table
{
    public $id = '';
    public $status_id = '';
    public $supplier_id = '';
    public $user_id = '';
    public $date = '';

    public function __CONSTRUCT()
    {
        parent::__construct('purchases');
    }

    public function save()
    {
        return $this->saveData($this->id, get_object_vars($this));
    }

    public function getWhere()
    {
        return $this->getRowsWhere(get_object_vars($this));
    }

    public function getPurchaseById($id)
    {
        $qry = "SELECT p.id, p.date, ps.status, s.name AS supplier, u.username
                FROM purchases p 
                JOIN purchases_status ps ON ps.id = p.status_id
                JOIN suppliers s ON s.id = p.supplier_id
                JOIN users u ON u.id = p.user_id
                WHERE p.id = $id";
        return $this->getQuery($qry)[0];
    }

    public function getAll($start = '0', $limit = '10')
    {
        $qry = "SELECT p.id, p.date, ps.status, s.name AS supplier, u.username
                FROM purchases p 
                JOIN purchases_status ps ON ps.id = p.status_id
                JOIN suppliers s ON s.id = p.supplier_id
                JOIN users u ON u.id = p.user_id
                ORDER BY ps.id ASC, p.date DESC";
        return $this->getQuery($qry, $start, $limit);
    }

    public function getFilter($filter, $start = '0', $limit = '10')
    {
        $qry = "SELECT p.id, p.date, ps.status, s.name AS supplier, u.username
                FROM purchases p 
                JOIN purchases_status ps ON ps.id = p.status_id
                JOIN suppliers s ON s.id = p.supplier_id
                JOIN users u ON u.id = p.user_id
                WHERE p.date LIKE '%$filter%'
                OR ps.status LIKE '%$filter%'
                OR s.name LIKE '%$filter%'
                OR u.username LIKE '%$filter%'
                ORDER BY ps.id ASC, p.date DESC";
        return $this->getQuery($qry, $start, $limit);
    }

    public function getCountFilter($filter)
    {
        $qry = "FROM purchases p 
                JOIN purchases_status ps ON ps.id = p.status_id
                JOIN suppliers s ON s.id = p.supplier_id
                JOIN users u ON u.id = p.user_id
                WHERE p.date LIKE '%$filter%'
                OR ps.status LIKE '%$filter%'
                OR s.name LIKE '%$filter%'
                OR u.username LIKE '%$filter%'";
        return $this->getCountQuery($qry);
    }

    public function getProductToAdd($purchaseId, $start = '0', $limit = '10', $filter = '')
    {
        $filtered = $filter == '' ? '' : "AND p.name LIKE '%$filter%'";

        $qry = "SELECT p.*
                FROM products p 
                WHERE NOT EXISTS (
                    SELECT 1 FROM movements m 
                    WHERE m.purchase_id = $purchaseId
                    AND m.product_id = p.id)
                $filtered";
        return $this->getQuery($qry, $start, $limit);
    }

    public function getCountProductToAdd($purchaseId, $start = '0', $limit = '10', $filter = '')
    {
        $filtered = $filter == '' ? '' : "AND p.name LIKE '%$filter%'";

        $qry = "FROM products p 
                WHERE NOT EXISTS (
                    SELECT 1 FROM movements m 
                    WHERE m.purchase_id = $purchaseId
                    AND m.product_id = p.id)
                $filtered";
        return $this->getCountQuery($qry);
    }

    public function getProduct($purchaseId, $start = '0', $limit = '10', $filter = '')
    {
        $filtered = $filter == '' ? '' : "AND p.name LIKE '%$filter%'";

        $qry = "SELECT p.*, m.quantity, m.id AS movId
                FROM products p 
                JOIN movements m ON m.product_id = p.id
                WHERE m.purchase_id = $purchaseId
                $filtered";
        return $this->getQuery($qry, $start, $limit);
    }

    public function getCountProduct($purchaseId, $start = '0', $limit = '10', $filter = '')
    {
        $filtered = $filter == '' ? '' : "AND p.name LIKE '%$filter%'";

        $qry = "FROM products p 
                JOIN movements m ON m.product_id = p.id
                WHERE m.purchase_id = $purchaseId
                $filtered";
        return $this->getCountQuery($qry);
    }

    public function finish($purchaseId)
    {
        $qry = "UPDATE purchases SET status_id = 2 where id = $purchaseId";
        $this->executeQry($qry);
        date_default_timezone_set('America/Guatemala');
        $date = date("Y-m-d H:i:s");
        $qry = "UPDATE movements SET apply = 1, date_apply = '$date' WHERE purchase_id = $purchaseId";
        $this->executeQry($qry);
    }

    public function edit($purchaseId)
    {
        $qry = "UPDATE purchases SET status_id = 1 where id = $purchaseId";
        $this->executeQry($qry);
        $qry = "UPDATE movements SET apply = 0, date_apply = null WHERE purchase_id = $purchaseId";
        $this->executeQry($qry);
    }
}
?>