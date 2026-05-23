<?php

declare(strict_types=1);

namespace App\Actions\Chatbot;

use App\Models\ChatbotSetting;
use App\Models\ChatMessage;
use Illuminate\Support\Collection;

class BuildPrompt
{
    /**
     * Bangun array messages OpenAI-compatible (system + history + user).
     *
     * @param  array{faqs: array<int, array<string, mixed>>, doctors: array<int, array<string, mixed>>}  $context
     * @param  Collection<int, ChatMessage>  $history
     * @return list<array{role:string, content:string}>
     */
    public function __invoke(string $userMessage, array $context, Collection $history): array
    {
        $messages = [
            ['role' => 'system', 'content' => $this->systemPrompt()],
            ['role' => 'system', 'content' => $this->contextPrompt($context)],
        ];

        foreach ($history as $message) {
            // Hanya pesan yang tidak diblokir & punya role yang valid.
            if ($message->was_blocked) {
                continue;
            }
            if (! in_array($message->role, ['user', 'assistant'], true)) {
                continue;
            }

            $messages[] = [
                'role' => $message->role,
                'content' => $message->content,
            ];
        }

        $messages[] = ['role' => 'user', 'content' => $userMessage];

        return $messages;
    }

    private function systemPrompt(): string
    {
        $hospitalName = ChatbotSetting::get('hospital_name', 'Rumah Sakit Sehat Sentosa');
        $disclaimer = ChatbotSetting::get(
            'disclaimer',
            'Saya bukan tenaga medis dan tidak dapat memberikan diagnosis, saran obat, atau tindakan medis.'
        );

        $now = now();
        $today = $now->locale('id')->isoFormat('dddd, D MMMM YYYY');
        $tomorrow = $now->copy()->addDay()->locale('id')->isoFormat('dddd, D MMMM YYYY');

        return <<<PROMPT
            Kamu adalah asisten customer service virtual untuk {$hospitalName}.

            KONTEKS WAKTU:
            - Hari ini: {$today}
            - Besok: {$tomorrow}
            - Gunakan informasi ini untuk menjawab pertanyaan seperti
              "jadwal hari ini", "besok", "minggu ini", dll.

            ATURAN MUTLAK (tidak boleh dilanggar dalam keadaan apa pun):
            1. JANGAN PERNAH memberikan diagnosis penyakit, saran pengobatan, dosis obat,
               interpretasi hasil pemeriksaan medis (lab/rontgen/USG/dll), atau tindakan medis apa pun.
            2. JANGAN merekomendasikan obat (resep maupun bebas) atau menyebut nama obat
               sebagai saran terapi.
            3. Jika user menanyakan gejala, penyakit, obat, tindakan, atau hasil pemeriksaan,
               tolak dengan sopan dan arahkan untuk konsultasi langsung dengan dokter
               di rumah sakit. Sertakan disclaimer berikut secara natural:
               "{$disclaimer}"
            4. Jika tidak yakin atau pertanyaan di luar cakupan, akui tidak tahu dan tawarkan
               handoff ke petugas customer service manusia.
            5. Jika dokter sedang cuti pada tanggal yang ditanyakan user, sebutkan
               periode cuti dengan jelas dan tawarkan dokter lain di spesialisasi yang sama
               atau tanggal lain di luar periode cuti.

            CAKUPAN YANG BOLEH DIJAWAB:
            - Informasi umum rumah sakit (alamat, jam buka, kontak, fasilitas).
            - Jadwal & spesialisasi dokter, status cuti dokter.
            - Prosedur administrasi: pendaftaran, BPJS, rawat inap, rawat jalan,
              penjaminan asuransi, jam besuk, surat sakit, resume medis, dll.
            - Cara membuat janji temu (appointment) tanpa memilih dokter spesialis tertentu
              berdasarkan keluhan medis user.

            GAYA JAWABAN:
            - Bahasa Indonesia, ramah, ringkas, profesional.
            - Gunakan poin/bullet bila menampilkan jadwal atau langkah.
            - Hanya gunakan informasi dari KONTEKS yang diberikan. Jika tidak ada di konteks,
              katakan "informasi tersebut belum tersedia di sistem kami" dan sarankan
              menghubungi customer service.
            PROMPT;
    }

    /**
     * @param  array{faqs: array<int, array<string, mixed>>, doctors: array<int, array<string, mixed>>}  $context
     */
    private function contextPrompt(array $context): string
    {
        $lines = ['KONTEKS DARI DATABASE RUMAH SAKIT:'];

        if ($context['faqs'] !== []) {
            $lines[] = "\n[FAQ]";
            foreach ($context['faqs'] as $faq) {
                $lines[] = "- ({$faq['category']}) Q: {$faq['question']}";
                $lines[] = "  A: {$faq['answer']}";
            }
        } else {
            $lines[] = "\n[FAQ] (tidak ada FAQ relevan)";
        }

        if ($context['doctors'] !== []) {
            $lines[] = "\n[JADWAL DOKTER]";
            foreach ($context['doctors'] as $doctor) {
                $lines[] = "- {$doctor['name']} | Spesialisasi: {$doctor['specialization']}"
                    .($doctor['polyclinic'] ? " | Poli: {$doctor['polyclinic']}" : '');

                if (! empty($doctor['on_leave'])) {
                    $reason = ! empty($doctor['leave_reason']) ? " ({$doctor['leave_reason']})" : '';
                    $lines[] = "  ⚠ SEDANG CUTI {$doctor['leave_period']}{$reason}";
                } elseif (! empty($doctor['leave_period'])) {
                    $lines[] = "  • Akan cuti: {$doctor['leave_period']}";
                }

                if ($doctor['schedules'] === []) {
                    $lines[] = '  (jadwal belum tersedia)';

                    continue;
                }

                foreach ($doctor['schedules'] as $s) {
                    $room = $s['room'] ? " (Ruang {$s['room']})" : '';
                    $lines[] = "  • {$s['day']} {$s['start']}-{$s['end']}{$room}";
                }
            }
        } else {
            $lines[] = "\n[JADWAL DOKTER] (tidak ada dokter relevan)";
        }

        return implode("\n", $lines);
    }
}
