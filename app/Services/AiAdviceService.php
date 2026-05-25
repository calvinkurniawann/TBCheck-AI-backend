<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AiAdviceService
{
    public function generateAdvice(string $riskLevel, array $symptoms): string
    {
        $prompt = "Sistem pakar TBC mendeteksi risiko " . $riskLevel . " dengan gejala: " . implode(", ", $symptoms) . ". Berikan satu paragraf pendek maksimal 3 kalimat berisi saran medis profesional untuk pasien. Jangan berikan diagnosis pasti. Tolong gunakan bahasa Indonesia yang baik.";

        $apiKey = env('GROQ_API_KEY');
        $url = 'https://api.groq.com/openai/v1/chat/completions';

        try {
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])->post($url, [
                    'model' => 'llama-3.1-8b-instant',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Anda adalah asisten medis profesional.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'temperature' => 0.5,
                    'max_tokens' => 150
                ]);

            if ($response->successful()) {
                $text = $response->json('choices.0.message.content');
                if ($text) {
                    return trim($text);
                }
            }
            
            return "ERROR GROQ: " . $response->body();
            
        } catch (\Exception $e) {
            return "ERROR SERVER: " . $e->getMessage();
        }
    }
}