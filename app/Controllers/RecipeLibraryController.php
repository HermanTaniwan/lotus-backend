<?php

namespace App\Controllers;

use DateInterval;
use Datetime;

class RecipeLibraryController extends BaseController
{
    public function loadLibrary(): string
    {
        $request = \Config\Services::request();
        $artist_id = $request->getGet('artist_id');
        if ($artist_id == "") $artist_id = 1;

        /**CARI NAMA ARTIS */
        $db = \Config\Database::connect();
        $builder = $db->table('artist');
        $builder->select('*');
        $builder->where(['id' => $artist_id]);
        $results = $builder->get()->getFirstRow();
        $data['artist'] = $results;

        /**DAPATIN SEMUA NAMA ARTIS & JUMLAH VIDEO YANG PUBLISH*/
        // SELECT artist.*, count(recipe.artist_id) as count_published_video FROM artist LEFT JOIN recipe ON artist.id = recipe.artist_id GROUP BY artist.id;
        $db = \Config\Database::connect();
        $builder = $db->table('artist');
        $builder->select('artist.*,count(recipe.artist_id) as count_published_video');
        $builder->join('recipe', 'artist.id = recipe.artist_id', 'left');
        $builder->groupBy('artist.id');
        $results = $builder->get()->getResult();
        $data['all_artist'] = $results;


        /**CARI TOTAL ROW */
        $pager = service('pager');
        $builder = $db->table('youtube');
        $builder->select('*');
        $builder->where(['artist_id' => $artist_id]);
        $builder->where('description !=""', NULL, FALSE);
        $builder->where('content_duration >time("00:02:00")', NULL, FALSE);
        $total   = $builder->countAllResults(false);
        $page    = (int) ($this->request->getGet('page') ?? 1);
        $perPage = 10;
        $pager_links = $pager->makeLinks($page, $perPage, $total, "bootstrap_pagination");
        $data['pager_links'] = $pager_links;


        /**AMBIL SEMUA KONTEN YANG DURASINYA TIDAK KOSONG DAN DURASI KONTEN LEBIH DARI 1 MENIT */
        $db = \Config\Database::connect();
        $builder = $db->table('youtube');
        $builder->select('youtube.*,IF(recipe.video_id IS NULL,"no","yes")  as "published"');
        $builder->join('recipe', 'recipe.video_id = youtube.video_id', 'left');
        $builder->where(['youtube.artist_id' => $artist_id]);
        // $builder->where('description !=""', NULL, FALSE);
        // $builder->where('content_duration >time("00:02:00")', NULL, FALSE);

        $result = $builder->get($perPage, ($page - 1) * $perPage)->getResult();
        $data['result'] = $result;

        return view('recipe_library', $data);
    }

    public function loadEditor()
    {

        $request = \Config\Services::request();
        $video_id = $request->getGet('video_id');
        if ($video_id == "") exit('NO VIDEO ID');


        $db = \Config\Database::connect();
        $builder = $db->table('youtube');
        $builder->select('*');
        $builder->where(['video_id' => $video_id]);
        $result = $builder->get()->getFirstRow();
        $data['original'] = $result;

        $db = \Config\Database::connect();
        $builder = $db->table('recipe');
        $builder->select('*');
        $builder->where(['video_id' => $video_id]);
        $result = $builder->get()->getFirstRow();

        $data['published'] = new \stdClass;
        if ($result == null) {
            $data['published']->name = $data['original']->title;
            $data['published']->instructions = $data['original']->description;
            $data['published']->aspect_ratio = '';
            $data['published']->preparation = '';
            $data['published']->tags = '';
            $data['published']->ingredients = '';
            $data['published']->video_id = $video_id;
            $data['published']->artist_id = $data['original']->artist_id;
        } else {
            $data['published'] = $result;
        }

        $session = \Config\Services::session();
        //$session->setFlashdata('submit-success', 'false');
        return view('recipe_editor', $data);
    }

    public function submitRecipe()
    {

        $request = \Config\Services::request();
        $submitted = $request->getPost();

        $sql = 'INSERT INTO recipe (video_id, name, instructions, aspect_ratio, tags,ingredients,artist_id)
        VALUES (?, ?, ?, ?, ?, ? ,?)
        ON DUPLICATE KEY UPDATE 
            video_id=VALUES(video_id),
            name=VALUES(name), 
            instructions=VALUES(instructions), 
            aspect_ratio=VALUES(aspect_ratio), 
            tags=VALUES(tags),
            ingredients=VALUES(ingredients),
            artist_id=VALUE(artist_id)';

        $db = \Config\Database::connect();
        $query = $db->query($sql, array(
            $submitted['video_id'],
            $submitted['name'],
            $submitted['instructions'],
            $submitted['aspect_ratio'],
            $submitted['tags'],
            $submitted['ingredients'],
            $submitted['artist_id'],

        ));

        $session = \Config\Services::session();
        $session->setFlashdata('submit-success', 'true');
        return redirect()->to('recipe-editor?video_id=' . $submitted['video_id']);
    }



    public function updateRecipe()
    {
        try {
            $request = \Config\Services::request();
            $submitted = $request->getPost();


            $db = \Config\Database::connect();
            $builder = $db->table('recipe');
            $builder->set($submitted['data']['category'], $submitted['data']['value']);
            $builder->where('id', $submitted['id']);
            $builder->update();
            $response_array['status'] = 'success';
        } catch (\Exception $e) {
            header('Content-type: application/json');
            $response_array['status'] = 'error';
            $response_array['message'] = $e->getMessage();
        }
        header('Content-type: application/json');
        echo json_encode($response_array);
    }

    public function submitRecipeYT()
    {
        $request = \Config\Services::request();
        $submitted = $request->getPost();

        $sql = 'INSERT INTO youtube (video_id, title, description)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            video_id=VALUES(video_id),
            title=VALUES(title), 
            description=VALUES(description)';

        $db = \Config\Database::connect();
        $query = $db->query($sql, array(
            $submitted['video_id'],
            $submitted['title'],
            $submitted['description'],
        ));

        $session = \Config\Services::session();
        $session->setFlashdata('submit-success', 'true');
        return redirect()->to('recipe-editor?video_id=' . $submitted['video_id']);
    }

    public function getIngredient()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('ingredients');
        $builder->select('*');
        $result = $builder->get()->getResult();
        echo json_encode($result);
        exit();
    }

    public function getRecipeTags()
    {
        $country = ['Indonesia', 'Chinese', 'Jepang', 'Korea', 'Barat', 'India', 'Thai'];
        $types = ['Goreng', 'Rebus', 'Bakar'];
        $olahan = ['Ayam', 'Sapi', 'Kambing', 'Seafood', 'Sayur', 'Nasi', 'Mie'];
        echo json_encode(array_merge($country, $types, $olahan));
        exit();
    }

    public function getPublishedRecipe()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('recipe');
        $builder->select('recipe.*, youtube.title, artist.name as artist, CONCAT(LEFT(youtube.description, 40),"...") as description, youtube.video_id as yt_video_id');
        $builder->join('youtube', 'recipe.video_id = youtube.video_id', 'left');
        $builder->join('artist', 'recipe.artist_id = artist.id', 'left');
        $builder->orderBy('timestamp', 'DESC');
        $result = $builder->get()->getResult();
        $data['result'] = $result;
        return view('recipe_published', $data);
    }

    public function loadTikTok()
    {
        return view('tiktok_template');
    }
}
