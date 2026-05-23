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
     * @var list<array{key:string, label:string, group:string, description:string, type:string}>
     */
    private const KNOWN_SETTINGS = [
        ['key' => 'hospital_name', 'label' => 'Nama Rumah Sakit', 'group' => 'general', 'description' => 'Ditampilkan di prompt sistem.', 'type' => 'text'],
        ['key' => 'disclaimer', 'label' => 'Disclaimer', 'group' => 'safety', 'description' => 'Disisipkan ke jawaban yang menyangkut topik medis.', 'type' => 'textarea'],
        ['key' => 'fallback_message', 'label' => 'Fallback Message', 'group' => 'safety', 'description' => 'Pesan ketika layanan AI gagal.', 'type' => 'textarea'],
        ['key' => 'emergency_message', 'label' => 'Emergency Message', 'group' => 'safety', 'description' => 'Pesan untuk pertanyaan darurat (IGD).', 'type' => 'textarea'],
        ['key' => 'medical_advice_message', 'label' => 'Medical Advice Message', 'group' => 'safety', 'description' => 'Pesan untuk pertanyaan diagnosis/obat/gejala.', 'type' => 'textarea'],
        ['key' => 'handoff_message', 'label' => 'Handoff Message', 'group' => 'general', 'description' => 'Pesan ketika user minta disambungkan ke petugas/CS.', 'type' => 'textarea'],
        ['key' => 'welcome_message', 'label' => 'Welcome Message', 'group' => 'general', 'description' => 'Sapaan pembuka chatbot.', 'type' => 'textarea'],
    ];

    public function edit(): Response
    {
        $values = ChatbotSetting::cached();

        $items = array_map(static fn (array $def): array => [
            ...$def,
            'value' => $values->get($def['key'], ''),
        ], self::KNOWN_SETTINGS);

        return Inertia::render('admin/settings/Index', [
            'settings' => $items,
            'modelInfo' => [
                'model' => config('openrouter.model'),
                'temperature' => config('openrouter.temperature'),
                'configured' => filled(config('openrouter.api_key')),
            ],
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
            ChatbotSetting::set((string) $key, is_string($value) ? $value : null);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Pengaturan disimpan.']);

        return back();
    }
}
