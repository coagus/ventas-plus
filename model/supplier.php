<?php
require_once __DIR__ . '/../modules/database/table.php';

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
        return $this->saveData(get_object_vars($this));
    }

    public function getWhere()
    {
        return $this->getDataWhere(get_object_vars($this));
    }
}
?>