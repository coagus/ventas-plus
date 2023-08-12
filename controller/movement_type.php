<?php
require_once __DIR__ . '/../modules/controller.php';

class Movement_typeController extends Controller
{
    public function __CONSTRUCT($action = 'index')
    {
        parent::__construct('movement_type', $action);
        $this->setCtlTitle('Tipo de movimientos');
        $this->useCRUD();
    }
}
?>