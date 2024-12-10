<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $jsonPath = storage_path('app/public/database.json');
        if (!file_exists($jsonPath)) {
            return response()->json(['error' => 'File JSON tidak ditemukan'], 404);
        }
        $json = file_get_contents($jsonPath);
        $data = json_decode($json, true);
        $keyword = strtolower(trim($request->input('keyword')));
        $results = [];

        foreach ($data as $letter => $entries) {
            foreach ($entries as $word => $details) {
                foreach ($details as $sentence => $content) {
                    // Match keyword without stemming
                    if (preg_match("/" . preg_quote($keyword, '/') . "/i", strtolower($sentence))) {
                        $results[] = [
                            'title' => $sentence,
                            'description' => $content['teks'],
                            'foto' => $content['foto'],
                            'video' => $content['video']
                        ];
                    }
                }
            }
        }

        return response()->json($results);
    }

    // This will handle the search suggestions request
    public function suggestions(Request $request)
    {
        $jsonPath = storage_path('app/public/database.json');
        if (!file_exists($jsonPath)) {
            return response()->json(['error' => 'File JSON tidak ditemukan'], 404);
        }
        $json = file_get_contents($jsonPath);
        $data = json_decode($json, true);
        $keyword = strtolower(trim($request->input('keyword')));
        $suggestions = [];

        foreach ($data as $letter => $entries) {
            foreach ($entries as $word => $details) {
                // Match keyword for suggestions
                if (preg_match("/^" . preg_quote($keyword, '/') . "/i", $word)) {
                    $suggestions[] = ['title' => $word];
                }
            }
        }

        return response()->json($suggestions);
    }
}

