<?php
require_once __DIR__ . '/../lib/mvc/controller.php';
require_once __DIR__ . '/../mdl/supplier.php';
require_once __DIR__ . '/../mdl/purchase.php';
require_once __DIR__ . '/../mdl/movement.php';

class PurchaseController extends Controller
{
    private $current = '';
    public function __CONSTRUCT($action = 'index')
    {
        parent::__construct('purchase', $action);
    }

    public function getAllSuppliers()
    {
        $suppliers = null;
        $supplier = new Supplier();

        if ($this->isFiltered()) {
            $filter = $this->getFilter();
            $this->setPagination($supplier->getCountFilter($filter));
            $suppliers = $supplier->getFilter($filter, $this->getStart(), $this->getLimit());
        } else {
            $this->setPagination($supplier->getCount());
            $suppliers = $supplier->getAll($this->getStart(), $this->getLimit());
        }

        return $suppliers;
    }

    public function selectSupplier()
    {
        $this->view();
    }

    public function getAllPurchases()
    {
        $purchases = null;
        $purchase = new Purchase();

        if ($this->isFiltered()) {
            $filter = $this->getFilter();
            $purchases = $purchase->getFilter($filter, $this->getStart(), $this->getLimit());
            $this->setPagination($purchase->getCountFilter($filter));
        } else {
            $this->setPagination($purchase->getCount());
            $purchases = $purchase->getAll($this->getStart(), $this->getLimit());
        }

        return $purchases;
    }

    public function addProduct()
    {
        $movement = new Movement();
        $movement->type_id = '1';
        $movement->product_id = $_REQUEST['productId'];
        $movement->quantity = $_REQUEST['quantity'];
        $movement->purchase_id = $_REQUEST['purchaseId'];
        $movement->save();

        $this->editPurchase();
    }

    public function updateQuantity()
    {
        $movement = new Movement();
        $movement->id = $_REQUEST['movId'];
        $movement->quantity = $_REQUEST['quantity'];
        $movement->save();

        $this->editPurchase();
    }

    public function deleteMovement()
    {
        $movement = new Movement();
        $movement->delete($_REQUEST['movId']);
        $this->editPurchase();
    }

    public function editPurchase()
    {
        $purchaseId = isset($_REQUEST['purchaseId']) ? $_REQUEST['purchaseId'] : '0';
        $this->current = new Purchase();

        if ($purchaseId === '0') {
            $this->current->supplier_id = $_REQUEST['supplierId'];
            $this->current->user_id = $_SESSION['id'];
            date_default_timezone_set('America/Guatemala');
            $this->current->date = date("Y-m-d H:i:s");
            $this->current->save();
            $purchaseId = $this->current->getLastInsertId();
        }
        $this->current = $this->current->getPurchaseById($purchaseId);
        $this->view('editPurchase');
    }

    public function finish()
    {
        $purchase = new Purchase();
        $purchase->finish($_REQUEST['purchaseId']);
        $this->editPurchase();
    }

    public function backToEdit()
    {
        $purchase = new Purchase();
        $purchase->edit($_REQUEST['purchaseId']);
        $this->editPurchase();
    }

    public function getProductsToAdd($purchaseId)
    {
        $this->setLimit(10);
        $purchase = new Purchase();
        $filter = $this->isFiltered() ? $this->getFilter() : '';
        $this->setPagination($purchase->getCountProductToAdd($purchaseId, $this->getStart(), $this->getLimit(), $filter));
        $products = $purchase->getProductToAdd($purchaseId, $this->getStart(), $this->getLimit(), $filter);
        return $products;
    }

    public function getProducts($purchaseId)
    {
        $this->setLimit(9);
        $purchase = new Purchase();
        $filter = $this->isFiltered() ? $this->getFilter() : '';
        $this->setPagination($purchase->getCountProduct($purchaseId, $this->getStart(), $this->getLimit(), $filter));
        $products = $purchase->getProduct($purchaseId, $this->getStart(), $this->getLimit(), $filter);
        return $products;
    }

    public function getAllProducts($purchaseId)
    {
        $purchase = new Purchase();
        return $purchase->getProduct($purchaseId, '0', '1000');
    }

    public function getCurrent()
    {
        return $this->current;
    }

    public function getTab()
    {
        return isset($_REQUEST['tab']) ? $_REQUEST['tab'] : '1';
    }
}
?>