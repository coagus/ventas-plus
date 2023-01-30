<?php
require_once __DIR__ . '/../lib/mvc/controller.php';
require_once __DIR__ . '/../mdl/user.php';

class UserController extends Controller
{
    public function __CONSTRUCT($action = 'index')
    {
        parent::__construct('user', $action);
    }

    public function getAllUsers()
    {
        $users = null;
        $user = new User();

        if ($this->isFiltered()) {
            $filter = $this->getFilter();
            $this->setPagination($user->getCountFilter($filter));
            $users = $user->getFilter($filter, $this->getStart(), $this->getLimit());
        } else {
            $this->setPagination($user->getCount());
            $users = $user->getAll($this->getStart(), $this->getLimit());
        }

        return $users;
    }

    public function getUser()
    {
        $user = new User();
        return isset($_REQUEST['id']) ? $user->getById($_REQUEST['id']) : $user;
    }

    public function edit()
    {
        $this->view();
    }

    public function save()
    {
        $user = new User();
        $user->id = $_REQUEST['id'];
        $user->username = $_REQUEST['username'];
        $user->password = $_REQUEST['password'];
        $user->name = $_REQUEST['name'];
        $user->save();
        $this->view('index');
    }

    public function delete()
    {
        $user = new User();
        $user->delete($_REQUEST['id']);
        $this->view('index');
    }
}
?>