<?php
require_once __DIR__ . '/../modules/database/table.php';

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
        return $this->saveData(get_object_vars($this));
    }

    public function getWhere()
    {
        return $this->getDataWhere(get_object_vars($this));
    }

    public function getAll()
    {
        $qry = "SELECT p.id, ps.status, p.date, s.name AS supplier, u.username
            FROM purchases p 
            JOIN purchases_status ps ON ps.id = p.status_id
            JOIN suppliers s ON s.id = p.supplier_id
            JOIN users u ON u.id = p.user_id";

        return $this->query($qry, false, "status DESC, date DESC");
    }

    public function getProductsToAdd($purchaseId)
    {
        $qry = "SELECT *
            FROM products p 
            WHERE NOT EXISTS (
                SELECT 1 FROM movements m WHERE p.id =m.product_id AND m.purchase_id = $purchaseId
            ) ORDER BY p.id";

        return $this->query($qry, false);
    }

    public function getProductsAdded($purchaseId)
    {
        $qry = "SELECT p.name AS prod_name, p.description AS prod_desc, 
                m.id AS mov_id, m.quantity, m.quantity_pack, m.product_id,
                m.package_id , pa.name, pa.quantity AS pack_quantity
            FROM products p 
            JOIN movements m ON m.product_id = p.id 
            LEFT JOIN packages pa ON pa.id = m.package_id 
            WHERE m.purchase_id = $purchaseId";

        return $this->query($qry, false);
    }
}
?>