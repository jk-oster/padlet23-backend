<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MetatagController extends Controller
{
    public function getMetaData(Request $request, string $url)
    {
        $urlDecoded = base64_decode(urldecode($url));

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $urlDecoded);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $sites_html = curl_exec($ch);
        curl_close($ch);

        if (!$sites_html) {
            return response()->json(['error' => 'Could not fetch content for: ' . $urlDecoded], 404);
        }

        // Load HTML to DOM Object
        $dom = new \DOMDocument();
        @$dom->loadHTML($sites_html);

        $output = [
            'title' => '',
            'image_url' => '',
            'description' => '',
            'url' => $urlDecoded,
        ];

        // Parse DOM to get Title
        $nodes = $dom->getElementsByTagName('title');
        if ($nodes->length > 0) {
            $output['title'] = $nodes->item(0)->nodeValue;
        }
        // Parse DOM to get Meta Description
        $metas = $dom->getElementsByTagName('meta');

        for ($i = 0; $i < $metas->length; $i++) {
            $meta = $metas->item($i);
            if ($meta->getAttribute('name') == 'Description' || $meta->getAttribute('name') == 'description') {
                $output['description'] = $meta->getAttribute('content');
            }
            if ($meta->getAttribute('property') == 'og:image:url') {
                $output['image_url'] = $meta->getAttribute('content');
            } elseif ($meta->getAttribute('property') == 'og:image') {
                $output['image_url'] = $meta->getAttribute('content');
            }
        }

        $images = $dom->getElementsByTagName('img');

        if (!isset($output['image_url']) && $images->length > 0) {
            $output['image_url'] = $images->item(0)->getAttribute('src');
        }

        $output['description'] = preg_replace('/[^\00-\255]+/u', '', $output['description']);

        return response()->json($output, 200);
    }
}
