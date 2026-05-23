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
     * @var list<string>
     */
    private const EMERGENCY_KEYWORDS = [
        'darurat', 'emergency', 'gawat', 'igd',
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

        // Hasil pemeriksaan
        'hasil lab', 'hasil rontgen', 'hasil usg', 'hasil ct scan',
        'hasil mri', 'hasil tes', 'arti hasil',

        // Kondisi spesifik
        'hamil', 'kehamilan', 'haid', 'menstruasi', 'keguguran',
        'tumor', 'kanker', 'kista', 'diabetes', 'hipertensi',
        'tekanan darah', 'gula darah', 'kolesterol',
    ];

    public function __invoke(string $message): ChatbotIntent
    {
        $normalized = $this->normalize($message);

        if ($this->matchesAny($normalized, self::EMERGENCY_KEYWORDS)) {
            return ChatbotIntent::Emergency;
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
