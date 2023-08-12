<?php
require_once __DIR__ . '/../modules/controller.php';
require_once __DIR__ . '/../model/purchase.php';

class PurchaseController extends Controller
{
    public function __CONSTRUCT($action = 'index')
    {
        parent::__construct('purchase', $action);
        $this->table = new Purchase();
    }

    public function getPurchases()
    {
        return $this->table->getAll();
    }

    public function purchase()
    {
        $this->view();
    }

    public function getProductsToAdd()
    {
        return $this->table->getProductsToAdd($_REQUEST['purchaseId']);
    }

    public function getProductsAdded()
    {
        return $this->table->getProductsAdded($_REQUEST['purchaseId']);
    }

    public function prodToAdd()
    {
        $this->view('prodToAdd');
    }
    public function prodAdded()
    {
        $this->view('prodAdded');
    }

    public function editProd()
    {
        $this->view();
    }
}
?>