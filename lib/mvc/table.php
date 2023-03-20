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

    public function __CONSTRUCT($table)
    {
        try {
            $strConnect = 'mysql:host=' . HOST . ';dbname=' . DBNAME . ';charset=' . CHARSET;
            $this->table = $table;
            $pdoconn = "\$this->pdo = new PDO($strConnect, " . USR . ", " . PWD . ");";
            $this->pdo = new PDO($strConnect, USR, PWD);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (Exception $e) {
            $this->showError($e, "Data Connect: $pdoconn");
        }
    }

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

    protected function createRow($row)
    {
        $this->fillFields($row);
        $qry = "INSERT INTO $this->table ($this->fields) VALUES ($this->values)";

        try {
            $stm = $this->pdo->prepare($qry);
            $stm->execute();
            $this->lastInsertId = $this->pdo->lastInsertId();
        } catch (Exception $e) {
            $this->showError($e, $qry);
        }
    }

    public function getLastInsertId()
    {
        return $this->lastInsertId;
    }

    public function getAll($start = '0', $limit = '10')
    {
        try {
            $qry = "SELECT * FROM $this->table LIMIT $start, $limit";
            $stm = $this->pdo->prepare($qry);
            $stm->execute();
            return $stm->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            $this->showError($e, $qry);
        }
    }

    public function getCount()
    {
        try {
            $qry = "SELECT COUNT(1) AS count FROM $this->table";
            $stm = $this->pdo->prepare($qry);
            $stm->execute();
            return $stm->fetchAll(PDO::FETCH_OBJ)[0]->count;
        } catch (Exception $e) {
            $this->showError($e, $qry);
        }
    }

    public function getCountQuery($query)
    {
        try {
            $qry = "SELECT COUNT(1) AS count $query";
            $stm = $this->pdo->prepare($qry);
            $stm->execute();
            return $stm->fetchAll(PDO::FETCH_OBJ)[0]->count;
        } catch (Exception $e) {
            $this->showError($e, $qry);
        }
    }

    public function getById($Id)
    {
        try {
            $qry = "SELECT * FROM $this->table where id = $Id";
            $stm = $this->pdo->prepare($qry);
            $stm->execute();

            return $stm->fetch(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            $this->showError($e, $qry);
        }
    }

    protected function getRowsWhere($row)
    {
        $this->fillWhere($row);

        try {
            $a = microtime(true);

            $qry = "SELECT * FROM $this->table $this->where";

            $stm = $this->pdo->prepare($qry);
            $stm->execute();

            $b = microtime(true);
            $diff = $b - $a;

            if ($diff > 0.5) {
                $handle = fopen(__DIR__ . "/../../performace.csv", "a");
                $reg = array(date("Y-m-d H:i:s"), $qry, number_format($diff, 10), $_SESSION["username"]);
                fputcsv($handle, $reg);
                fclose($handle);
            }

            return $stm->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            $this->showError($e, $qry);
        }
    }

    protected function updateRow($row)
    {
        $this->fillUpdate($row);
        $Id = $row['id'];

        try {
            $qry = "UPDATE $this->table SET $this->update where id = $Id";
            $stm = $this->pdo->prepare($qry);
            $stm->execute();
        } catch (Exception $e) {
            $this->showError($e, $qry);
        }
    }

    public function delete($Id)
    {
        try {
            $qry = "DELETE FROM $this->table WHERE id = $Id";
            $stm = $this->pdo->prepare($qry);
            $stm->execute();
        } catch (Exception $e) {
            $this->showError($e, $qry);
        }
    }

    public function getQuery($qry, $start = 0, $limit = 10)
    {
        try {
            $qry = "$qry LIMIT $start, $limit";
            $stm = $this->pdo->prepare($qry);
            $stm->execute();
            return $stm->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            $this->showError($e, $qry);
        }
    }

    public function executeQry($qry)
    {
        try {
            $stm = $this->pdo->prepare($qry);
            $stm->execute();
        } catch (Exception $e) {
            $this->showError($e, $qry);
        }
    }

    public function saveData($id, $data)
    {
        if ($this->validUnique($data)) {
            if ($id == '') {
                $this->createRow($data);
            } else {
                $this->updateRow($data);
            }
        } else {
            return false;
        }
        return true;
    }

    private function validUnique($row)
    {
        $qry = "SELECT COLUMN_NAME
                FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_SCHEMA = '" . DBNAME . "'
                AND TABLE_NAME = '" . $this->table . "'
                AND COLUMN_KEY IN('PRI', 'UNI')";

        foreach ($row as $key => $value) {
            if ($key == 'id') {
                $id = $value;
                break;
            }
        }

        $uniqueList = $this->getQuery($qry, 10);

        if ($uniqueList != null) {
            foreach ($uniqueList as $u) {
                foreach ($row as $key => $value) {
                    if ($key == $u->COLUMN_NAME && $key != 'id') {
                        $qry = 'SELECT * FROM ' . $this->table . " WHERE $key = '$value' AND id <> '$id'";
                        if (count($this->getQuery($qry, 10)) > 0) {
                            $this->showWarning("Ingrese otro valor para el campo <strong>$key</strong> dado que ya existe un registro con el valor <strong>$value</strong>.");
                            return false;
                        }
                    }
                }
            }
        }

        return true;
    }

    public function showWarning($msg)
    {
        echo
            '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>
                    <i class="bi bi-exclamation-triangle" style="font-size:17px"></i> 
                    Advertencia!
                </strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button> 
                <hr>' . $msg . '
            </div>';
    }

    public function showError($e, $qry)
    {
        $codigo = date("mdHGs");
        echo "
        <div class='alert alert-danger alert-dismissible fade show' role='alert'>
            <strong>Error!</strong> Código $codigo, comunícalo al Administrador.
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
        </div>
        ";

        $handle = fopen(__DIR__ . "/../../error.csv", "a");
        $reg = array(date("Y-m-d H:i:s"), $codigo, $e->getMessage(), $qry, $_SESSION["id"]);
        fputcsv($handle, $reg);
        fclose($handle);
    }

    public function showRegNoSuper($table, $r)
    {
        $isSuper = false;
        foreach ($r as $key => $value) {
            if ($table == 'users' && $key == 'username' && $value == 'coagus') {
                $isSuper = true;
                break;
            }
        }
        return $_SESSION["username"] == 'coagus'
            || !$isSuper;
    }

    // For to edit
    public function isNotRequired($key)
    {
        return false;
    }

    public function hideFieldToEdit($key)
    {
        return false;
    }
    public function getCustomValue($key, $value)
    {
        return $value;
    }

    public function isTextArea($key)
    {
        return false;
    }

    // For to list 
    public function showField($key)
    {
        return true;
    }

    public function hideField($key)
    {
        $hide = array("id");
        return in_array($key, $hide);
    }

    public function hideFieldView($key)
    {
        $hide = array("id");
        return in_array($key, $hide);
    }

    public function isImage($key)
    {
        return false;
    }

    public function isMoney($key)
    {
        return false;
    }

    // Catálogos
    public function hasCatalogo($key)
    {
        return false;
    }

    public function getValCatalogo($key, $val)
    {
        return $val;
    }

    public function getCatalogo($key)
    {
        $catalogo = [];
        return $catalogo;
    }

    // Titulos
    public function getTitleList()
    {
        return $this->table . '';
    }

    public function getTitleSingle()
    {
        return $this->table;
    }

    public function min($key)
    {
        return '0';
    }
}
?>