<?php

declare(strict_types=1);

use App\Models\ChatbotSetting;
use App\Models\ChatMessage;
use App\Models\Doctor;
use App\Models\Faq;
use App\Models\FaqCategory;
use App\Services\Chatbot\ChatService;
use Database\Seeders\ChatbotSettingSeeder;
use Illuminate\Support\Facades\Http;

beforeEach(function (): void {
    $this->seed(ChatbotSettingSeeder::class);

    $bpjsCategory = FaqCategory::create([
        'name' => 'Pendaftaran',
        'slug' => 'pendaftaran',
        'is_active' => true,
    ]);

    Faq::create([
        'category_id' => $bpjsCategory->id,
        'question' => 'Apakah RS menerima BPJS Kesehatan?',
        'answer' => 'Ya, RS menerima BPJS dengan rujukan faskes 1.',
        'keywords' => ['bpjs'],
        'is_active' => true,
        'priority' => 100,
    ]);

    $doctor = Doctor::create([
        'name' => 'dr. Test Anak, Sp.A',
        'specialization' => 'Anak',
        'polyclinic' => 'Poli Anak',
        'is_active' => true,
    ]);
    $doctor->schedules()->create([
        'day_of_week' => 1,
        'start_time' => '08:00',
        'end_time' => '12:00',
        'room' => 'A-1',
        'is_active' => true,
    ]);
});

it('blocks emergency questions and triggers handoff without calling LLM', function (): void {
    Http::preventStrayRequests();

    /** @var ChatService $service */
    $service = app(ChatService::class);

    $result = $service->handle([
        'session_id' => null,
        'message' => 'Tolong, saya tidak sadarkan diri dan sesak napas',
        'channel' => 'web',
        'user_identifier' => '127.0.0.1',
    ]);

    expect($result['intent'])->toBe('emergency')
        ->and($result['handoff'])->toBeTrue()
        ->and($result['used_llm'])->toBeFalse()
        ->and($result['reply'])->toContain('IGD');

    expect(ChatMessage::where('was_blocked', true)->count())->toBeGreaterThan(0);
});

it('blocks medical advice and returns disclaimer without LLM', function (): void {
    Http::preventStrayRequests();

    $result = app(ChatService::class)->handle([
        'session_id' => null,
        'message' => 'Saya batuk dan demam, obat apa yang harus saya minum?',
        'channel' => 'web',
        'user_identifier' => '127.0.0.1',
    ]);

    expect($result['intent'])->toBe('medical_advice')
        ->and($result['handoff'])->toBeTrue()
        ->and($result['used_llm'])->toBeFalse()
        ->and($result['reply'])->toContain('bukan tenaga medis');
});

it('calls OpenRouter for safe questions and returns LLM reply', function (): void {
    config()->set('openrouter.api_key', 'test-key');
    config()->set('openrouter.model', 'openai/gpt-4o-mini');

    Http::fake([
        'openrouter.ai/*' => Http::response([
            'model' => 'openai/gpt-4o-mini',
            'choices' => [
                ['message' => ['role' => 'assistant', 'content' => 'Untuk daftar BPJS, bawa kartu BPJS dan rujukan faskes 1.']],
            ],
            'usage' => ['prompt_tokens' => 120, 'completion_tokens' => 30],
        ]),
    ]);

    $result = app(ChatService::class)->handle([
        'session_id' => null,
        'message' => 'Bagaimana cara daftar BPJS di rumah sakit?',
        'channel' => 'web',
        'user_identifier' => '127.0.0.1',
    ]);

    expect($result['intent'])->toBe('general')
        ->and($result['handoff'])->toBeFalse()
        ->and($result['used_llm'])->toBeTrue()
        ->and($result['model'])->toBe('openai/gpt-4o-mini')
        ->and($result['reply'])->toContain('BPJS');

    Http::assertSent(function ($request): bool {
        $body = $request->data();

        return str_contains($request->url(), 'chat/completions')
            && $body['model'] === 'openai/gpt-4o-mini'
            && collect($body['messages'])->contains(fn ($m) => $m['role'] === 'system' && str_contains($m['content'], 'asisten customer service'));
    });
});

it('uses fallback message when LLM fails', function (): void {
    config()->set('openrouter.api_key', 'test-key');

    Http::fake([
        'openrouter.ai/*' => Http::response(['error' => 'boom'], 500),
    ]);

    $result = app(ChatService::class)->handle([
        'session_id' => null,
        'message' => 'Berapa nomor telepon RS?',
        'channel' => 'web',
        'user_identifier' => '127.0.0.1',
    ]);

    expect($result['used_llm'])->toBeFalse()
        ->and($result['reply'])->toBe(ChatbotSetting::get('fallback_message'));
});

it('detects handoff request and skips LLM', function (): void {
    Http::preventStrayRequests();

    $result = app(ChatService::class)->handle([
        'session_id' => null,
        'message' => 'Saya mau bicara dengan customer service saja',
        'channel' => 'web',
        'user_identifier' => '127.0.0.1',
    ]);

    expect($result['intent'])->toBe('request_handoff')
        ->and($result['handoff'])->toBeTrue()
        ->and($result['used_llm'])->toBeFalse()
        ->and($result['reply'])->toContain('petugas customer service');
});

it('treats complaints as handoff request', function (): void {
    Http::preventStrayRequests();

    $result = app(ChatService::class)->handle([
        'session_id' => null,
        'message' => 'Saya kecewa dengan pelayanan, mau komplain',
        'channel' => 'web',
        'user_identifier' => '127.0.0.1',
    ]);

    expect($result['intent'])->toBe('request_handoff')
        ->and($result['handoff'])->toBeTrue()
        ->and($result['used_llm'])->toBeFalse();
});

it('injects today and tomorrow date into system prompt', function (): void {
    config()->set('openrouter.api_key', 'test-key');

    Http::fake([
        'openrouter.ai/*' => Http::response([
            'model' => 'openai/gpt-4o-mini',
            'choices' => [['message' => ['content' => 'OK']]],
        ]),
    ]);

    app(ChatService::class)->handle([
        'session_id' => null,
        'message' => 'Jadwal dokter hari ini',
        'channel' => 'web',
        'user_identifier' => '127.0.0.1',
    ]);

    Http::assertSent(function ($request): bool {
        $systemMessages = collect($request->data()['messages'])
            ->where('role', 'system')
            ->pluck('content')
            ->implode("\n");

        return str_contains($systemMessages, 'Hari ini:')
            && str_contains($systemMessages, 'Besok:');
    });
});

it('marks doctor as on leave when current date in range', function (): void {
    $doctor = Doctor::create([
        'name' => 'dr. Test Cuti, Sp.PD',
        'specialization' => 'Penyakit Dalam',
        'polyclinic' => 'Poli PD',
        'is_active' => true,
        'leave_start_date' => now()->subDay()->toDateString(),
        'leave_end_date' => now()->addDays(3)->toDateString(),
        'leave_reason' => 'Cuti tahunan',
    ]);

    expect($doctor->isOnLeave())->toBeTrue();

    $doctor->update([
        'leave_start_date' => now()->addDays(7)->toDateString(),
        'leave_end_date' => now()->addDays(10)->toDateString(),
    ]);

    expect($doctor->fresh()->isOnLeave())->toBeFalse();
});

it('strips markdown formatting from LLM reply', function (): void {
    config()->set('openrouter.api_key', 'test-key');

    Http::fake([
        'openrouter.ai/*' => Http::response([
            'model' => 'openai/gpt-4o-mini',
            'choices' => [[
                'message' => [
                    'content' => "Untuk daftar berobat:\n1. **Online** via website [rsss.example/daftar](http://rsss.example/daftar)\n2. *Aplikasi Mobile* unduh di Play Store\n3. `WhatsApp` 0812-3456-7890\n\nDokumen: __KTP__ dan **kartu BPJS**.\n# Catatan\n* opsi 1\n* opsi 2",
                ],
            ]],
        ]),
    ]);

    $result = app(ChatService::class)->handle([
        'session_id' => null,
        'message' => 'Bagaimana cara daftar berobat?',
        'channel' => 'web',
        'user_identifier' => '127.0.0.1',
    ]);

    expect($result['reply'])
        ->not->toContain('**')
        ->not->toContain('__')
        ->not->toContain('`')
        ->not->toMatch('/\[.+?\]\(.+?\)/')
        ->not->toMatch('/^#\s/m')
        ->toContain('Online via website')
        ->toContain('Aplikasi Mobile unduh di Play Store')
        ->toContain('WhatsApp 0812-3456-7890')
        ->toContain('rsss.example/daftar (http://rsss.example/daftar)')
        ->toContain('• opsi 1');
});

it('exposes POST /api/chat endpoint that returns structured response', function (): void {
    config()->set('openrouter.api_key', 'test-key');

    Http::fake([
        'openrouter.ai/*' => Http::response([
            'model' => 'openai/gpt-4o-mini',
            'choices' => [['message' => ['content' => 'Hai, ada yang bisa saya bantu?']]],
        ]),
    ]);

    $response = $this->postJson('/api/chat', [
        'message' => 'Halo',
    ]);

    $response->assertOk()
        ->assertJsonStructure([
            'data' => ['session_id', 'reply', 'intent', 'handoff', 'used_llm', 'model', 'conversation_id'],
        ]);
});
