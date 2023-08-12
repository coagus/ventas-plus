<?php
require_once __DIR__ . '/../modules/database/table.php';

class Product extends Table
{
    public $id = '';
    public $name = '';
    public $description = '';
    public $cost = '';
    public $price = '';
    public $stock = '';
    public $date_upd_stock = '';

    public function __CONSTRUCT()
    {
        parent::__construct('products');
    }

    public function save()
    {
        return $this->saveData(get_object_vars($this));
    }

    public function getWhere()
    {
        return $this->getDataWhere(get_object_vars($this));
    }
}
?>