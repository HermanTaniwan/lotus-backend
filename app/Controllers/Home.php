<?php

namespace App\Controllers;

use DateInterval;
use Datetime;

class Home extends BaseController
{
    public function index(): string
    {
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
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
        $region = $request->getGet('region');
        $duration = $request->getGet('duration');


        $page = $request->getGet('page');
        $limit = $request->getGet('limit');
        $offset = intval($page) * intval($limit);


        $db = \Config\Database::connect();
        $builder = $db->table('recipe');
        $builder->select('recipe.*, youtube.title as name, youtube.description as instructions, artist.image as "artist_image", artist.name as "artist_name"');
        $builder->join('artist', 'artist.id = recipe.artist_id', 'left');
        $builder->join('youtube', 'youtube.video_id = recipe.video_id', 'left');
        $matchQuery = "";
        if ($ingredients != "") {
            $matchQuery = 'MATCH (instructions) AGAINST ("' . $ingredients . '" IN BOOLEAN MODE)';
        } else if ($keywords != "") {

            $matchQuery = 'MATCH (recipe.name) AGAINST ("' . $keywords . '" IN BOOLEAN MODE)';
        }

        if ($region != '') {
            $builder->like('region', $region);
        }
        switch ($duration) {
            case 'cepat':
                $builder->where('youtube.content_duration <', '00:05:00');
                break;
            case 'sedang':
                $builder->where('youtube.content_duration >', '00:05:00');
                $builder->where('youtube.content_duration <', '00:08:00');
                break;
            case 'detail':
                $builder->where('youtube.content_duration >', '00:08:00');
                break;
        }

        if ($matchQuery != "") {
            $builder->where($matchQuery);
            $builder->orderBy($matchQuery, 'DESC');
        } else {
            $builder->orderBy('recipe.id', 'DESC');
        }

        // var_dump($builder->getCompiledSelect());
        // die();

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

    function searchRecipeNLP()
    {
        $request = \Config\Services::request();
        $keywords = $request->getGet('keywords');
        if ($keywords == "") exit('No keywords found');
        $db = \Config\Database::connect();
        $builder = $db->table('recipe');
        $builder->select('recipe.name, recipe.instructions');
        $builder->select('MATCH(recipe.name) AGAINST ("' . $keywords . '" IN NATURAL LANGUAGE MODE) as score_name');
        $builder->select('MATCH(recipe.instructions) AGAINST ("' . $keywords . '" IN NATURAL LANGUAGE MODE)  as score_instructions');
        $builder->select('(MATCH(recipe.name) AGAINST ("' . $keywords . '" IN NATURAL LANGUAGE MODE) * MATCH(recipe.instructions) AGAINST ("' . $keywords . '" IN NATURAL LANGUAGE MODE)) as full_score');
        $builder->join('artist', 'artist.id = recipe.artist_id', 'left');
        // $builder->where($matchQuery);
        $builder->orderBy('full_score', 'DESC');
        $results = $builder->get(10, 0)->getResult();
        echo "<pre>";
        var_dump($results);
        echo json_encode($results);
        echo "</pre>";
        exit();
    }


    function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
