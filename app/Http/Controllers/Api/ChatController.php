<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChatMessageRequest;
use App\Services\Chatbot\ChatService;
use Illuminate\Http\JsonResponse;

class ChatController extends Controller
{
    public function __invoke(ChatMessageRequest $request, ChatService $chat): JsonResponse
    {
        $result = $chat->handle([
            'session_id' => $request->validated('session_id'),
            'message' => (string) $request->validated('message'),
            'channel' => (string) ($request->validated('channel') ?? 'web'),
            'user_identifier' => $request->ip(),
        ]);

        return response()->json([
            'data' => $result,
        ]);
    }
}
