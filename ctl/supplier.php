<?php
require_once __DIR__ . '/../lib/mvc/controller.php';
require_once __DIR__ . '/../mdl/supplier.php';

class SupplierController extends Controller
{
    public function __CONSTRUCT($action = 'index')
    {
        parent::__construct('supplier', $action);
        $this->setLimit(10);
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

    public function getSupplier()
    {
        $supplier = new Supplier();
        return isset($_REQUEST['id']) ? $supplier->getById($_REQUEST['id']) : $supplier;
    }

    public function edit()
    {
        $this->view();
    }

    public function save()
    {
        $supplier = new Supplier();
        $supplier->id = $_REQUEST['id'];
        $supplier->name = $_REQUEST['name'];
        $supplier->description = $_REQUEST['description'];
        $supplier->phone = $_REQUEST['phone'];
        $supplier->save();
        $this->view('index');
    }

    public function delete()
    {
        $supplier = new Supplier();
        $supplier->delete($_REQUEST['id']);
        $this->view('index');
    }
}
?>