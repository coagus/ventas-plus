<?php
require_once __DIR__ . '/../modules/database/table.php';

class Movement_type extends Table
{
    public $id = '';
    public $type = '';

    public function __CONSTRUCT()
    {
        parent::__construct('movements_type');
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