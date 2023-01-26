<?php
require_once __DIR__ . '/../lib/mvc/table.php';

class Rol extends Table {
    public $Id = '';
    public $Rol = '';
    
    public function __CONSTRUCT() {
        parent::__construct('Rol');
    }
    
    public function save() {
        $this->saveData($this->Id, get_object_vars($this));
    }
    
    public function getWhere() {
        return $this->getRowsWhere(get_object_vars($this));
    }
}
?>
