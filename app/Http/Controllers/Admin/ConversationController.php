<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatConversation;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ConversationController extends Controller
{
    public function index(Request $request): Response
    {
        $conversations = ChatConversation::query()
            ->withCount('messages')
            ->when($request->boolean('handed_off'), fn ($q) => $q->where('handed_off', true))
            ->when($request->string('q')->toString(), function ($q, string $term): void {
                $q->where(function ($inner) use ($term): void {
                    $inner->where('session_id', 'ILIKE', "%{$term}%")
                        ->orWhere('user_identifier', 'ILIKE', "%{$term}%");
                });
            })
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('admin/conversations/Index', [
            'conversations' => $conversations,
            'filters' => [
                'q' => $request->string('q')->toString(),
                'handed_off' => $request->boolean('handed_off'),
            ],
        ]);
    }

    public function show(ChatConversation $conversation): Response
    {
        $conversation->load(['messages' => fn ($q) => $q->orderBy('id')]);

        return Inertia::render('admin/conversations/Show', [
            'conversation' => $conversation,
        ]);
    }
}
