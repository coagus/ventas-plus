<?php
require_once __DIR__ . '/../modules/controller.php';

class RoleController extends Controller
{
    public function __CONSTRUCT($action = 'index')
    {
        parent::__construct('role', $action);
        $this->setCtlTitle('Roles');
        $this->useCRUD();
    }
}
?>