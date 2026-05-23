<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\ChatbotSetting;
use Illuminate\Database\Seeder;

class ChatbotSettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            [
                'key' => 'hospital_name',
                'value' => 'Rumah Sakit Ibnu Sina YW-UMI Makassar',
                'group' => 'general',
                'label' => 'Nama Rumah Sakit',
            ],
            [
                'key' => 'welcome_message',
                'value' => 'Assalamualaikum, saya asisten virtual RS Ibnu Sina Makassar. Saya bisa bantu informasi jadwal dokter, pendaftaran, BPJS, dan administrasi rumah sakit. Ada yang bisa saya bantu?',
                'group' => 'general',
                'label' => 'Welcome Message',
            ],
            [
                'key' => 'disclaimer',
                'value' => 'Saya bukan tenaga medis dan tidak dapat memberikan diagnosis, saran obat, atau tindakan medis. Silakan konsultasi langsung dengan dokter kami.',
                'group' => 'safety',
                'label' => 'Disclaimer',
            ],
            [
                'key' => 'fallback_message',
                'value' => 'Mohon maaf, layanan saya sedang gangguan. Silakan coba beberapa saat lagi atau hubungi customer service kami di (0411) 452917.',
                'group' => 'safety',
                'label' => 'Fallback Message',
            ],
            [
                'key' => 'emergency_message',
                'value' => 'Pertanyaan Anda terdengar darurat. Mohon segera hubungi IGD RS Ibnu Sina di (0411) 452-917 ext. 119 atau layanan darurat 119. Jika kondisi mengancam nyawa, datang langsung ke IGD kami di Jl. Urip Sumoharjo Km. 5, Makassar. Saya akan menghubungkan Anda dengan petugas customer service.',
                'group' => 'safety',
                'label' => 'Emergency Message',
            ],
            [
                'key' => 'medical_advice_message',
                'value' => 'Untuk pertanyaan terkait gejala, penyakit, obat, atau hasil pemeriksaan, saya sarankan untuk berkonsultasi langsung dengan dokter kami. Apakah Anda ingin saya bantu menjadwalkan konsultasi atau menampilkan jadwal dokter spesialis?',
                'group' => 'safety',
                'label' => 'Medical Advice Message',
            ],
            [
                'key' => 'handoff_message',
                'value' => "Tentu, saya akan menghubungkan Anda dengan petugas customer service kami.\n\nSilakan hubungi (0411) 452917 (Senin-Sabtu 07.30-21.00) atau tunggu sebentar, percakapan ini akan diteruskan ke petugas.",
                'group' => 'general',
                'label' => 'Handoff Message',
            ],
        ];

        foreach ($defaults as $row) {
            ChatbotSetting::query()->updateOrCreate(
                ['key' => $row['key']],
                $row,
            );
        }
    }
}
