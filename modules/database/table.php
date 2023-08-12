<?php
require_once 'db.php';

class Table
{
    private $pdo;
    private $table;
    private $fields;
    private $values;
    private $where;
    private $update;
    private $lastInsertId;
    private $count;
    private $start;
    private $limit;
    private $pagination;

    public function __CONSTRUCT($table)
    {
        try {
            $strConnect = 'mysql:host=' . HOST . ';dbname=' . DBNAME . ';charset=' . CHARSET;
            $this->table = $table;
            $pdoconn = "\$this->pdo = new PDO($strConnect, " . USR . ", " . PWD . ");";
            $this->pdo = new PDO($strConnect, USR, PWD);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->start = 1;
            $this->limit = 10;
            $this->count = 0;
        } catch (Exception $e) {
            $this->setError($e, "Database Connection Error: $pdoconn");
        }
    }

    private function setError($e, $msg = '', $qry = '')
    {
        $codigo = date("mdHGs");
        echo "
        <div class='alert alert-danger alert-dismissible fade show' role='alert'>
            <strong>Error!</strong> Código $codigo, comunícalo al Administrador.
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
        </div>
        ";

        $filename = __DIR__ . "/../../log/errordb.csv";
        $dirname = dirname($filename);
        if (!is_dir($dirname)) {
            mkdir($dirname, 0755, true);
        }

        $handle = fopen($filename, "a");
        $reg = array(date("Y-m-d H:i:s"), $codigo, $msg, $e, $qry, $_SESSION["id"]);
        fputcsv($handle, $reg);
        fclose($handle);
    }

    // Mapper

    private function fillFields($row)
    {
        $this->fields = "";
        $this->values = "";
        foreach ($row as $key => $value) {
            if ($value != '' && $key != 'id') {
                $this->fields .= $key . ',';
                $this->values .= "'" . $value . "',";
            }
        }
        $this->fields = substr($this->fields, 0, -1);
        $this->values = substr($this->values, 0, -1);
    }

    private function fillWhere($row)
    {
        $this->where = '';
        foreach ($row as $key => $value) {
            if ($value != '' && $value != null) {
                $this->where .= $key . "='" . $value . "' and ";
            }
        }
        $this->where = $this->where == '' ? '' : ' where ' . substr($this->where, 0, -4);
    }

    private function fillUpdate($row)
    {
        $this->update = '';

        foreach ($row as $key => $value) {
            if ($value != '' && $value != null && $key != 'id') {
                $this->update .= $key . "='" . $value . "', ";
            }
        }
        $this->update = substr($this->update, 0, -2);
    }

    // Mutation

    public function mutation($qry)
    {
        try {
            $stm = $this->pdo->prepare($qry);
            $stm->execute();
            return true;
        } catch (Exception $e) {
            $this->setError($e, 'Mutation error', $qry);
        }
        return false;
    }

    private function insert($row)
    {
        $this->fillFields($row);
        $qry = "INSERT INTO $this->table ($this->fields) VALUES ($this->values)";
        $result = $this->mutation($qry);
        $this->lastInsertId = $result ? $this->pdo->lastInsertId() : '';
        return $result;
    }

    public function lastInsertId()
    {
        return $this->lastInsertId;
    }

    private function update($row)
    {
        $this->fillUpdate($row);
        $qry = "UPDATE $this->table SET $this->update where id = " . $row['id'];
        return $this->mutation($qry);
    }

    protected function saveData($data)
    {
        return $data['id'] == '' ? $this->insert($data) : $this->update($data);
    }

    public function delete($Id)
    {
        $qry = "DELETE FROM $this->table WHERE id = $Id";
        return $this->mutation($qry);
    }

    // Query

    private function setPagination()
    {
        $startPage = isset($_REQUEST['startPage']) ? $_REQUEST['startPage'] : 1;
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;
        $pages = ceil($this->count / $this->limit);

        if ($page > ($startPage + 4))
            $startPage = ($startPage + 9) > $pages
                ? $pages - 4 : $startPage + 5;

        if ($page < $startPage)
            $startPage = ($startPage - 5) < 1
                ? 1 : $startPage - 5;

        $maxPages = $pages < 5 ? $pages : $startPage + 4;
        $pageList = range($startPage, $maxPages);
        $this->start = $this->limit * ($page - 1);
        $this->pagination = [$startPage, $page, $pages, $pageList];
    }

    public function getPagination()
    {
        return $this->pagination;
    }

    public function query($qry, $single = true, $order = '')
    {
        try {
            if ($single) {
                $stm = $this->pdo->prepare($qry);
                $stm->execute();
                return $stm->fetch(PDO::FETCH_OBJ);
            }

            $qryCount = "SELECT COUNT(1) as quantity FROM ($qry) query";
            $stm = $this->pdo->prepare($qryCount);
            $stm->execute();
            $this->count = intval($stm->fetch(PDO::FETCH_OBJ)->quantity);

            $this->setPagination();

            $qry = "SELECT * FROM ($qry) query";
            $qry .= $order == '' ? '' : ' ORDER BY ' . $order;
            $qry .= " LIMIT $this->start, $this->limit";

            $stm = $this->pdo->prepare($qry);
            $stm->execute();
            return $stm->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            $this->setError($e, 'Error in query', $qry);
        }

        return [];
    }

    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    public function getById($Id)
    {
        $qry = "SELECT * FROM $this->table where id = $Id";
        return $this->query($qry);
    }

    protected function getDataWhere($row)
    {
        $this->fillWhere($row);
        $qry = "SELECT * FROM $this->table $this->where";
        return $this->query($qry, false);
    }

    public function getAll()
    {
        return $this->query("SELECT * FROM $this->table", false, 'id DESC');
    }
}
?>