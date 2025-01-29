<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected $apiKey;
    protected $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent';
    protected $ispData;

    public function __construct(array $ispData = [])
    {
        $this->apiKey = config('services.gemini.api_key');
        $this->ispData = $ispData;
    }

    public function generateResponse($message, $provider, $context = [])
    {
        try {
            $prompt = $this->buildPrompt($message, $provider, $context);
            
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}?key={$this->apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'topK' => 40,
                    'topP' => 0.95,
                    'maxOutputTokens' => 1024,
                ]
            ]);

            if ($response->successful()) {
                $result = $response->json();
                return $this->processGeminiResponse($result);
            } else {
                Log::error('Gemini API error', [
                    'status' => $response->status(),
                    'response' => $response->json()
                ]);
                throw new \Exception('Failed to generate response from Gemini');
            }
        } catch (\Exception $e) {
            Log::error('Error in Gemini service', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    protected function buildPrompt($message, $provider, $context)
    {
        $providerInfo = $this->ispData[$provider] ?? [];
        
        $basePrompt = "You are an AI customer service assistant for {$provider}, a leading Internet Service Provider in the Philippines. " .
            "Provide helpful, accurate, and professional responses based on the following information:\n\n";

        // Add provider-specific information
        if (!empty($providerInfo)) {
            $basePrompt .= "Available Plans:\n";
            foreach ($providerInfo['plans'] as $plan) {
                $speedInfo = '';
                if (isset($plan['speed'])) {
                    $speedInfo = "{$plan['speed']} Mbps";
                } elseif (isset($plan['speed_day']) && isset($plan['speed_night'])) {
                    $speedInfo = "{$plan['speed_day']} Mbps (Day) / {$plan['speed_night']} Mbps (Night)";
                } elseif (isset($plan['speed_peak']) && isset($plan['speed_offpeak'])) {
                    $speedInfo = "{$plan['speed_peak']} Mbps (Peak) / {$plan['speed_offpeak']} Mbps (Off-peak)";
                }
                $basePrompt .= "- {$plan['name']}: {$speedInfo} at ₱{$plan['price']}/month\n";
            }
            $basePrompt .= "\n";

            if (isset($providerInfo['support_topics'])) {
                $basePrompt .= "Support Information:\n";
                foreach ($providerInfo['support_topics'] as $topic => $details) {
                    $basePrompt .= "- {$topic}\n";
                }
                $basePrompt .= "\n";
            }
        }

        // Add context if available
        if (!empty($context)) {
            $basePrompt .= "Context:\n";
            foreach ($context as $key => $value) {
                $basePrompt .= "- {$key}: {$value}\n";
            }
            $basePrompt .= "\n";
        }

        $basePrompt .= "Customer Message: {$message}\n\n";
        $basePrompt .= "Please provide a response that is:\n";
        $basePrompt .= "1. Relevant to the customer's query\n";
        $basePrompt .= "2. Specific to {$provider}'s services and plans\n";
        $basePrompt .= "3. Professional and helpful\n";
        $basePrompt .= "4. Clear and concise\n\n";
        $basePrompt .= "Response:";

        return $basePrompt;
    }

    protected function processGeminiResponse($result)
    {
        if (empty($result['candidates'][0]['content']['parts'][0]['text'])) {
            throw new \Exception('Invalid response format from Gemini');
        }

        $responseText = $result['candidates'][0]['content']['parts'][0]['text'];

        return [
            'message' => $responseText,
            'type' => 'text'
        ];
    }
} 