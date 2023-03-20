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
    private $paramsPags = '';
    private $extraParams = '';

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
                if ($this->action != "filter") {
                    $error = "No existe mantenimiento de " . ucwords($this->controller) . " ($pathObject) ni vista $this->action a presentar";
                    $this->showError($error);
                    return;
                }
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
        $codigo = date("mdHGs");
        echo "
            <div class='alert alert-warning alert-dismissible fade show' role='alert'>
                <strong>Error!</strong> Código $codigo, comunícalo al Administrador.
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>
            ";

        $handle = fopen(__DIR__ . "/../../error.csv", "a");
        $reg = array(date("Y-m-d H:i:s"), $codigo, $e, '', $_SESSION["id"]);
        fputcsv($handle, $reg);
        fclose($handle);
    }

    // pagination
    public function pagination($paramsPags = '')
    {
        $this->isLocal = true;
        $this->paramsPags = $paramsPags;
        return $this->view('pagination');
    }

    public function getParamsPags()
    {
        return $this->paramsPags;
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

    public function getBackFiltered()
    {
        return isset($_SESSION['backfiltered']) ? $_SESSION['backfiltered'] : '';
    }

    public function setPagination($count)
    {
        $this->page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;
        $this->startPage = isset($_REQUEST['startPage']) ? $_REQUEST['startPage'] : 1;

        if ($this->isFiltered()) {
            $filter = $this->getFilter();
            $_SESSION['backfiltered'] = "page=$this->page&startPage=$this->startPage&datafilter=$filter";
        } else {
            $_SESSION['backfiltered'] = '';
        }

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

    public function filter($params = '')
    {
        if ($params == '') {
            $amp = '';
            foreach ($_REQUEST as $key => $value) {
                if ($value != '' && $key != 'controller' && $key != 'action' && $key != 'filter' && $key != 'datafilter') {
                    $params .= "$amp$key=$value";
                    $amp = $amp == '' ? '&' : $amp;
                }
            }
        }
        $this->extraParams = $params;
        $this->isLocal = true;
        $this->view('filter');
    }

    public function getPreAction()
    {
        return isset($_REQUEST['preAction']) ? $_REQUEST['preAction'] : 'index';
    }

    public function getExtraParams()
    {
        return $this->extraParams;
    }

    public function search($params = '')
    {
        $action = ($this->action == 'edit'
            || $this->action == 'delete'
            || $this->action == 'save') ? 'index' : $this->action;

        echo '<div class="container mt-2" id="filter">';

        if ($this->isFiltered())
            $this->filter($params);

        echo '
</div>

<script>
$(document).ready(function () {
    $.getScript("lib/vcl/nav.js", function () {        
        $("#search").click(function () {
            show("' . $this->controller . '", "filter", "#filter", "preAction=' . $action . "&" . $params . '");
        });
    });
});
</script>
        ';
    }
}