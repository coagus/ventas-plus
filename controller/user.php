<?php
require_once __DIR__ . '/../modules/controller.php';

class UserController extends Controller
{
    public function __CONSTRUCT($action = 'index')
    {
        parent::__construct('user', $action);
        $this->setCtlTitle('Usuarios');
        $this->useCRUD();
    }
}
?>