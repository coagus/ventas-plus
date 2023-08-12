<?php
require_once __DIR__ . '/../modules/controller.php';

class Purchase_statusController extends Controller
{
    public function __CONSTRUCT($action = 'index')
    {
        parent::__construct('purchase_status', $action);
        $this->setCtlTitle('Estatus de Compra');
        $this->useCRUD();
    }
}
?>