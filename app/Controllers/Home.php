<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        return view('welcome_message');
    }

    public function loadDB()
    {
        $db = \Config\Database::connect();
        $query   = $db->query('SELECT name, id FROM test');
        $results = $query->getResult();

        foreach ($results as $row) {
            echo $row->name;
            echo $row->id;
        }

        echo 'Total Results: ' . count($results);
    }
}
