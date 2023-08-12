<?php
require_once __DIR__ . '/../modules/database/table.php';

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
        return $this->saveData(get_object_vars($this));
    }

    public function getWhere()
    {
        return $this->getDataWhere(get_object_vars($this));
    }
}
?>