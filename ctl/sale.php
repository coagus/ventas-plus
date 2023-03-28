<?php
require_once __DIR__ . '/../lib/mvc/controller.php';
require_once __DIR__ . '/../mdl/client.php';
require_once __DIR__ . '/../mdl/sale.php';
require_once __DIR__ . '/../mdl/movement.php';
require_once __DIR__ . '/../lib/utl/badgeResult.php';

class SaleController extends Controller
{
    private $current = '';
    public function __CONSTRUCT($action = 'index')
    {
        parent::__construct('sale', $action);
    }

    public function getAllClients()
    {
        $this->setLimit(9);
        $client = new Client();
        $filter = $this->isFiltered() ? $this->getFilter() : '';
        $this->setPagination($client->getCountFilter($filter));
        return $client->getFilter($filter, $this->getStart(), $this->getLimit());
    }

    public function selectClient()
    {
        $this->view();
    }

    public function editOrder()
    {
        $saleId = isset($_REQUEST['saleId']) ? $_REQUEST['saleId'] : '0';
        $this->current = new Sale();
        if ($saleId === '0') {
            $this->current->client_id = $_REQUEST['clientId'];
            $this->current->user_id = $_SESSION['id'];
            $this->current->status_id = '1';
            date_default_timezone_set('America/Guatemala');
            $this->current->date = date("Y-m-d H:i:s");
            $this->current->save();
            $saleId = $this->current->getLastInsertId();
        }

        $this->current = $this->current->getSaleById($saleId);
        $this->view('editOrder');
    }

    public function getCurrent()
    {
        return $this->current;
    }

    public function getTab()
    {
        return isset($_REQUEST['tab']) ? $_REQUEST['tab'] : '1';
    }

    public function getProductsToAdd($saleId)
    {
        $this->setLimit(10);
        $sale = new Sale();
        $filter = $this->isFiltered() ? $this->getFilter() : '';
        $this->setPagination($sale->getCountProductToAdd($saleId, $this->getStart(), $this->getLimit(), $filter));
        $products = $sale->getProductToAdd($saleId, $this->getStart(), $this->getLimit(), $filter);
        return $products;
    }
    public function getProducts($saleId)
    {
        $this->setLimit(9);
        $sale = new Sale();
        $filter = $this->isFiltered() ? $this->getFilter() : '';
        $this->setPagination($sale->getCountProduct($saleId, $this->getStart(), $this->getLimit(), $filter));
        $products = $sale->getProduct($saleId, $this->getStart(), $this->getLimit(), $filter);
        return $products;
    }

    public function getAllProducts($saleId)
    {
        $sale = new Sale();
        return $sale->getProduct($saleId, '0', '1000');
    }

    public function addProduct()
    {
        $movement = new Movement();
        $movement->type_id = '2';
        $movement->product_id = $_REQUEST['productId'];
        $movement->quantity = $_REQUEST['quantity'];
        $movement->product_price = $_REQUEST['price'];
        $movement->sale_id = $_REQUEST['saleId'];
        $movement->save();
        $this->editOrder();
    }

    public function updateQuantity()
    {
        $movement = new Movement();
        $movement->id = $_REQUEST['movId'];
        $movement->quantity = $_REQUEST['quantity'];
        $movement->save();
        $this->editOrder();
    }

    public function finish()
    {
        $sale = new Sale();
        $sale->finish($_REQUEST['saleId']);
        $this->editOrder();
    }

    public function backToEdit()
    {
        $sale = new Sale();
        $sale->edit($_REQUEST['saleId']);
        $this->editOrder();
    }

    public function deleteMovement()
    {
        $movement = new Movement();
        $movement->delete($_REQUEST['movId']);
        $this->editOrder();
    }

    public function getBadge($count)
    {
        $badge = new BadgeResult();
        return $badge->getColorCount($count, '0');
    }
}
?>