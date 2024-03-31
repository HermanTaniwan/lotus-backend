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
            foreach ($listIngredient as $l) {
                $l->{'category_name'} = $row->name;
            }
            $ingredients = array();
            $ingredients['id'] = $row->id;
            $ingredients['name'] = $row->name;
            $ingredients['ingredients'] = $listIngredient;
            array_push($final, $ingredients);
        }

        echo json_encode($final);
        exit();
    }

    public function loadRecipe()
    {

        $request = \Config\Services::request();
        $ingredients = $request->getGet('ingredients');
        $listIngredient = explode(' ', $ingredients);

        $page = $request->getGet('page');
        $limit = $request->getGet('limit');
        $offset = $page * $limit;


        $db = \Config\Database::connect();
        $strquery  = 'SELECT r.*,a.name as "artist_name", a.image as "artist_image" FROM recipe r LEFT JOIN artist a ON r.artist_id = a.id';
        if ($ingredients != "") {
            $strquery .= ' WHERE MATCH (instructions) AGAINST ("' . $ingredients . '" IN BOOLEAN MODE)';
            $strquery .= ' ORDER BY MATCH (instructions) AGAINST ("' . $ingredients . '" IN BOOLEAN MODE) DESC';
        } else {
            $strquery .= ' ORDER BY r.id DESC';
        }
        $strquery .= ' LIMIT ' . $limit . ' OFFSET ' . $offset;
        $query = $db->query($strquery);
        $results = $query->getResult();
        foreach ($results as $row) {
            $row->artist_image = 'https://quarks.id/food-recom-cms/asset/images/artist/' . $row->artist_image;
        }

        // echo "<pre>";
        // var_dump($results);
        // echo "</pre>";
        // exit();

        echo json_encode($results);
        exit();
    }
}
