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

    public function generateResponse($message, $provider, $userName = null)
    {
        $bot = $this->ispBots[$provider] ?? null;
        
        if (!$this->apiKey) {
            Log::error('Gemini API key not configured');
            return [
                'message' => "I'm currently undergoing maintenance. Please try again in a few moments.",
                'type' => 'text'
            ];
        }
        
        // Create a context that includes the bot's identity and user's name
        $context = "You are {$bot['name']} ({$bot['fullName']}), a {$bot['personality']} AI assistant for {$provider}. " .
            ($userName ? "You are speaking with {$userName}. Always address them by name occasionally to make the conversation more personal. " : "") .
            "You specialize in providing customer support for {$provider}'s internet services, plans, and technical issues. " .
            "Always maintain a consistent identity as {$bot['name']} and respond in a {$bot['personality']} manner.";

        try {
            $prompt = $this->buildPrompt($message, $provider, $context, $userName);
            
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
                'message' => $userName 
                    ? "I apologize {$userName}, I'm having a bit of trouble accessing my knowledge base. What specific details would you like to know about {$provider}?"
                    : "I'm having a bit of trouble accessing my knowledge base. What specific details would you like to know about {$provider}?",
                'type' => 'text'
            ];
            
        } catch (\Exception $e) {
            Log::error('Error in Gemini service', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'message' => $userName 
                    ? "I apologize {$userName}, but I'm experiencing a temporary technical issue. Could you please rephrase your question?"
                    : "I apologize for the inconvenience, but I'm experiencing a temporary technical issue. Could you please rephrase your question?",
                'type' => 'text'
            ];
        }
    }

    protected function buildPrompt($message, $provider, $context, $userName = null)
    {
        $bot = $this->ispBots[$provider];
        $providerInfo = $this->ispData[$provider] ?? [];
        
        // Enhanced personalization instructions
        $basePrompt = "Instructions: You are {$bot['name']} ({$bot['fullName']}), a {$bot['personality']} AI assistant for {$provider}.\n\n";
        
        if ($userName) {
            $basePrompt .= "Important: You are speaking with {$userName}. Make your responses personal by:\n";
            $basePrompt .= "1. Using their name naturally in greetings\n";
            $basePrompt .= "2. Occasionally mentioning their name mid-conversation to maintain engagement\n";
            $basePrompt .= "3. Using their name when providing specific recommendations or important information\n";
            $basePrompt .= "4. Always maintain a friendly yet professional tone\n\n";
        }
        
        $basePrompt .= "Always respond in first person as {$bot['name']}. Never mention that you are an AI or assistant directly.\n\n";

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
        $basePrompt .= "4. Use natural, conversational language\n";
        if ($userName) {
            $basePrompt .= "5. Address {$userName} by name in a natural way\n";
            $basePrompt .= "6. Make the response personal but maintain professionalism\n";
        }
        $basePrompt .= "\nYour Response:";

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
        
        // Remove any AI/Assistant prefixes
        $responseText = preg_replace('/^(AI|Assistant|Bot):\s*/', '', $responseText);
        
        // Ensure the response doesn't start with the bot's name if it's already included
        $responseText = preg_replace('/^([A-Za-z\s]+):\s*/', '', $responseText);
        
        return [
            'message' => $responseText,
            'type' => 'text',
            'success' => true
        ];
    }
} 