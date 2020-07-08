<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Library\TwitterAPIExchange;

class TwitterController extends Controller
{
    public function tweets()
    {
        $settings = array(
            'oauth_access_token' => "1691393953-3B8Bxi6V5aB1SwzoC4ChEQv73PXlbkas8qFsmdu",
            'oauth_access_token_secret' => "hkOkG54dmakYIRB9kwWp1NjFn5dGPD2NLJyjsUzVY3w10",
            'consumer_key' => "drfXqP9gvsXwSrXR1Y8Okn85D",
            'consumer_secret' => "eKILUC85058IbmEcH4HkLrbYrmHOOUDcOtwyJThhZfQ7vJAbGl"
        );
        $url = "https://api.twitter.com/1.1/statuses/user_timeline.json";
        $requestMethod = "GET";
        if (isset($_GET['user'])) {
            $user = $_GET['user'];
        } else {
            $user = "FvisLtd";
        }
        if (isset($_GET['count'])) {
            $count = $_GET['count'];
        } else {
            $count = 5;
        }
        // $getfield = "?screen_name=$user&count=$count&tweet_mode=extended";
        $getfield = "?screen_name=$user&count=$count";
        $twitter = new TwitterAPIExchange($settings);
        $string = json_decode($twitter->setGetfield($getfield)
            ->buildOauth($url, $requestMethod)
            ->performRequest(), $assoc = TRUE);
        
        return response()->json([
                'data' => $string
            ]);
    }
}
