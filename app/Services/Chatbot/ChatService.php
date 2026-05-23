<?php

declare(strict_types=1);

namespace App\Services\Chatbot;

use App\Actions\Chatbot\BuildPrompt;
use App\Actions\Chatbot\DetectSensitiveQuestion;
use App\Actions\Chatbot\RetrieveKnowledge;
use App\Enums\ChatbotIntent;
use App\Models\ChatbotSetting;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Services\OpenRouter\OpenRouterService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class ChatService
{
    public function __construct(
        private readonly DetectSensitiveQuestion $detector,
        private readonly RetrieveKnowledge $retriever,
        private readonly BuildPrompt $promptBuilder,
        private readonly OpenRouterService $openRouter,
    ) {}

    /**
     * Pipeline chatbot:
     *  - Pastikan / buat conversation.
     *  - Simpan pesan user.
     *  - Filter sensitif (rule-based).
     *  - Jika aman → retrieve knowledge → build prompt → call LLM.
     *  - Simpan jawaban assistant.
     *
     * @param  array{session_id:?string, message:string, channel?:string, user_identifier?:?string}  $input
     * @return array{
     *     session_id:string,
     *     reply:string,
     *     intent:string,
     *     handoff:bool,
     *     used_llm:bool,
     *     model:?string,
     *     conversation_id:int
     * }
     */
    public function handle(array $input): array
    {
        $conversation = $this->resolveConversation(
            $input['session_id'] ?? null,
            $input['channel'] ?? 'web',
            $input['user_identifier'] ?? null,
        );

        $userText = trim($input['message']);
        $intent = ($this->detector)($userText);

        $userMessage = $this->logMessage($conversation, [
            'role' => 'user',
            'content' => $userText,
            'intent' => $intent->value,
            'was_blocked' => ! $intent->allowsLlm(),
        ]);

        // 1. Pertanyaan sensitif → JANGAN panggil LLM. Kembalikan template aman.
        if (! $intent->allowsLlm()) {
            $reply = $this->safeReplyFor($intent);

            $this->logMessage($conversation, [
                'role' => 'assistant',
                'content' => $reply,
                'intent' => $intent->value,
                'was_blocked' => true,
                'used_llm' => false,
            ]);

            $conversation->update([
                'last_activity_at' => now(),
                'handed_off' => $intent->requiresHandoff(),
            ]);

            return [
                'session_id' => $conversation->session_id,
                'reply' => $reply,
                'intent' => $intent->value,
                'handoff' => $intent->requiresHandoff(),
                'used_llm' => false,
                'model' => null,
                'conversation_id' => $conversation->id,
            ];
        }

        // 2. Aman → retrieve knowledge & call LLM.
        $context = ($this->retriever)($userText);
        $history = $this->loadHistory($conversation, excludeMessageId: $userMessage->id);
        $messages = ($this->promptBuilder)($userText, $context, $history);

        try {
            $result = $this->openRouter->chat($messages);
            $reply = $result['content'];
            $modelUsed = $result['model'];
            $usedLlm = true;
        } catch (Throwable $e) {
            Log::warning('Chatbot LLM call failed, returning fallback', [
                'error' => $e->getMessage(),
            ]);

            $reply = ChatbotSetting::get(
                'fallback_message',
                'Maaf, layanan saya sedang gangguan. Silakan coba beberapa saat lagi atau hubungi customer service kami.'
            ) ?? '';
            $modelUsed = null;
            $usedLlm = false;
        }

        $this->logMessage($conversation, [
            'role' => 'assistant',
            'content' => $reply,
            'intent' => $intent->value,
            'used_llm' => $usedLlm,
            'model' => $modelUsed,
            'metadata' => [
                'retrieved_faq_ids' => array_column($context['faqs'], 'id'),
                'retrieved_doctor_ids' => array_column($context['doctors'], 'id'),
                'usage' => $result['usage'] ?? null,
            ],
        ]);

        $conversation->update(['last_activity_at' => now()]);

        return [
            'session_id' => $conversation->session_id,
            'reply' => $reply,
            'intent' => $intent->value,
            'handoff' => false,
            'used_llm' => $usedLlm,
            'model' => $modelUsed,
            'conversation_id' => $conversation->id,
        ];
    }

    private function resolveConversation(?string $sessionId, string $channel, ?string $userIdentifier): ChatConversation
    {
        if ($sessionId !== null && $sessionId !== '') {
            $conversation = ChatConversation::query()
                ->where('session_id', $sessionId)
                ->first();

            if ($conversation !== null) {
                return $conversation;
            }
        }

        return ChatConversation::query()->create([
            'channel' => $channel,
            'user_identifier' => $userIdentifier,
        ]);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function logMessage(ChatConversation $conversation, array $attributes): ChatMessage
    {
        return DB::transaction(fn () => $conversation->messages()->create($attributes));
    }

    /**
     * @return Collection<int, ChatMessage>
     */
    private function loadHistory(ChatConversation $conversation, int $excludeMessageId): Collection
    {
        $window = (int) config('openrouter.chatbot.history_window', 6);

        return $conversation->messages()
            ->where('id', '!=', $excludeMessageId)
            ->where('was_blocked', false)
            ->whereIn('role', ['user', 'assistant'])
            ->orderByDesc('id')
            ->limit($window)
            ->get()
            ->reverse()
            ->values();
    }

    private function safeReplyFor(ChatbotIntent $intent): string
    {
        $disclaimer = ChatbotSetting::get(
            'disclaimer',
            'Saya bukan tenaga medis dan tidak dapat memberikan diagnosis, saran obat, atau tindakan medis.'
        );

        return match ($intent) {
            ChatbotIntent::Emergency => ChatbotSetting::get(
                'emergency_message',
                "Pertanyaan Anda terdengar darurat. Mohon segera hubungi IGD kami atau layanan darurat 119.\n\n".
                'Jika kondisi mengancam nyawa, datang langsung ke IGD terdekat. Saya akan menghubungkan Anda dengan petugas customer service.'
            ) ?? '',
            ChatbotIntent::MedicalAdvice => "{$disclaimer}\n\n".(ChatbotSetting::get(
                'medical_advice_message',
                'Untuk pertanyaan terkait gejala, penyakit, obat, atau hasil pemeriksaan, saya sarankan untuk berkonsultasi langsung dengan dokter kami. Apakah Anda ingin saya bantu menjadwalkan konsultasi?'
            ) ?? ''),
            ChatbotIntent::RequestHandoff => ChatbotSetting::get(
                'handoff_message',
                "Tentu, saya akan menghubungkan Anda dengan petugas customer service kami.\n\n".
                'Silakan hubungi (021) 555-1234 (Senin-Sabtu 08.00-20.00) atau tunggu sebentar, percakapan ini akan diteruskan ke petugas.'
            ) ?? '',
            default => throw new RuntimeException('safeReplyFor dipanggil untuk intent yang seharusnya boleh ke LLM.'),
        };
    }
}
