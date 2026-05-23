<?php

declare(strict_types=1);

namespace App\Services\OpenRouter;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Factory as Http;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class OpenRouterService
{
    public function __construct(
        private readonly Http $http,
    ) {}

    /**
     * Kirim chat completion ke OpenRouter dan kembalikan teks jawaban + metadata.
     *
     * @param  list<array{role:string, content:string}>  $messages
     * @return array{content:string, model:string, usage:array<string, mixed>, raw:array<string, mixed>}
     *
     * @throws RuntimeException
     */
    public function chat(array $messages): array
    {
        $apiKey = (string) config('openrouter.api_key', '');

        if ($apiKey === '') {
            throw new RuntimeException('OPENROUTER_API_KEY belum diset. Lengkapi .env Anda.');
        }

        $model = (string) config('openrouter.model');

        $payload = [
            'model' => $model,
            'messages' => $messages,
            'temperature' => (float) config('openrouter.temperature', 0.3),
            'max_tokens' => (int) config('openrouter.max_tokens', 800),
        ];

        try {
            $response = $this->http
                ->withToken($apiKey)
                ->withHeaders([
                    'HTTP-Referer' => (string) config('openrouter.http_referer'),
                    'X-Title' => (string) config('openrouter.site_title'),
                    'Content-Type' => 'application/json',
                ])
                ->timeout((int) config('openrouter.timeout', 30))
                ->acceptJson()
                ->post(rtrim((string) config('openrouter.base_url'), '/').'/chat/completions', $payload)
                ->throw();
        } catch (ConnectionException $e) {
            Log::warning('OpenRouter connection failed', ['error' => $e->getMessage()]);

            throw new RuntimeException('Tidak dapat menghubungi layanan AI. Coba lagi sebentar.', 0, $e);
        } catch (RequestException $e) {
            Log::warning('OpenRouter returned error', [
                'status' => $e->response?->status(),
                'body' => $e->response?->body(),
            ]);

            throw new RuntimeException('Layanan AI mengembalikan error. Silakan coba lagi.', 0, $e);
        }

        /** @var array<string, mixed> $data */
        $data = $response->json() ?? [];

        $content = (string) data_get($data, 'choices.0.message.content', '');

        if ($content === '') {
            throw new RuntimeException('Layanan AI mengembalikan respons kosong.');
        }

        return [
            'content' => $this->stripMarkdown(trim($content)),
            'model' => (string) data_get($data, 'model', $model),
            'usage' => (array) data_get($data, 'usage', []),
            'raw' => $data,
        ];
    }

    /**
     * Hilangkan formatting markdown yang tidak ter-render di widget chat.
     * Bersifat best-effort safety net jika LLM masih melanggar instruksi prompt.
     */
    private function stripMarkdown(string $text): string
    {
        // **bold** dan __bold__ → plain
        $text = preg_replace('/\*\*(.+?)\*\*/su', '$1', $text) ?? $text;
        $text = preg_replace('/__(.+?)__/su', '$1', $text) ?? $text;

        // *italic* dan _italic_ (hindari menabrak bullet "* item")
        $text = preg_replace('/(?<![\*\w])\*(?!\s)([^\*\n]+?)\*(?!\w)/su', '$1', $text) ?? $text;
        $text = preg_replace('/(?<![_\w])_(?!\s)([^_\n]+?)_(?!\w)/su', '$1', $text) ?? $text;

        // `inline code`
        $text = preg_replace('/`([^`\n]+)`/su', '$1', $text) ?? $text;

        // Heading "# ", "## ", dst di awal baris
        $text = preg_replace('/^#{1,6}\s+/m', '', $text) ?? $text;

        // Bullet markdown "* " / "- " / "+ " di awal baris → "• "
        $text = preg_replace('/^(\s*)[\*\-\+]\s+/m', '$1• ', $text) ?? $text;

        // [text](url) → "text (url)"
        $text = preg_replace('/\[([^\]]+)\]\(([^)]+)\)/su', '$1 ($2)', $text) ?? $text;

        return $text;
    }
}
