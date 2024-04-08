<?php

namespace App\Controllers;

use DateInterval;
use Datetime;

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

    public function scrapeYT()
    {



        $request = \Config\Services::request();
        $artist_id = $request->getGet('artist_id');
        if ($artist_id == "") exit('NO ARTIST ID');

        $db = \Config\Database::connect();
        $builder = $db->table('artist');
        $builder->select('*');
        $builder->where(['id' => $artist_id]);
        $results = $builder->get()->getFirstRow();
        $channelID = $results->channel_id;

        echo 'Artist: ' .  $results->name . '<br>';

        $builder = $db->table('youtube');
        $builder->selectCount('id');
        $builder->where(['artist_id' => $artist_id]);
        $result = $builder->get()->getResult();
        echo 'BEFORE Count Video: ' . $result[0]->id . '<br>';

        // Get videos from channel by YouTube Data API
        $API_key    = 'AIzaSyBkbEE5GvMxemj3RGVdROPbPwY8170ob9A'; //my API key for hermantaniwan@gmail.com
        //$API_key    = 'AIzaSyB6NTNVxhKN2Qjho8sCiqw8XNNIN8gYBTo'; //herman.create@gmail.com
        $maxResults = 100;
        $totalPage = 1;

        $builder = $db->table('channel');
        $builder->select('*');
        $builder->where(['artist_id' => $artist_id]);
        $results = $builder->get()->getLastRow();

        $pageToken = $results != null ? $results->next_page_token : '';


        for ($i = 0; $i < $totalPage; $i++) {
            $content = [];
            $channelstr = file_get_contents("https://www.googleapis.com/youtube/v3/search?part=snippet&channelId=$channelID&maxResults=$maxResults&order=date&key=$API_key&fields=nextPageToken,pageInfo,items(id(videoId))&pageToken=$pageToken");


            // $channelstr = file_get_contents("http://localhost/food-recom-cms/asset/json/sample-channel-result.json");
            $channeljson = json_decode($channelstr, true);
            // var_dump($channeljson);
            // die();
            // echo "<pre>";
            echo 'Total Video: ' . $channeljson['pageInfo']['totalResults'] . "<br>" . "<br>";
            // echo "</pre>";

            echo 'Videos Inserted: <br>';
            $videos = [];
            foreach ($channeljson['items'] as $video) {
                if (isset($video['id']['videoId']) == null) {
                    continue;
                }
                $videostr = file_get_contents("https://www.googleapis.com/youtube/v3/videos?part=snippet,contentDetails&id=" . $video['id']['videoId'] . "&key=$API_key");
                // $videostr = file_get_contents("http://localhost/food-recom-cms/asset/json/sample-video-result.json");
                $videojson = json_decode($videostr, true);
                array_push($videos, $videojson);
                // echo $video['id']['videoId'];
                $builder = $db->table('youtube');
                $duration = new DateInterval($videojson['items'][0]['contentDetails']['duration']);
                $dt = new DateTime($videojson['items'][0]['snippet']['publishedAt']); //first argument "must" be a string
                $data = [
                    'video_id' => $videojson['items'][0]['id'],
                    // 'video_id' => $this->generateRandomString(10),
                    'title' => $videojson['items'][0]['snippet']['title'],
                    'description' => $videojson['items'][0]['snippet']['description'],
                    'published_time' => $dt->format('Y-m-d H:i:s'),
                    'content_duration' => $duration->format("%H:%I:%s"),
                    'artist_id' => $artist_id,
                    'raw' => json_encode($videojson),
                ];

                $builder->ignore(true)->insert($data);
                echo "Inserted Videos ID " . $db->insertID() . "<br>";
            }


            // echo $dt->format('Y-m-d, H:i:s');
            // die();
            if (isset($channeljson['nextPageToken'])) {
                $pageToken = $channeljson['nextPageToken'];
                $data = [
                    'artist_id' => $artist_id,
                    'next_page_token' => $pageToken
                ];
                $builder = $db->table('channel');
                $builder->insert($data);
                echo "<br>" . "Inserted Page:  " . $db->insertID() . "<br>";
            } else {
                echo "<br>" . "Inserted Page:  THE END <br>";
            }
            $builder = $db->table('youtube');
            $builder->selectCount('id');
            $builder->where(['artist_id' => $artist_id]);
            $result = $builder->get()->getResult();
            echo 'AFTER Count Video: ' . $result[0]->id . '<br>';

            // echo $db->table('youtube')->where(['id' => $id])->delete();
        }

        // echo $content['channel']['nextPageToken'];
        // $content['channel']['videos'];
        // $content['channel']['videos'][0]['items'][0]['id'];
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
