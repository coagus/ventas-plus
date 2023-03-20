<?php
require_once __DIR__ . '/../lib/mvc/table.php';

class Movement extends Table
{
    public $id = '';
    public $type_id = '';
    public $product_id = '';
    public $apply = '';
    public $date_apply = '';
    public $quantity = '';
    public $purchase_id = '';
    public $sale_id = '';
    public $return_id = '';

    public function __CONSTRUCT()
    {
        parent::__construct('movements');
    }

    public function save()
    {
        return $this->saveData($this->id, get_object_vars($this));
    }

    public function getWhere()
    {
        return $this->getRowsWhere(get_object_vars($this));
    }
}
?>