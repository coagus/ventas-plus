<?php
require_once __DIR__ . '/../modules/controller.php';

class HomeController extends Controller
{
    public function __CONSTRUCT($action = 'index')
    {
        parent::__construct('home', $action);
    }

    public function login()
    {
        $view = '';
        if (isset($_REQUEST['username'])) {
            $_SESSION["id"] = '1';
            $view = $_REQUEST['username'] == 'coagus' ? 'index' : '';
        }

        $this->view($view);
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
}
?>