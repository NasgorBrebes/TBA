<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        // Path ke file JSON
        $jsonPath = storage_path('app/public/database.json');

        // Cek apakah file JSON ada
        if (!file_exists($jsonPath)) {
            return response()->json(['error' => 'File JSON tidak ditemukan'], 404);
        }

        // Baca dan decode JSON
        $json = file_get_contents($jsonPath);
        $data = json_decode($json, true);

        // Ambil kata kunci dari request
        $keyword = strtolower($request->input('keyword'));

        // Array untuk menyimpan hasil pencarian
        $results = [];

        // Pencarian dalam struktur JSON
        foreach ($data[$keyword] as $itemName => $items) {
            foreach ($items as $title => $content) {
                if (
                    strpos(strtolower($title), $keyword) !== false ||
                    strpos(strtolower($content['teks']), $keyword) !== false
                ) {
                    // Tambahkan hasil yang cocok ke array results
                    $results[] = [
                        'title' => $title,
                        'description' => $content['teks'],
                        'foto' => $content['foto'],
                        'video' => $content['video']
                    ];
                }
            }
        }

        // Jika tidak ada hasil, berikan pesan 'tidak ada hasil yang cocok'
        if (empty($results)) {
            return response()->json(['message' => 'Tidak ada hasil yang cocok untuk kata kunci ini.'], 404);
        }

        // Kembalikan hasil dalam format JSON
        return response()->json($results);
    }
}
