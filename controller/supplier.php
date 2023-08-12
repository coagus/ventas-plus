<?php
require_once __DIR__ . '/../modules/controller.php';

class SupplierController extends Controller
{
    public function __CONSTRUCT($action = 'index')
    {
        parent::__construct('supplier', $action);
        $this->setCtlTitle('Proveedores');
        $this->useCRUD();
    }
}
?>