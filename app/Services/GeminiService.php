<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected $apiKey;
    protected $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent';
    protected $ispData;
    protected $ispBots = [
        'PLDT' => [
            'name' => 'PLDT AINA',
            'fullName' => 'Advanced Intelligent Network Assistant',
            'personality' => 'professional and technically knowledgeable'
        ],
        'Globe' => [
            'name' => 'GlobeGuide',
            'fullName' => 'Your Digital Globe Assistant',
            'personality' => 'friendly and solution-focused'
        ],
        'Converge' => [
            'name' => 'C-Verse',
            'fullName' => 'Your Virtual Converge Assistant',
            'personality' => 'efficient and customer-oriented'
        ]
    ];

    public function __construct(array $ispData = [])
    {
        $this->apiKey = config('services.gemini.api_key');
        $this->ispData = $ispData;
    }

    public function generateResponse($message, $provider)
    {
        $bot = $this->ispBots[$provider] ?? null;
        
        if (!$this->apiKey) {
            Log::error('Gemini API key not configured');
            return [
                'message' => "I'm currently undergoing maintenance. Please try again in a few moments.",
                'type' => 'text'
            ];
        }
        
        // Create a context that includes the bot's identity
        $context = "You are {$bot['name']} ({$bot['fullName']}), a {$bot['personality']} AI assistant for {$provider}. 
        You specialize in providing customer support for {$provider}'s internet services, plans, and technical issues. 
        Always maintain a consistent identity as {$bot['name']} and respond in a {$bot['personality']} manner.";

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
                if (!empty($result['candidates'][0]['content']['parts'][0]['text'])) {
                    return $this->processGeminiResponse($result);
                }
            }
            
            // If we reach here, there was an issue with the response
            Log::error('Gemini API error', [
                'status' => $response->status(),
                'response' => $response->json()
            ]);
            
            return [
                'message' => "I'm having a bit of trouble accessing my knowledge base. Let me help you with some basic information instead. What specific details would you like to know about {$provider}?",
                'type' => 'text'
            ];
            
        } catch (\Exception $e) {
            Log::error('Error in Gemini service', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'message' => "I apologize for the inconvenience. As {$bot['name']}, I aim to provide accurate information, but I'm experiencing a temporary technical issue. Could you please rephrase your question or try again in a moment?",
                'type' => 'text'
            ];
        }
    }

    protected function buildPrompt($message, $provider, $context)
    {
        $bot = $this->ispBots[$provider];
        $providerInfo = $this->ispData[$provider] ?? [];
        
        $basePrompt = "Instructions: You are {$bot['name']} ({$bot['fullName']}), a {$bot['personality']} AI assistant for {$provider}. " .
            "Always respond in first person as {$bot['name']}. Never mention that you are an AI or assistant directly.\n\n";

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
        }

        $basePrompt .= "User Message: {$message}\n\n";
        $basePrompt .= "Response Guidelines:\n";
        $basePrompt .= "1. Be {$bot['personality']}\n";
        $basePrompt .= "2. Focus on {$provider} specific information\n";
        $basePrompt .= "3. Be concise but helpful\n";
        $basePrompt .= "4. Use natural, conversational language\n\n";
        $basePrompt .= "Your Response:";

        return $basePrompt;
    }

    protected function processGeminiResponse($result)
    {
        if (empty($result['candidates'][0]['content']['parts'][0]['text'])) {
            throw new \Exception('Invalid response format from Gemini');
        }

        $responseText = $result['candidates'][0]['content']['parts'][0]['text'];
        
        // Clean up the response text
        $responseText = trim($responseText);
        
        // Remove any existing bot name prefix if it exists
        $responseText = preg_replace('/^(AI|Assistant|Bot):\s*/', '', $responseText);
        
        return [
            'message' => $responseText,
            'type' => 'text',
            'success' => true
        ];
    }
} 