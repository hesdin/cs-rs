<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ChatbotSetting;
use App\Models\FaqCategory;
use Inertia\Inertia;
use Inertia\Response;

class ChatPageController extends Controller
{
    public function __invoke(): Response
    {
        $hospitalName = ChatbotSetting::get('hospital_name', 'Rumah Sakit Ibnu Sina YW-UMI Makassar');
        $welcomeMessage = ChatbotSetting::get(
            'welcome_message',
            'Assalamualaikum, saya asisten virtual RS Ibnu Sina Makassar. Saya bisa bantu informasi jadwal dokter, pendaftaran, BPJS, dan administrasi rumah sakit. Ada yang bisa saya bantu?'
        );

        $categories = FaqCategory::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['name', 'description'])
            ->map(fn (FaqCategory $c): array => [
                'name' => $c->name,
                'description' => $c->description,
            ])
            ->all();

        $suggestions = [
            ['icon' => 'calendar', 'label' => 'Jadwal dokter spesialis anak hari ini'],
            ['icon' => 'clipboard', 'label' => 'Bagaimana cara mendaftar berobat?'],
            ['icon' => 'shield', 'label' => 'Apakah RS menerima BPJS?'],
            ['icon' => 'wallet', 'label' => 'Berapa biaya konsultasi dokter?'],
            ['icon' => 'bed', 'label' => 'Kelas kamar rawat inap apa saja?'],
            ['icon' => 'file', 'label' => 'Cara meminta surat keterangan sakit'],
        ];

        return Inertia::render('Chat', [
            'hospitalName' => $hospitalName,
            'welcomeMessage' => $welcomeMessage,
            'categories' => $categories,
            'suggestions' => $suggestions,
            'contact' => [
                'phone' => '(0411) 452917',
                'igd' => '(0411) 452-917 ext. 119',
                'whatsapp' => '0822-9000-9119',
            ],
        ]);
    }
}
