<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SpeciFindController extends Controller
{
    public function findScientificName(Request $request)
    {
        $request->validate([
            'organism' => 'required|string|max:255',
        ]);

        $organism = $request->input('organism');

        try {
            $geminiApiKey = env('GEMINI_API_KEY');
            $prompt = "Give the scientific name, family, and a short description of the organism '{$organism}'. Reply in this JSON format:\n{\n  \"scientificName\": \"\",\n  \"family\": \"\",\n  \"description\": \"\"\n}";


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

            if ($response->failed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to get response from Gemini API.',
                ]);
            }

            $data = $response->json();

            $generatedText = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

            if (!$generatedText) {
                return response()->json([
                    'success' => false,
                    'message' => 'No scientific information returned by Gemini API.',
                ]);
            }

            return response()->json([
                'success' => true,
                'result' => $generatedText,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Exception: ' . $e->getMessage(),
            ]);
        }
    }
}
