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
        $keywords = $request->getGet('keywords');

        $page = $request->getGet('page');
        $limit = $request->getGet('limit');
        $offset = intval($page) * intval($limit);


        $db = \Config\Database::connect();
        $builder = $db->table('recipe');
        $builder->select('recipe.*, artist.image as "artist_image", artist.name as "artist_name"');
        $builder->join('artist', 'artist.id = recipe.artist_id', 'left');

        $matchQuery = "";
        if ($ingredients != "") {
            $matchQuery = 'MATCH (instructions) AGAINST ("' . $ingredients . '" IN BOOLEAN MODE)';
        } else if ($keywords != "") {

            $matchQuery = 'MATCH (recipe.name) AGAINST ("' . $keywords . '" IN BOOLEAN MODE)';
        }

        if ($matchQuery != "") {
            $builder->where($matchQuery);
            $builder->orderBy($matchQuery, 'DESC');
        } else {
            $builder->orderBy('recipe.id', 'DESC');
        }

        $results = $builder->get($limit, $offset)->getResult();

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

    public function searchRecipe()
    {
        $request = \Config\Services::request();
        $keywords = $request->getGet('keywords');
        $db = \Config\Database::connect();
        $builder = $db->table('recipe');
        $builder->select('recipe.*, artist.image as "artist_image", artist.name as "artist_name"');
        $builder->join('artist', 'artist.id = recipe.artist_id', 'left');
        $matchQuery = 'MATCH (recipe.name) AGAINST ("' . $keywords . '" IN BOOLEAN MODE)';
        $builder->where($matchQuery);
        $builder->orderBy($matchQuery, 'DESC');
        $results = $builder->get(10, 0)->getResult();
        echo json_encode($results);
        exit();
    }
}
