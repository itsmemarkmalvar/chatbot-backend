<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use App\Services\ChatService;

class ChatController extends Controller
{
    protected $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    public function store(Request $request): JsonResponse
    {
        try {
            Log::info('Store method called', ['user_id' => Auth::id()]);

            if (!Auth::check()) {
                Log::warning('Unauthorized access attempt in store method');
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized access'
                ], 401);
            }

            $validated = $request->validate([
                'message' => 'required|string|max:1000',
            ]);

            // TODO: Integrate with AI service for response generation
            $aiResponse = "This is a placeholder response. AI integration pending.";

            $chat = Chat::create([
                'user_id' => Auth::id(),
                'message' => $validated['message'],
                'response' => $aiResponse,
            ]);

            Log::info('Chat message created', ['chat_id' => $chat->id]);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'id' => $chat->id,
                    'message' => $chat->message,
                    'response' => $chat->response,
                    'timestamp' => $chat->created_at,
                ],
                'message' => 'Message sent successfully'
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Validation failed', ['errors' => $e->errors()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Chat store error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Error creating chat message',
                'debug_message' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function history(): JsonResponse
    {
        try {
            Log::info('History method called', ['user_id' => Auth::id()]);

            if (!Auth::check()) {
                Log::warning('Unauthorized access attempt in history method');
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized access'
                ], 401);
            }

            $user_id = Auth::id();
            
            if (!$user_id) {
                Log::warning('User ID not found');
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found'
                ], 404);
            }

            Log::info('Fetching chat history', ['user_id' => $user_id]);

            $chats = Chat::where('user_id', $user_id)
                ->orderBy('created_at', 'desc')
                ->get();

            if ($chats->isEmpty()) {
                Log::info('No chat history found', ['user_id' => $user_id]);
                return response()->json([
                    'status' => 'success',
                    'data' => [],
                    'message' => 'No chat history found'
                ]);
            }

            $formattedChats = $chats->map(function ($chat) {
                return [
                    'id' => $chat->id,
                    'message' => $chat->message,
                    'response' => $chat->response,
                    'timestamp' => $chat->created_at,
                ];
            });

            Log::info('Chat history retrieved', [
                'user_id' => $user_id,
                'count' => $chats->count()
            ]);

            return response()->json([
                'status' => 'success',
                'data' => $formattedChats,
                'message' => 'Chat history retrieved successfully'
            ]);

        } catch (ModelNotFoundException $e) {
            Log::warning('Chat history not found', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Chat history not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Chat history error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Error fetching chat history',
                'debug_message' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function sendMessage(Request $request)
    {
        try {
            Log::info('Received chat request', [
                'user_id' => Auth::id(),
                'provider' => $request->provider,
                'category' => $request->category,
                'message' => $request->message
            ]);

            $request->validate([
                'message' => 'required|string',
                'provider' => 'required|string|in:PLDT,Globe,Converge',
                'category' => 'nullable|string|in:plans,support,billing,faqs'
            ]);

            $chat = $this->chatService->processMessage(
                Auth::id(),
                $request->message,
                $request->provider,
                $request->category ?? null
            );

            Log::info('Chat processed successfully', [
                'chat_id' => $chat->id,
                'type' => $chat->type,
                'metadata' => $chat->metadata
            ]);

            $metadata = $chat->metadata ? json_decode($chat->metadata, true) : null;

            return response()->json([
                'success' => true,
                'message' => $chat->response,
                'type' => $chat->type,
                'content' => $metadata
            ]);
        } catch (\Exception $e) {
            Log::error('Message processing error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to process message',
                'error' => config('app.debug') ? $e->getMessage() : 'An error occurred'
            ], 500);
        }
    }

    public function getHistory(Request $request)
    {
        $chats = Chat::where('user_id', Auth::id())
            ->when($request->provider, function ($query, $provider) {
                return $query->where('provider', $provider);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $chats
        ]);
    }

    public function deleteHistory(Request $request)
    {
        $request->validate([
            'chat_ids' => 'required|array',
            'chat_ids.*' => 'exists:chats,id'
        ]);

        Chat::whereIn('id', $request->chat_ids)
            ->where('user_id', Auth::id())
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Chat history deleted successfully'
        ]);
    }
} 