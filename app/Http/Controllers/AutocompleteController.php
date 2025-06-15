<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AutocompleteController extends Controller
{
    public function suggest(Request $request)
    {
        $query = $request->query('query');

        if (!$query) {
            return response()->json([]);
        }

        try {
            $prompt = "Suggest up to 5 common names of plants or animals that start with \"$query\". Reply only with a comma-separated list.";
            $geminiApiKey = env('GEMINI_API_KEY');

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=$geminiApiKey", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ]
            ]);

            $output = $response->json();
            $text = $output['candidates'][0]['content']['parts'][0]['text'] ?? '';

            $suggestions = collect(explode(',', $text))
                ->map(fn($s) => trim($s))
                ->filter()
                ->values();

            return response()->json($suggestions);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch suggestions'], 500);
        }
    }
}
