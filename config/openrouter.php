<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | OpenRouter API Key
    |--------------------------------------------------------------------------
    |
    | Ambil dari https://openrouter.ai/keys. Jangan pernah commit ke git.
    |
    */
    'api_key' => env('OPENROUTER_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Base URL
    |--------------------------------------------------------------------------
    */
    'base_url' => env('OPENROUTER_BASE_URL', 'https://openrouter.ai/api/v1'),

    /*
    |--------------------------------------------------------------------------
    | Model
    |--------------------------------------------------------------------------
    |
    | Daftar lengkap: https://openrouter.ai/models. Bisa diganti dari .env
    | tanpa mengubah kode (mis. anthropic/claude-3.5-sonnet, openai/gpt-4o-mini,
    | google/gemini-2.0-flash-001, meta-llama/llama-3.1-70b-instruct, dst.).
    |
    */
    'model' => env('OPENROUTER_MODEL', 'openai/gpt-4o-mini'),

    /*
    |--------------------------------------------------------------------------
    | Generation Parameters
    |--------------------------------------------------------------------------
    */
    'temperature' => (float) env('OPENROUTER_TEMPERATURE', 0.3),
    'max_tokens' => (int) env('OPENROUTER_MAX_TOKENS', 800),
    'timeout' => (int) env('OPENROUTER_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | Attribution Headers (opsional, dianjurkan oleh OpenRouter)
    |--------------------------------------------------------------------------
    */
    'http_referer' => env('OPENROUTER_HTTP_REFERER', env('APP_URL')),
    'site_title' => env('OPENROUTER_SITE_TITLE', env('APP_NAME', 'Hospital Chatbot')),

    /*
    |--------------------------------------------------------------------------
    | Chatbot Behavior
    |--------------------------------------------------------------------------
    */
    'chatbot' => [
        // Jumlah pesan history terakhir yang dikirim ke LLM.
        'history_window' => (int) env('CHATBOT_HISTORY_WINDOW', 6),

        // Jumlah maksimum FAQ & jadwal yang diinjeksi ke prompt.
        'max_faq_context' => (int) env('CHATBOT_MAX_FAQ_CONTEXT', 5),
        'max_schedule_context' => (int) env('CHATBOT_MAX_SCHEDULE_CONTEXT', 10),
    ],

];
