<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatbotSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ChatbotSettingController extends Controller
{
    /**
     * Daftar setting yang dikelola via UI. Sumber kebenaran tunggal.
     *
     * @var list<array{key:string, label:string, group:string, description:string, type:string, sensitive?:bool}>
     */
    private const KNOWN_SETTINGS = [
        ['key' => 'hospital_name', 'label' => 'Nama Rumah Sakit', 'group' => 'general', 'description' => 'Ditampilkan di prompt sistem.', 'type' => 'text'],
        ['key' => 'disclaimer', 'label' => 'Disclaimer', 'group' => 'safety', 'description' => 'Disisipkan ke jawaban yang menyangkut topik medis.', 'type' => 'textarea'],
        ['key' => 'fallback_message', 'label' => 'Fallback Message', 'group' => 'safety', 'description' => 'Pesan ketika layanan AI gagal.', 'type' => 'textarea'],
        ['key' => 'emergency_message', 'label' => 'Emergency Message', 'group' => 'safety', 'description' => 'Pesan untuk pertanyaan darurat (IGD).', 'type' => 'textarea'],
        ['key' => 'medical_advice_message', 'label' => 'Medical Advice Message', 'group' => 'safety', 'description' => 'Pesan untuk pertanyaan diagnosis/obat/gejala.', 'type' => 'textarea'],
        ['key' => 'handoff_message', 'label' => 'Handoff Message', 'group' => 'general', 'description' => 'Pesan ketika user minta disambungkan ke petugas/CS.', 'type' => 'textarea'],
        ['key' => 'welcome_message', 'label' => 'Welcome Message', 'group' => 'general', 'description' => 'Sapaan pembuka chatbot.', 'type' => 'textarea'],

        // OpenRouter / LLM
        ['key' => 'openrouter_api_key', 'label' => 'API Key', 'group' => 'openrouter', 'description' => 'OpenRouter API key. Kosongkan untuk pakai nilai dari .env.', 'type' => 'password', 'sensitive' => true],
        ['key' => 'openrouter_model', 'label' => 'Model', 'group' => 'openrouter', 'description' => 'ID model OpenRouter (contoh: openai/gpt-4o-mini).', 'type' => 'text'],
        ['key' => 'openrouter_temperature', 'label' => 'Temperature', 'group' => 'openrouter', 'description' => 'Nilai 0.0 - 2.0. Semakin tinggi semakin kreatif.', 'type' => 'text'],
        ['key' => 'openrouter_max_tokens', 'label' => 'Max Tokens', 'group' => 'openrouter', 'description' => 'Maksimum token respons (1-4096).', 'type' => 'text'],
        ['key' => 'openrouter_timeout', 'label' => 'Timeout (detik)', 'group' => 'openrouter', 'description' => 'Timeout request HTTP dalam detik.', 'type' => 'text'],
    ];

    public function edit(): Response
    {
        $values = ChatbotSetting::cached();

        $items = array_map(static fn (array $def): array => [
            ...$def,
            'value' => $values->get($def['key'], ''),
        ], self::KNOWN_SETTINGS);

        $modelInfo = [
            'model' => $this->modelValue('openrouter_model', config('openrouter.model')),
            'temperature' => $this->modelValue('openrouter_temperature', (string) config('openrouter.temperature')),
            'configured' => filled($this->modelValue('openrouter_api_key', config('openrouter.api_key'))),
        ];

        return Inertia::render('admin/settings/Index', [
            'settings' => $items,
            'modelInfo' => $modelInfo,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $known = collect(self::KNOWN_SETTINGS)->pluck('key')->all();

        $data = $request->validate([
            'settings' => ['required', 'array'],
            'settings.*' => ['nullable', 'string', 'max:5000'],
        ]);

        foreach ($data['settings'] as $key => $value) {
            if (! in_array($key, $known, true)) {
                continue;
            }

            $stringValue = is_string($value) ? trim($value) : null;

            // Skip API key update if empty (preserve existing value)
            if ($key === 'openrouter_api_key' && ($stringValue === null || $stringValue === '')) {
                continue;
            }

            ChatbotSetting::set((string) $key, $stringValue);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Pengaturan disimpan.']);

        return back();
    }

    private function modelValue(string $settingKey, ?string $default = null): ?string
    {
        $dbValue = ChatbotSetting::get($settingKey);

        if ($dbValue !== null && $dbValue !== '') {
            return $dbValue;
        }

        return $default;
    }
}
