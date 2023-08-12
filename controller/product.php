<?php
require_once __DIR__ . '/../modules/controller.php';

class ProductController extends Controller
{
    public function __CONSTRUCT($action = 'index')
    {
        parent::__construct('product', $action);
        $this->setCtlTitle('Productos');
        $this->useCRUD();
    }
}
?>