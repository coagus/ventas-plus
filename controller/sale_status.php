<?php
require_once __DIR__ . '/../modules/controller.php';

class Sale_statusController extends Controller
{
    public function __CONSTRUCT($action = 'index')
    {
        parent::__construct('sale_status', $action);
        $this->setCtlTitle('Estatus de Venta');
        $this->useCRUD();
    }
}
?>