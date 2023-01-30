<?php
require_once __DIR__ . '/../lib/mvc/controller.php';
require_once __DIR__ . '/../mdl/product.php';

class ProductController extends Controller
{
    public function __CONSTRUCT($action = 'index')
    {
        parent::__construct('product', $action);
        $this->setLimit(10);
    }

    public function getAllProducts()
    {
        $products = null;
        $product = new Product();

        if ($this->isFiltered()) {
            $filter = $this->getFilter();
            $this->setPagination($product->getCountFilter($filter));
            $products = $product->getFilter($filter, $this->getStart(), $this->getLimit());
        } else {
            $this->setPagination($product->getCount());
            $products = $product->getAll($this->getStart(), $this->getLimit());
        }

        return $products;
    }

    public function getProduct()
    {
        $product = new Product();
        return isset($_REQUEST['id']) ? $product->getById($_REQUEST['id']) : $product;
    }

    public function edit()
    {
        $this->view();
    }

    public function save()
    {
        $product = new Product();
        $product->id = $_REQUEST['id'];
        $product->name = $_REQUEST['name'];
        $product->description = $_REQUEST['description'];
        $product->cost = $_REQUEST['cost'];
        $product->price = $_REQUEST['price'];
        $product->save();
        $this->view('index');
    }

    public function delete()
    {
        $product = new Product();
        $product->delete($_REQUEST['id']);
        $this->view('index');
    }
}
?>