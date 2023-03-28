<?php
require_once __DIR__ . '/../lib/mvc/controller.php';
require_once __DIR__ . '/../mdl/user.php';
require_once __DIR__ . '/../mdl/product.php';
require_once __DIR__ . '/../mdl/sale.php';
require_once __DIR__ . '/../lib/utl/badgeResult.php';

class HomeController extends Controller
{
    private $error = '';

    public function __CONSTRUCT($action = 'index')
    {
        parent::__construct('home', $action);
    }

    public function login()
    {
        $this->view();
    }

    public function setLogin()
    {
        $view = 'login';
        $usr = isset($_REQUEST['username']) ? $_REQUEST['username'] : '';
        $pwd = isset($_REQUEST['password']) ? $_REQUEST['password'] : '';

        if ($this->validaUsuario($usr, $pwd))
            $view = 'index';
        else
            $this->error = 'Usuario incorrecto.';

        $this->view($view);
    }

    private function validaUsuario($usr, $pwd)
    {
        if (trim($usr) == '' || trim($pwd) == '')
            return false;

        $usuario = new User();
        $usuario->username = $usr;
        $usuario->password = $pwd;
        $usr = $usuario->getWhere();
        $valido = count($usr) == 1;

        if ($valido) {
            $_SESSION["id"] = $usr[0]->id;
            $_SESSION["username"] = $usr[0]->username;
        }

        return $valido;
    }

    public function getCurrentUser()
    {
        $usr = new User();
        return $usr->getById($_SESSION["id"]);
    }

    public function logout()
    {
        session_destroy();
        $this->view('login');
    }

    public function admin()
    {
        $this->view();
    }

    public function getError()
    {
        return $this->error;
    }

    public function stock()
    {
        $this->view();
    }

    public function getStock()
    {
        $this->setLimit(9);
        $product = new Product();
        $filter = $this->isFiltered() ? $this->getFilter() : '';
        $this->setPagination($product->getCountFilter($filter));
        $products = $product->getProductsStock($filter, $this->getStart(), $this->getLimit());
        return $products;
    }

    public function getAllOrders()
    {
        $sales = new Sale();
        return $sales->getSales('0', '1000');
    }

    public function getBadge($count)
    {
        $badge = new BadgeResult();
        return $badge->getColorCount($count, '0');
    }
}
?>