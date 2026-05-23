<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\Doctor;
use App\Models\Faq;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        $today = now()->startOfDay();
        $sevenDaysAgo = now()->subDays(6)->startOfDay();

        $messagesPerDay = ChatMessage::query()
            ->where('created_at', '>=', $sevenDaysAgo)
            ->selectRaw("to_char(created_at::date, 'YYYY-MM-DD') as day, count(*) as total")
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->map(fn ($row) => ['day' => $row->day, 'total' => (int) $row->total])
            ->all();

        return Inertia::render('admin/Index', [
            'stats' => [
                'totalConversations' => ChatConversation::query()->count(),
                'todayConversations' => ChatConversation::query()->where('started_at', '>=', $today)->count(),
                'totalMessages' => ChatMessage::query()->count(),
                'blockedMessages' => ChatMessage::query()->where('was_blocked', true)->count(),
                'handoffs' => ChatConversation::query()->where('handed_off', true)->count(),
                'totalDoctors' => Doctor::query()->where('is_active', true)->count(),
                'totalFaqs' => Faq::query()->where('is_active', true)->count(),
            ],
            'messagesPerDay' => $messagesPerDay,
        ]);
    }
}
