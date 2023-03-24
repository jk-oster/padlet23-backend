<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp;

class UnsplashController extends Controller
{
    public function search($searchTerm): \Illuminate\Http\JsonResponse
    {
        $client = new GuzzleHttp\Client();
        $res = $client->get('https://api.unsplash.com/search/photos?query=' . $searchTerm . '&client_id=' . env('UNSPLASH_ACCESS_KEY'));
        if ($res->getStatusCode() === 200) {
            try {
                $unsplashSuggestions = json_decode($res->getBody(), true, 512, JSON_THROW_ON_ERROR);
                return response()->json($unsplashSuggestions, 200);
            } catch (\Exception $e) {
                //
            }
        }
        return response()->json(['error' => 'Something went wrong'], 500);
    }
}
