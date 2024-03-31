<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        return view('welcome_message');
    }

    public function loadIngredients()
    {
        $db = \Config\Database::connect();
        $query   = $db->query('SELECT name, id FROM ingredients_category');
        $results = $query->getResult();
        $final = array();
        foreach ($results as $row) {
            $q   = $db->query('SELECT * FROM ingredients where category_id =' . $row->id);
            $listIngredient = $q->getResult();
            $ingredients = array();
            $ingredients[$row->name] = $listIngredient;
            array_push($final, $ingredients);
        }

        echo json_encode($final);
        exit();
        // echo "<pre>";
        // var_dump($final);
        // echo "</pre>";
    }
}
