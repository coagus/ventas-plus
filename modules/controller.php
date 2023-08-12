<?php
class Controller
{
    protected $controller;
    protected $action;
    private $ctlTitle;
    private $actTitle;
    protected $table = null;
    protected $componentParams;

    public function __CONSTRUCT($controller, $action)
    {
        $this->controller = $controller;
        $this->action = $action;
        $this->ctlTitle = $controller;
        $this->actTitle = $action;
    }

    public function setCtlTitle($title)
    {
        $this->ctlTitle = $title;
    }

    public function setActTitle($title)
    {
        $this->actTitle = $title;
    }

    protected function view($view = '')
    {
        $view = $view == '' ? $this->action : $view;
        if ($view == '') {
            $view = $this->action;
        } else {
            $this->action = $view;
        }

        $viewPath = __DIR__ . "/../view/$this->controller/$view.phtml";
        if (file_exists($viewPath)) {
            require_once $viewPath;
            return;
        }

        if ($this->table == null) {
            echo "No se encuentra la vista $view";
        } else {
            require_once __DIR__ . "/../modules/database/$view-view.phtml";
        }
    }

    public function index()
    {
        $this->view();
    }

    // DB CRUD

    public function useCRUD()
    {
        $tablePath = __DIR__ . "/../model/$this->controller.php";
        if (file_exists($tablePath)) {
            require_once $tablePath;
            $table = ucwords($this->controller);
            $this->table = new $table();
        }
    }

    public function getRows()
    {
        return $this->table == null ? [] : $this->table->getAll();
    }

    public function getSingleRow()
    {
        $row = [];
        if ($this->table != null)
            $row = isset($_REQUEST['id']) ? $this->table->getById($_REQUEST['id']) : $this->table;

        return $row;
    }

    public function edit()
    {
        $this->view();
    }

    public function save()
    {
        if ($this->table != null) {
            foreach ($this->table as $key => $value) {
                $this->table->$key = $_REQUEST[$key];
            }
            $this->table->save();
        }

        $this->view('index');
    }

    public function delete()
    {
        if ($this->table != null && isset($_REQUEST['id']))
            $this->table->delete($_REQUEST['id']);

        $this->view('index');
    }

    public function getPagination()
    {
        return $this->table == null ? [1, 1, 1, []] : $this->table->getPagination();
    }

    public function getDataListDevice()
    {
        $principal = '';
        $secondary = '';
        $complementary = '';
        if ($this->table != null) {
            foreach ($this->table as $key => $value) {
                if ($key != 'id') {
                    if ($principal == '') {
                        $principal = $key;
                    } else if ($secondary == '') {
                        $secondary = $key;
                    } else if ($complementary == '') {
                        $complementary = $key;
                    } else {
                        break;
                    }
                }
            }
        }

        return [$principal, $secondary, $complementary];
    }

    // Components

    public function component($component, $params = [])
    {
        $this->componentParams = $params;
        $dir = __DIR__ . "/../components";
        $componentPath = "$dir/$this->controller/$component.phtml";
        if (!file_exists($componentPath)) {
            $componentPath = "$dir/common/$component.phtml";
            if (!file_exists($componentPath))
                $componentPath = "$dir/base/$component.phtml";
        }

        require file_exists($componentPath) ? $componentPath : "$dir/base/404component.phtml";
    }
}
?>