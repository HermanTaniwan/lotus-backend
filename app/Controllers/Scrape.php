<?php

namespace App\Controllers;

use DateInterval;
use Datetime;

class Scrape extends BaseController
{
    public function index(): string
    {
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }

    public function scrapeYT()
    {

        $request = \Config\Services::request();
        $artist_id = $request->getGet('artist_id');
        if ($artist_id == "") exit('NO ARTIST ID');

        $mode = $request->getGet('mode');
        if ($artist_id == "") exit('NO MODE FOUND');

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

        if ($mode != "sync") {
            $builder = $db->table('channel');
            $builder->select('*');
            $builder->where(['artist_id' => $artist_id]);
            $results = $builder->get()->getLastRow();
            $pageToken = $results != null ? $results->next_page_token : '';
        } else {
            $pageToken = '';
        }


        for ($i = 0; $i < $totalPage; $i++) {
            $content = [];
            $channelstr = file_get_contents("https://www.googleapis.com/youtube/v3/search?part=snippet&channelId=$channelID&maxResults=$maxResults&order=date&key=$API_key&fields=nextPageToken,pageInfo,items(id(videoId))&pageToken=$pageToken");


            // $channelstr = file_get_contents("http://localhost/food-recom-cms/asset/json/sample-channel-result.json");
            $channeljson = json_decode($channelstr, true);

            echo 'Total Video: ' . $channeljson['pageInfo']['totalResults'] . "<br>" . "<br>";


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
}
