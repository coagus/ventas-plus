<?php
class Controller
{
    protected $table = null;
    private $controller;
    private $action;
    private $isLocal = false;
    private $key;
    private $path;
    private $tableList;

    // Pagination
    private $limit = 12;
    private $page = 1;
    private $pages = 1;
    private $startPage = 1;
    private $start = 0;
    private $pageList;

    public function __CONSTRUCT($controller, $action)
    {
        $this->controller = $controller;
        $this->action = $action;

        $_SESSION["controller"] = $controller;
        $_SESSION["action"] = $action;

        if (
            !file_exists(__DIR__ . "/../../vie/$this->controller/$this->action.phtml")
            && (file_exists(__DIR__ . "/$this->action.phtml") || $this->action == "delete")
        ) {
            $pathObject = __DIR__ . "/../../mdl/$this->controller.php";

            if (file_exists($pathObject)) {
                require_once $pathObject;
                $table = ucwords($this->controller);
                $this->table = new $table();
                $this->isLocal = true;
            } else {
                $error = "No existe mantenimiento de " . ucwords($this->controller) . " ($pathObject) ni vista $this->action a presentar";
                $this->showError($error);
                return;
            }
        }
    }

    protected function view($view = '')
    {
        $view = $view == '' ? $this->action : $view;
        $pathview = __DIR__ . "/../../vie/$this->controller/$view.phtml";

        if (file_exists($pathview)) {
            require_once $pathview;
        } else {
            if ($this->isLocal) {
                require_once __DIR__ . "/$view.phtml";
            } else {
                echo "No se encuentra la vista $view";
            }
        }
    }

    public function index()
    {
        $this->view();
    }

    public function viewReg()
    {
        $this->view();
    }

    public function edit()
    {
        $view = '';

        if (isset($_REQUEST['Id'])) {
            foreach ($this->table as $key => $value) {
                if ($key == 'Id') {
                    $this->table->Id = $_REQUEST['Id'] == "0" ? '' : $_REQUEST['Id'];
                } else {
                    $this->table->$key = $_REQUEST[$key];
                }
            }

            if ($this->table->save()) {
                $this->tableList = $this->table->getList();
                $view = 'index';
            }
        }

        $this->view($view);
    }

    public function delete()
    {
        if (isset($_REQUEST['Id']))
            $this->table->delete($_REQUEST['Id']);

        $this->view('index');
    }

    public function is_Date($str)
    {
        $str = str_replace("/", "-", $str);
        $stamp = strtotime($str);

        if (strpos($str, "-") !== false && is_numeric($stamp)) {
            $month = date('m', $stamp);
            $day = date('d', $stamp);
            $year = date('Y', $stamp);

            return checkdate($month, $day, $year);
        }

        return false;
    }

    public function formUpLoad()
    {
        $this->key = $_REQUEST['key'];
        $this->view();
    }

    public function upLoadImage()
    {
        $this->key = $_REQUEST['key'];
        $errors = array();
        $file_name = $_FILES['file_img']['name'];
        $file_size = $_FILES['file_img']['size'];
        $file_tmp = $_FILES['file_img']['tmp_name'];
        $file_type = $_FILES['file_img']['type'];
        $tmp = explode('.', $_FILES['file_img']['name']);
        $file_ext = strtolower(end($tmp));

        $extensions = array("jpeg", "jpg", "png");

        if (in_array($file_ext, $extensions) === false) {
            $errors[] = "extension not allowed, please choose a JPEG or PNG file.";
        }

        if ($file_size > 2097152) {
            $errors[] = 'File size must be excately 2 MB';
        }

        if (empty($errors) == true) {
            $this->path = "img/upload/" . $file_name;
            move_uploaded_file($file_tmp, __DIR__ . "/../../" . $this->path);
        } else {
            $this->showError($errors);
        }
        $this->view();
    }

    public function getKey()
    {
        return $this->key;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function showError($e)
    {
        echo
            '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>
                    <i class="fas fa-bug" style="font-size:17px"></i> 
                    Error Controlado!
                </strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <hr>
                ' . $e . '
            </div>';
    }

    // pagination
    public function pagination()
    {
        $this->isLocal = true;
        return $this->view('pagination');
    }
    public function getPage()
    {
        return $this->page;
    }

    public function getPages()
    {
        return $this->pages;
    }

    public function getStartPage()
    {
        return $this->startPage;
    }

    public function getPageList()
    {
        return $this->pageList;
    }

    public function getStart()
    {
        return $this->start;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    public function isFiltered()
    {
        return isset($_REQUEST['datafilter']);
    }

    public function getFilter()
    {
        return isset($_REQUEST['datafilter']) ? $_REQUEST['datafilter'] : '';
    }

    public function setPagination($count)
    {
        $this->page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;
        $this->startPage = isset($_REQUEST['startPage']) ? $_REQUEST['startPage'] : 1;

        $this->pages = ceil($count / $this->limit);

        if ($this->page > ($this->startPage + 4))
            $this->startPage = ($this->startPage + 9) > $this->pages
                ? $this->pages - 4 : $this->startPage + 5;

        if ($this->page < $this->startPage)
            $this->startPage = ($this->startPage - 5) < 1
                ? 1 : $this->startPage - 5;

        $maxPages = $this->pages < 5 ? $this->pages : $this->startPage + 4;
        $this->pageList = range($this->startPage, $maxPages);
        $this->start = $this->limit * ($this->page - 1);
    }

    public function filter()
    {
        $this->isLocal = true;
        $this->view('filter');
    }

    public function search()
    {
        echo '<div class="container mt-2" id="filter">';

        if ($this->isFiltered())
            $this->filter();

        echo '</div>';
    }
}