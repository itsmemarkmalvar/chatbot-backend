<?php

namespace App\Http\Controllers;

use App\Services\ChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    protected $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    protected function getServiceForProvider($provider)
    {
        // Validate provider
        if (!in_array($provider, ['PLDT', 'Globe', 'Converge'])) {
            return null;
        }
        
        return $this->chatService;
    }

    public function chat(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'message' => 'required|string',
                'provider' => 'required|string',
                'userName' => 'nullable|string'
            ]);

            $message = $validatedData['message'];
            $provider = $validatedData['provider'];
            $userName = $validatedData['userName'] ?? null;

            $service = $this->getServiceForProvider($provider);
            if (!$service) {
                return response()->json([
                    'error' => 'Invalid provider specified'
                ], 400);
            }

            $response = $service->generateResponse($message, $provider, $userName);

            return response()->json($response);

        } catch (\Exception $e) {
            Log::error('Chat error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'An error occurred while processing your request'
            ], 500);
        }
    }
} 