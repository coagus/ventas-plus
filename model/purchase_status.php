<?php
require_once __DIR__ . '/../modules/database/table.php';

class Purchase_status extends Table
{
    public $id = '';
    public $status = '';

    public function __CONSTRUCT()
    {
        parent::__construct('purchases_status');
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