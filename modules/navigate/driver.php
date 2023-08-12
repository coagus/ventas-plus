<?php
$controllerCall = 'home';
$action = 'login';

session_start();

if (isset($_SESSION['id']) || $_REQUEST['action'] == "login") {
    $controllerCall = isset($_REQUEST['controller']) ? strtolower($_REQUEST['controller']) : 'home';
    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'index';
}

$pathController = __DIR__ . "/../../controller/$controllerCall.php";

if (file_exists($pathController)) {
    require_once $pathController;

    $controllerName = ucwords($controllerCall) . 'Controller';
    $controller = new $controllerName($action);
    call_user_func(array($controller, $action));
} else {
    require_once __DIR__ . "/../../components/base/404controller.phtml";
}
?>