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
            Log::info('Chat request received', $request->all());

            $validatedData = $request->validate([
                'message' => 'required|string',
                'provider' => 'required|string',
                'category' => 'nullable|string',
                'userName' => 'nullable|string'
            ]);

            $message = $validatedData['message'];
            $provider = $validatedData['provider'];
            $category = $validatedData['category'] ?? null;
            $userName = $validatedData['userName'] ?? null;

            $service = $this->getServiceForProvider($provider);
            if (!$service) {
                return response()->json([
                    'success' => false,
                    'error' => 'Invalid provider specified'
                ], 400);
            }

            Log::info('Processing request', [
                'provider' => $provider,
                'category' => $category
            ]);

            // Handle category-specific requests
            if ($category === 'plans') {
                $response = $service->handlePlansQuery($provider);
                Log::info('Plans response', $response);
            } else {
                $response = $service->generateResponse($message, $provider, $userName);
            }

            return response()->json([
                'success' => true,
                'type' => $response['type'] ?? 'text',
                'message' => $response['message'] ?? '',
                'content' => $response['content'] ?? null
            ]);

        } catch (\Exception $e) {
            Log::error('Chat error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'An error occurred while processing your request: ' . $e->getMessage()
            ], 500);
        }
    }

    public function sendMessage(Request $request)
    {
        try {
            Log::info('Chat request received', [
                'message' => $request->input('message'),
                'provider' => $request->input('provider'),
                'category' => $request->input('category')
            ]);

            $validatedData = $request->validate([
                'message' => 'required|string',
                'provider' => 'required|string',
                'category' => 'nullable|string',
                'userName' => 'nullable|string'
            ]);

            $service = $this->getServiceForProvider($validatedData['provider']);
            if (!$service) {
                Log::warning('Invalid provider specified', [
                    'provider' => $validatedData['provider']
                ]);
                return response()->json([
                    'success' => false,
                    'error' => 'Invalid provider specified'
                ], 400);
            }

            Log::info('Processing request', [
                'provider' => $validatedData['provider'],
                'category' => $validatedData['category'] ?? 'general'
            ]);

            $response = null;

            // Handle category-specific requests
            if ($validatedData['category'] === 'plans') {
                $response = $service->handlePlansQuery($validatedData['provider']);
                Log::info('Plans response generated', ['response' => $response]);
            } else {
                $response = $service->processMessage(
                    auth()->id(),
                    $validatedData['message'],
                    $validatedData['provider'],
                    $validatedData['category']
                );
            }

            if (!$response) {
                throw new \Exception('No response generated from service');
            }

            return response()->json([
                'success' => true,
                'type' => $response['type'] ?? 'text',
                'message' => $response['message'] ?? '',
                'content' => $response['content'] ?? null
            ]);

        } catch (\Exception $e) {
            Log::error('Chat error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }
} 