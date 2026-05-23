<?php

declare(strict_types=1);

namespace App\Actions\Chatbot;

use App\Enums\ChatbotIntent;

/**
 * Rule-based detector untuk pertanyaan medis sensitif.
 *
 * Aturan ini sengaja konservatif. Lebih baik false positive (handoff)
 * daripada bot memberi diagnosis atau saran obat.
 */
class DetectSensitiveQuestion
{
    /**
     * Kata kunci darurat → langsung arahkan ke IGD/119.
     *
     * Catatan: kata "igd" sengaja TIDAK dimasukkan agar pertanyaan info
     * non-darurat seperti "apakah IGD buka 24 jam?" tetap bisa dijawab via FAQ.
     * Kata kunci di bawah sudah cukup spesifik untuk situasi mengancam nyawa.
     *
     * @var list<string>
     */
    private const EMERGENCY_KEYWORDS = [
        'darurat', 'emergency', 'gawat darurat',
        'pingsan', 'tidak sadar', 'tidak sadarkan diri', 'kejang',
        'sesak napas', 'sesak nafas', 'tidak bernapas', 'tidak bernafas',
        'serangan jantung', 'henti jantung', 'nyeri dada hebat',
        'stroke', 'pendarahan hebat', 'pendarahan berat', 'muntah darah',
        'bunuh diri', 'mau mati', 'overdosis', 'keracunan',
        'kecelakaan', 'tertabrak', 'jatuh dari', 'tenggelam',
        'luka bakar parah', 'patah tulang',
    ];

    /**
     * Kata kunci medis yang BUKAN darurat tapi tetap tidak boleh dijawab bot
     * (gejala, diagnosis, obat, tindakan).
     *
     * @var list<string>
     */
    private const MEDICAL_ADVICE_KEYWORDS = [
        // Gejala umum
        'gejala', 'nyeri', 'demam', 'batuk', 'pilek', 'pusing',
        'mual', 'muntah', 'diare', 'sembelit', 'lemas', 'lelah berlebihan',
        'gatal', 'ruam', 'bengkak', 'kram',

        // Permintaan diagnosis
        'apakah saya kena', 'apakah saya menderita', 'apa penyakit',
        'penyakit apa', 'saya kena apa', 'saya sakit apa',
        'diagnosa', 'diagnosis',

        // Permintaan obat / tindakan
        'obat apa', 'obat untuk', 'minum obat', 'rekomendasi obat',
        'dosis', 'resep', 'antibiotik',
        'cara menyembuhkan', 'cara mengobati', 'cara meredakan',
        'pengobatan', 'terapi',

        // Hasil pemeriksaan (interpretasi saja, BUKAN administrasi pengambilan)
        'arti hasil', 'maksud hasil', 'tafsir hasil',
        'apa artinya hasil', 'apa maksud hasil',
        'baca hasil', 'bacakan hasil',
        'hasil saya bagaimana', 'hasil saya normal',

        // Kondisi spesifik
        'hamil', 'kehamilan', 'haid', 'menstruasi', 'keguguran',
        'tumor', 'kanker', 'kista', 'diabetes', 'hipertensi',
        'tekanan darah', 'gula darah', 'kolesterol',
    ];

    /**
     * Frasa yang menandakan user ingin disambungkan ke petugas/CS manusia.
     *
     * @var list<string>
     */
    private const HANDOFF_PHRASES = [
        'sambungkan ke cs', 'sambungkan ke customer service',
        'sambungkan ke petugas', 'sambungkan ke operator',
        'sambungkan ke manusia', 'sambungkan saya',
        'bicara dengan cs', 'bicara dengan customer service',
        'bicara dengan petugas', 'bicara dengan staff',
        'bicara dengan manusia', 'bicara dengan operator',
        'bicara sama cs', 'bicara sama petugas',
        'ngomong sama cs', 'ngomong sama petugas',
        'mau bicara sama orang',
        'live agent', 'human agent',
        'minta cs', 'minta petugas', 'minta operator', 'minta manusia',
        'butuh petugas', 'butuh customer service', 'butuh bantuan manusia',
        'hubungkan ke cs', 'hubungkan ke customer service',
        'hubungkan saya', 'hubungkan ke petugas',
        'tidak mau dengan bot', 'jangan dengan bot', 'jangan ai',
        'saya mau cs', 'saya mau customer service',
        'saya mau petugas', 'saya mau operator',
    ];

    public function __invoke(string $message): ChatbotIntent
    {
        $normalized = $this->normalize($message);

        if ($this->matchesAny($normalized, self::EMERGENCY_KEYWORDS)) {
            return ChatbotIntent::Emergency;
        }

        if ($this->matchesAny($normalized, self::HANDOFF_PHRASES)) {
            return ChatbotIntent::RequestHandoff;
        }

        if ($this->matchesAny($normalized, self::MEDICAL_ADVICE_KEYWORDS)) {
            return ChatbotIntent::MedicalAdvice;
        }

        return ChatbotIntent::General;
    }

    private function normalize(string $message): string
    {
        // Lowercase + collapse whitespace untuk matching yang konsisten.
        return preg_replace('/\s+/u', ' ', mb_strtolower(trim($message))) ?? '';
    }

    /**
     * @param  list<string>  $keywords
     */
    private function matchesAny(string $haystack, array $keywords): bool
    {
        foreach ($keywords as $keyword) {
            // Word-boundary match agar "obat" tidak match "obatan-obatan" yg sah
            // tapi tetap longgar untuk frasa.
            if (str_contains($haystack, $keyword)) {
                return true;
            }
        }

        return false;
    }
}
