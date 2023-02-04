<?php
require_once __DIR__ . '/../lib/mvc/controller.php';
require_once __DIR__ . '/../mdl/client.php';

class ClientController extends Controller
{
    public function __CONSTRUCT($action = 'index')
    {
        parent::__construct('client', $action);
        $this->setLimit(10);
    }

    public function getAllClients()
    {
        $clients = null;
        $client = new Client();

        if ($this->isFiltered()) {
            $filter = $this->getFilter();
            $this->setPagination($client->getCountFilter($filter));
            $clients = $client->getFilter($filter, $this->getStart(), $this->getLimit());
        } else {
            $this->setPagination($client->getCount());
            $clients = $client->getAll($this->getStart(), $this->getLimit());
        }

        return $clients;
    }

    public function getClient()
    {
        $client = new Client();
        return isset($_REQUEST['id']) ? $client->getById($_REQUEST['id']) : $client;
    }

    public function edit()
    {
        $this->view();
    }

    public function save()
    {
        $client = new Client();
        $client->id = $_REQUEST['id'];
        $client->name = $_REQUEST['name'];
        $client->phone = $_REQUEST['phone'];
        $client->address = $_REQUEST['address'];
        $client->reference = $_REQUEST['reference'];
        $client->save();
        $this->view('index');
    }

    public function delete()
    {
        $client = new Client();
        $client->delete($_REQUEST['id']);
        $this->view('index');
    }
}
?>