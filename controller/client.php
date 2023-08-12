<?php
require_once __DIR__ . '/../modules/controller.php';

class ClientController extends Controller
{
    public function __CONSTRUCT($action = 'index')
    {
        parent::__construct('client', $action);
        $this->setCtlTitle('Clientes');
        $this->useCRUD();
    }
}
?>